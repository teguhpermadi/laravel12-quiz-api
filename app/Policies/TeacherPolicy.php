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
        return true;
    }

    public function storeBulk(User $user): bool
    {
        return true;
    }

    public function update(User $user, Teacher $model): bool
    {
        return true;
    }

    public function updateBulk(User $user, Teacher $model): bool
    {
        return true;
    }

    public function deleteBulk(User $user, Teacher $model): bool
    {
        return true;
    }

    public function delete(User $user, Teacher $model): bool
    {
        return true;
    }
}
