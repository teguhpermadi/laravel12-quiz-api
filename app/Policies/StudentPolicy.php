<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentPolicy
{
    use HandlesAuthorization;

    public function allowRestify(User $user = null): bool
    {
        return true;
    }

    public function show(User $user = null, Student $model): bool
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

    public function update(User $user, Student $model): bool
    {
        return true;
    }

    public function updateBulk(User $user, Student $model): bool
    {
        return true;
    }

    public function deleteBulk(User $user, Student $model): bool
    {
        return true;
    }

    public function delete(User $user, Student $model): bool
    {
        return true;
    }
}
