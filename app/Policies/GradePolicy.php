<?php

namespace App\Policies;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GradePolicy
{
    use HandlesAuthorization;

    public function allowRestify(User $user = null): bool
    {
        return true;
    }

    public function show(User $user = null, Grade $model): bool
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

    public function update(User $user, Grade $model): bool
    {
        return true;
    }

    public function updateBulk(User $user, Grade $model): bool
    {
        return true;
    }

    public function deleteBulk(User $user, Grade $model): bool
    {
        return true;
    }

    public function delete(User $user, Grade $model): bool
    {
        return true;
    }
}
