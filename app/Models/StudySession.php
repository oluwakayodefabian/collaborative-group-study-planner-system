<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudySession extends Model
{
    protected $fillable = [
        'study_group_id',
        'session_title',
        'start_time',
        'end_time',
        'location',
        'participants',
        'description',
        'creator_id',
    ];

    public function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'participants' => 'array',
        ];
    }
    public function group(): BelongsTo
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function sessionChatMessages(): HasMany
    {
        return $this->hasMany(SessionChatMessage::class);
    }

    public function is_creator(): bool
    {
        return $this->creator_id === request()->user()->id;
    }

    public function is_participant(): bool
    {
        return collect($this->participants)->contains(request()->user()->id);
    }

    public function notifiedUsers()
    {
        return $this->belongsToMany(User::class, 'session_user_notifications')->withTimestamps();
    }
}
