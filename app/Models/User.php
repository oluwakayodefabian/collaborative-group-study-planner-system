<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use \App\Traits\HasWebPushSubscriptions;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasWebPushSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function studyGroups(): HasMany
    {
        return $this->hasMany(StudyGroup::class, 'creator_id');
    }

    public function GroupMemberships(): HasMany
    {
        return $this->hasMany(GroupMembership::class);
    }

    public function StudyFiles(): HasMany
    {
        return $this->hasMany(StudyFile::class);
    }

    public function ChatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }


    /**
     * Get the study sessions for which the user has been notified.
     *
     * This relationship represents the many-to-many association between users
     * and study sessions through the 'session_user_notifications' pivot table.
     * It includes timestamp information indicating when the notification was sent.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */

    public function notifiedSessions()
    {
        return $this->belongsToMany(StudySession::class, 'session_user_notifications')->withTimestamps();
    }

    /**
     * Checks if the user has been notified for the given session
     *
     * @param StudySession $session
     * @return bool
     */
    public function hasBeenNotifiedFor(StudySession $session)
    {
        return $this->notifiedSessions()->where('study_session_id', $session->id)->exists();
    }

    /**
     * Marks the user as notified for the given study session
     *
     * @param StudySession $session The study session to mark the user as notified for
     * @return void
     */
    public function markNotifiedFor(StudySession $session)
    {
        $this->notifiedSessions()->attach($session->id, ['notified_at' => now()]);
    }
}
