<?php

namespace App\Http\Controllers\User;

use App\Models\StudyGroup;
use App\Models\ChatMessage;
use App\Models\StudySession;
use Illuminate\Http\Request;
use App\Models\SessionChatMessage;
use App\Events\NewGroupMessageEvent;
use App\Http\Controllers\Controller;
use App\Events\NewSessionMessageEvent;

class ChatController extends Controller
{
    public function show(StudyGroup $studyGroup)
    {
        // Ensure user is a member
        abort_unless($studyGroup->members->contains(request()->user()->id), 403);

        $title = 'Chat Room - ' . $studyGroup->name;
        $chatPage = true;

        $messages = $studyGroup->messages()->with('user')->latest()->take(50)->get()->reverse();

        return view('user.study-groups.chat.index', compact('title', 'studyGroup', 'messages', 'chatPage'));
    }

    public function send(Request $request, StudyGroup $studyGroup)
    {
        $request->validate([
            'message' => 'nullable|string|max:1000',
            'file' => 'nullable|file|max:10240', // max 10MB
        ]);

        if (!$studyGroup->members->contains($request->user()->id)) {
            return response()->json(['error' => 'Not a group member'], 403);
        }

        if (empty($request->message) && empty($request->file)) {
            return response()->json(['error' => 'No message or file provided'], 422);
        }

        $filePath = null;
        $file = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('group_chat_files', 'public');
        }

        $message = ChatMessage::create([
            'study_group_id'    => $studyGroup->id,
            'user_id'           => $request->user()->id,
            'message'           => $request->message,
            'has_file'          => !empty($filePath) ? true : false,
            'file_path'         => $filePath,
            'file_name'         => $filePath ? $file->getClientOriginalName() : null
        ]);

        broadcast(new NewGroupMessageEvent($message))->toOthers();

        return response()->json([
            'sender_id'     => $request->user()->id,
            'username'      => $request->user()->username,
            'message'       => $message->message,
            'timestamp'     => $message->created_at->format('H:i a'),
            'file_url'      => $filePath ? asset('storage/' . $filePath) : null,
            'file_name'     => $filePath ? $file->getClientOriginalName() : null,
        ]);
    }

    public function showSessionChat(StudySession $studySession)
    {
        // dd(StudySession::where('id', $studySession->id)->exists());
        // Ensure user is a member
        abort_unless($studySession->is_participant() || $studySession->is_creator(), 403);

        $title = 'Chat Room - ' . $studySession->session_title;
        $chatPage = true;

        $messages = $studySession->sessionChatMessages()->with('user')->latest()->take(50)->get()->reverse();

        return view('user.study-groups.sessions.session_chat', compact('title', 'studySession', 'messages', 'chatPage'));
    }

    public function sendChatForStudySession(Request $request, StudySession $studySession)
    {
        $request->validate([
            'message' => 'nullable|string|max:1000',
            'file' => 'nullable|file|max:10240', // max 10MB
        ]);

        if (!$studySession->is_participant()) {
            return response()->json(['error' => 'Not a group member'], 403);
        }

        if (empty($request->message) && empty($request->file)) {
            return response()->json(['error' => 'No message or file provided'], 422);
        }

        $filePath = null;
        $file = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('group_chat_files', 'public');
        }

        $message = SessionChatMessage::create([
            'study_session_id'    => $studySession->id,
            'user_id'           => $request->user()->id,
            'message'           => $request->message,
            'has_file'          => !empty($filePath) ? true : false,
            'file_path'         => $filePath,
            'file_name'         => $filePath ? $file->getClientOriginalName() : null,
        ]);

        broadcast(new NewSessionMessageEvent($message))->toOthers();

        return response()->json([
            'sender_id'     => $request->user()->id,
            'username'      => $request->user()->username,
            'message'       => $message->message,
            'timestamp'     => $message->created_at->format('H:i a'),
            'file_url'      => $filePath ? asset('storage/' . $filePath) : null,
            'file_name'     => $filePath ? $file->getClientOriginalName() : null,
        ]);
    }
}
