<?php

namespace App\Policies;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy للتحقق من صلاحيات الوصول للمعلمين
 */
class TeacherPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->is_super_admin) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->school_id !== null && $user->role === 'admin';
    }

    public function view(User $user, Teacher $teacher): bool
    {
        if ($user->school_id !== $teacher->school_id) {
            return false;
        }

        // المدير يرى الكل
        if ($user->role === 'admin') {
            return true;
        }

        // المعلم يرى بياناته فقط
        if ($user->role === 'teacher' && $user->teacher) {
            return $user->teacher->id === $teacher->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->school_id !== null && $user->role === 'admin';
    }

    public function update(User $user, Teacher $teacher): bool
    {
        return $user->school_id === $teacher->school_id && $user->role === 'admin';
    }

    public function delete(User $user, Teacher $teacher): bool
    {
        return $user->school_id === $teacher->school_id && $user->role === 'admin';
    }
}
