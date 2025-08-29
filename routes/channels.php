<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('group.{groupId}', function ($user, $groupId) {
    // \Log::info("User ID: $user->id wants to join group $groupId");
    return $user->studyGroups()->where('id', $groupId)->exists();
});
Broadcast::channel('session.{sessionId}', function ($sessionId) {
    // \Log::info("User ID: $user->id wants to join group $groupId");
    return \App\Models\StudySession::where('id', $sessionId->id)->exists();
});
