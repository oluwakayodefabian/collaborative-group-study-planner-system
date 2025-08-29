<?php

namespace App\Policies;

use App\Models\StudyFile;
use App\Models\StudyGroup;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StudyFilePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, StudyGroup $studyGroup): bool
    {
        return $studyGroup->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StudyGroup $studyGroup): bool
    {
        return $studyGroup->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, StudyGroup $studyGroup): bool
    {
        return $studyGroup->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StudyFile $studyFile): bool
    {
        return $user->id === $studyFile->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StudyFile $studyFile): bool
    {
        return $user->id === $studyFile->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StudyFile $studyFile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StudyFile $studyFile): bool
    {
        return false;
    }
}
