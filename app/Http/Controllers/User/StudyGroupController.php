<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\StudyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StudyGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->user()->cannot('viewAny', StudyGroup::class)) {
            abort(403);
        }

        // Fetch study groups that a user is not a member of
        // StudyGroup::whereNotIn('id', request()->user()->studyGroups()->pluck('id')->toArray())->get()
        $availableGroups = StudyGroup::whereDoesntHave('members', function ($query) {
            $query->where('user_id', request()->user()->id);
        })->get();

        // Fetch study groups that a user is a member of
        $myStudyGroups = StudyGroup::whereHas('members', function ($query) {
            $query->where('user_id', request()->user()->id);
        })->get();


        $data = ['title' => 'Study Groups', 'studyGroups' => $availableGroups, 'myStudyGroups' => $myStudyGroups];

        return view('user.study-groups.index', $data);
    }

    public function myStudyGroups()
    {
        if (request()->user()->cannot('viewAny', StudyGroup::class)) {
            abort(403);
        }
        $data = ['title' => 'Study Groups', 'studyGroups' => request()->user()->studyGroups()->with('members')->get()];

        return view('user.study-groups.my-groups', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    private function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('create', StudyGroup::class)) {
            abort(403);
        }
        $request->validate(
            [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
            ]
        );

        $group = StudyGroup::create([
            'name' => $request->name,
            'creator_id' => $request->user()->id,
            'description' => $request->description,
        ]);

        if (!$group) {
            return redirect()->back()->with('error', 'Failed to create study group');
        }

        $group->members()->attach($request->user()->id);

        return redirect(route('user.study-groups.index'))->with('success', 'Study group created and joined successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(StudyGroup $studyGroup)
    {
        Gate::authorize('view', $studyGroup);
        $studyGroup->load('members', 'studySessions', 'studyFiles', 'messages');

        $data = [
            'title' => "Study Group - $studyGroup->name",
            'group' => $studyGroup,
        ];

        return view('user.study-groups.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudyGroup $studyGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudyGroup $studyGroup)
    {
        Gate::authorize('update', $studyGroup);
        $request->validate(
            [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
            ]
        );

        $studyGroup->update($request->all());

        return redirect(route('user.study-groups.index'))->with('success', 'Study group updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudyGroup $studyGroup)
    {
        Gate::authorize('delete', $studyGroup);
        $studyGroup->delete();

        return redirect(route('user.study-groups.index'))->with('success', 'Study group deleted successfully');
    }

    public function join(StudyGroup $studyGroup)
    {
        Gate::authorize('join', $studyGroup);
        $studyGroup->members()->attach(request()->user()->id);

        // Send Notification to group creator
        $studyGroup->creator->notify(new \App\Notifications\GroupJoinNotification(request()->user(), $studyGroup));

        return redirect(route('user.study-groups.index'))->with('success', 'Joined study group successfully');
    }

    public function leave(StudyGroup $studyGroup)
    {
        Gate::authorize('leave', $studyGroup);
        $studyGroup->members()->detach(request()->user()->id);

        return redirect(route('user.study-groups.index'))->with('success', 'Left study group successfully');
    }
}
