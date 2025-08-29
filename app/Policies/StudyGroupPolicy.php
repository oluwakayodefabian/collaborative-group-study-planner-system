<?php

namespace App\Policies;

use App\Models\StudyGroup;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StudyGroupPolicy
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
    public function view(User $user, StudyGroup $studyGroup): bool
    {
        return $user->id === $studyGroup->creator_id || $studyGroup->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StudyGroup $studyGroup): bool
    {
        return $user->id === $studyGroup->creator_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StudyGroup $studyGroup): bool
    {
        return $user->id === $studyGroup->creator_id;
    }

    public function join(User $user, StudyGroup $studyGroup): Response
    {
        return !$studyGroup->members()->where('user_id', $user->id)->exists()
            ? Response::allow()
            : Response::deny('You are already a member of this study group');
    }

    public function leave(User $user, StudyGroup $studyGroup): Response
    {
        return $studyGroup->members()->where('user_id', $user->id)->exists()
            ? Response::allow()
            : Response::deny('You are not a member of this study group');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StudyGroup $studyGroup): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StudyGroup $studyGroup): bool
    {
        return false;
    }
}
