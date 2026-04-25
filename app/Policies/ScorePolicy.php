<?php

namespace App\Policies;

use App\Models\Score;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy للتحقق من صلاحيات الوصول للدرجات
 */
class ScorePolicy
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
        return $user->school_id !== null && in_array($user->role, ['admin', 'teacher', 'guardian']);
    }

    public function view(User $user, Score $score): bool
    {
        if ($user->school_id !== $score->school_id) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        // المعلم يرى درجات فصوله وموادّه
        if ($user->role === 'teacher' && $user->teacher) {
            return $score->teacher_id === $user->teacher->id;
        }

        // ولي الأمر يرى درجات أبنائه
        if ($user->role === 'guardian' && $user->guardian) {
            return $user->guardian->students()->where('students.id', $score->student_id)->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->school_id !== null && in_array($user->role, ['admin', 'teacher']);
    }

    public function update(User $user, Score $score): bool
    {
        if ($user->school_id !== $score->school_id) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        // المعلم يعدّل درجاته فقط
        if ($user->role === 'teacher' && $user->teacher) {
            return $score->teacher_id === $user->teacher->id;
        }

        return false;
    }

    public function delete(User $user, Score $score): bool
    {
        return $this->update($user, $score);
    }
}
