<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StudyGroup;
use App\Models\StudySession;
use Illuminate\Auth\Access\Response;

class StudySessionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StudySession $studySession): bool
    {
        return $user->id === $studySession->group->creator_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, StudyGroup $studyGroup): bool
    {
        return $user->id === $studyGroup->creator_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StudySession $studySession): bool
    {
        return $user->id === $studySession->group->creator_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StudySession $studySession): bool
    {
        return $user->id === $studySession->creator_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StudySession $studySession): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StudySession $studySession): bool
    {
        return false;
    }
}
