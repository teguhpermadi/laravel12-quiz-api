<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubjectPolicy
{
    use HandlesAuthorization;

    public function allowRestify(User $user = null): bool
    {
        return true;
    }

    public function show(User $user = null, Subject $model): bool
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

    public function update(User $user, Subject $model): bool
    {
        return false;
    }

    public function updateBulk(User $user, Subject $model): bool
    {
        return false;
    }

    public function deleteBulk(User $user, Subject $model): bool
    {
        return false;
    }

    public function delete(User $user, Subject $model): bool
    {
        return false;
    }
}
