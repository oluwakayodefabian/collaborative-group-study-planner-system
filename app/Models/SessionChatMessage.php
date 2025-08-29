<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionChatMessage extends Model
{
    protected $fillable = [
        'user_id',
        'study_session_id',
        'message',
        'has_file',
        'file_path',
        'file_name',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studySession(): BelongsTo
    {
        return $this->belongsTo(StudySession::class);
    }

    // public function is_participant(): bool
    // {
    //     return $this->studySession->participants->contains(request()->user()->id);
    // }
}
