<?php

namespace App\Policies;

use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AcademicYearPolicy
{
    use HandlesAuthorization;

    public function allowRestify(User $user = null): bool
    {
        return true;
    }

    public function show(User $user = null, AcademicYear $model): bool
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

    public function update(User $user, AcademicYear $model): bool
    {
        return true;
    }

    public function updateBulk(User $user, AcademicYear $model): bool
    {
        return true;
    }

    public function deleteBulk(User $user, AcademicYear $model): bool
    {
        return true;
    }

    public function delete(User $user, AcademicYear $model): bool
    {
        return true;
    }
}
