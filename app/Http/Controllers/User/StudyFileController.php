<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\StudyFile;
use App\Models\FileShare;
use App\Models\StudyGroup;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class StudyFileController extends Controller
{
    use FileTrait;
    public function index(StudyGroup $studyGroup)
    {
        Gate::authorize('viewAny', [$studyGroup]);
        $files = $studyGroup->studyFiles;

        $data = ['title' => 'Study Group Library Management', 'files' => $files, 'studyGroup' => $studyGroup];
        return view('user.study-groups.file.index', $data);
    }

    public function store(Request $request, StudyGroup $studyGroup)
    {
        Gate::authorize('create', $studyGroup);
        // mimetypes:video/avi,video/mpeg,video/quicktime
        $request->validate([
            'files.*' => 'required|file|max:10240|mimetypes:image/jpeg,image/png,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);

        $uploaded = [];

        if (!$request->hasFile('files')) {
            return redirect()->back()->with('error', 'No files uploaded.');
        }

        foreach ($request->file('files') as $file) {
            $mime = $file->getMimeType();
            $category = $this->getFileCategory($mime);

            //* Encrypt content of the file
            $encryptedContent = Crypt::encrypt($file->getContent($file));

            $fileName = uniqid() . '_' . $file->getClientOriginalName();
            $path = "files/{$category}/{$fileName}";

            // $file->move('files/' . $category, $fileName);
            Storage::put($path, $encryptedContent);

            $uploaded[] = $studyGroup->studyFiles()->create([
                'user_id'       => Auth::id(),
                'original_name' => $file->getClientOriginalName(),
                'storage_path'  => $path,
                'file_type'     => $file->getMimeType(),
                'category'      => $category,
                'size'          => $file->getSize(),
            ]);
        }

        return redirect()->back()->with('success', 'Files uploaded successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function preview(StudyGroup $studyGroup, $id)
    {
        Gate::authorize('view', $studyGroup);

        $file = StudyFile::findOrFail($id);

        $this->authorizeView($file);

        try {
            $decryptedContent = Crypt::decrypt(Storage::get($file->storage_path));
            $mime = Storage::mimeType($file->storage_path) ?? 'application/octet-stream';

            $this->logAudit(fileId: $file->id, action: 'view');

            return response($decryptedContent)->header('Content-Type', $mime);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load file content.');
        }
    }

    public function download(StudyGroup $studyGroup, $id)
    {
        Gate::authorize('view', $studyGroup);

        $file = StudyFile::findOrFail($id);

        $this->authorizeView($file);

        try {
            $decrypted = Crypt::decrypt(Storage::get($file->storage_path));
            $this->logAudit(fileId: $file->id, action: 'download');

            return response($decrypted)
                ->header('Content-Disposition', 'attachment; filename="' . $file->original_name . '"');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load file content.');
        }
    }

    public function rename(Request $request, StudyGroup $studyGroup, $id)
    {
        $file = StudyFile::findOrFail($id);
        Gate::authorize('update', $file);
        $request->validate(['new_name' => 'required|string|max:255']);

        $extension = pathinfo($file->original_name, PATHINFO_EXTENSION);

        if (str_contains($request->new_name, '.')) {
            return redirect()->back()->with('error', 'File name cannot contain ' . '');
        }
        $newName = $request->new_name . '.' . $extension;

        if (StudyFile::where('original_name', $newName)->exists()) {
            return redirect()->back()->with('error', 'A file with this name already exists.');
        }

        $file->original_name = $newName;
        $file->save();

        $this->logAudit(fileId: $file->id, action: 'rename');

        return redirect()->back()->with('success', 'File renamed successfully.');
    }

    public function share(Request $request, StudyGroup $studyGroup, $id)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $file = StudyFile::findOrFail($id);

        $this->authorizeShare($file);



        $userToShareWith = \App\Models\User::where('email', $request->email)->first();

        // Avoid duplicate shares
        if ($file->sharedWith()->where('user_id', $userToShareWith->id)->exists()) {
            return redirect()->back()->with('error', 'File already shared with this user');
        }


        DB::transaction(function () use ($file, $userToShareWith, $request) {
            $file->sharedWith()->attach($userToShareWith->id);

            // Create record of the share
            $created = FileShare::create([
                'study_file_id'   => $file->id,
                'shared_by' => $request->user()->id,
                'shared_to' => $userToShareWith->id,
            ]);

            if ($created) {
                // Optionally notify user
                $userToShareWith->notify(new \App\Notifications\FileSharedNotification($file));

                $this->logAudit(fileId: $file->id, action: 'share');

                return redirect()->back()->with('success', 'File shared successfully');
            }
        });
    }

    public function destroy(Request $request, $group_id, $id)
    {
        $file = StudyFile::findOrFail($id);
        // $this->authorizeModify($file);

        Storage::delete($file->storage_path);
        $file->delete();

        return redirect()->back()->with('success', 'File deleted successfully');
    }


    public function sharedWithMe()
    {
        $title = "Shared with me";
        $sharedFiles = FileShare::with('file', 'sender')
            ->where('shared_to', request()->user()->id)
            ->latest()
            ->get();

        return view('file.shared_with_me', compact('sharedFiles', 'title'));
    }


    public function search(Request $request)
    {
        $title = "Search Results";

        $query = StudyFile::query();

        if ($request->filled('q')) {
            $query->where('original_name', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('type')) {
            $query->where('category', $request->type); // type should be stored in db: 'images', 'documents', etc.
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $results = $query->latest()->paginate(20);

        return view('file.search_results', compact('title', 'results'));
    }


    private function validateSecretCode($file, $inputCode)
    {
        if (!Hash::check($inputCode, $file->secret_code)) {
            return redirect()->back()->withErrors(['secret_code' => 'Invalid secret code.']);
        }
    }


    /**
     * Checks if the current user has the right to view a file.
     * If the file is private and the user is not an admin, a 403 error is thrown.
     *
     * @param \App\Models\File $file
     * @return void
     */
    private function authorizeView($file)
    {
        if ($file->is_private && Auth::user()->role !== 'admin') {
            abort(403, 'Access denied');
        }
    }

    /**
     * Checks if the current user has the right to modify a file.
     * If the user is not an admin, a 403 error is thrown.
     *
     * @param \App\Models\File $file
     * @return void
     */
    private function authorizeModify($file)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $file->user_id !== $user->id) {
            abort(403, 'You are not authorized to modify this file');
        }
    }

    private function authorizeShare($file)
    {
        $studyGroup = $file->group;
        $is_a_member = $studyGroup->members()->where('user_id', Auth::id())->exists();
        if (!$is_a_member) {
            abort(403, 'You are not a member of this study group');
        }
        if (!Auth::user()->email_verified_at) {
            abort(403, 'Only verified users can share files');
        }
    }
}
