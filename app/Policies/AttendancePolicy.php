<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy للتحقق من صلاحيات الوصول للحضور
 */
class AttendancePolicy
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

    public function view(User $user, Attendance $attendance): bool
    {
        if ($user->school_id !== $attendance->school_id) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        // المعلم يرى حضور فصوله
        if ($user->role === 'teacher' && $user->teacher) {
            $teacherClassroomIds = $user->teacher->classrooms()->pluck('classrooms.id')->toArray();
            return in_array($attendance->student->classroom_id, $teacherClassroomIds);
        }

        // ولي الأمر يرى حضور أبنائه
        if ($user->role === 'guardian' && $user->guardian) {
            return $user->guardian->students()->where('students.id', $attendance->student_id)->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->school_id !== null && in_array($user->role, ['admin', 'teacher']);
    }

    public function update(User $user, Attendance $attendance): bool
    {
        if ($user->school_id !== $attendance->school_id) {
            return false;
        }

        return in_array($user->role, ['admin', 'teacher']);
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->school_id === $attendance->school_id && $user->role === 'admin';
    }
}
