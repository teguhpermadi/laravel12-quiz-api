<?php

namespace App\Policies;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TeacherPolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('viewAny-teacher');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Teacher $teacher): bool
    {
        return $user->can('view-teacher');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create-teacher');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Teacher $teacher): bool
    {
        return $user->can('update-teacher');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Teacher $teacher): bool
    {
        return $user->can('delete-teacher');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Teacher $teacher): bool
    {
        return $user->can('restore-teacher');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Teacher $teacher): bool
    {
        return $user->can('forceDelete-teacher');
    }
}
