<?php

namespace App\Policies;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeacherPolicy
{
    use HandlesAuthorization;

    public function allowRestify(User $user = null): bool
    {
        return true;
    }

    public function show(User $user = null, Teacher $model): bool
    {
        return true;
    }

    public function store(User $user): bool
    {
        return false;
    }

    public function storeBulk(User $user): bool
    {
        return false;
    }

    public function update(User $user, Teacher $model): bool
    {
        return false;
    }

    public function updateBulk(User $user, Teacher $model): bool
    {
        return false;
    }

    public function deleteBulk(User $user, Teacher $model): bool
    {
        return false;
    }

    public function delete(User $user, Teacher $model): bool
    {
        return false;
    }
}
