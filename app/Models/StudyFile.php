<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StudyFile extends Model
{
    protected $fillable = [
        'user_id',
        'group_id',
        'original_name',
        'storage_path',
        'file_type',
        'size',
        'shared_with',
        'category',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    public function sharedWith(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'file_user_shares')
            ->withPivot('shared_at');
    }


    public function auditLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function is_owner(): bool
    {
        return request()->user()->id === $this->user_id;
    }
}
