<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileShare extends Model
{
    protected $fillable = ['study_file_id', 'shared_by', 'shared_to'];

    public function file(): BelongsTo
    {
        return $this->belongsTo(StudyFile::class, 'study_file_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_by');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_to');
    }
}
