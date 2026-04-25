<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy للتحقق من صلاحيات الوصول للطلاب
 * يضمن أن كل مستخدم يرى فقط بيانات مدرسته
 */
class StudentPolicy
{
    use HandlesAuthorization;

    /**
     * السماح لـ Super Admin بكل شيء
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->is_super_admin) {
            return true;
        }
        return null;
    }

    /**
     * التحقق من إمكانية عرض قائمة الطلاب
     */
    public function viewAny(User $user): bool
    {
        return $user->school_id !== null && in_array($user->role, ['admin', 'teacher', 'guardian']);
    }

    /**
     * التحقق من إمكانية عرض طالب معين
     */
    public function view(User $user, Student $student): bool
    {
        // التحقق من انتماء الطالب لنفس المدرسة
        if ($user->school_id !== $student->school_id) {
            return false;
        }

        // المدير يرى الكل
        if ($user->role === 'admin') {
            return true;
        }

        // المعلم يرى طلاب فصوله فقط
        if ($user->role === 'teacher' && $user->teacher) {
            $teacherClassroomIds = $user->teacher->classrooms()->pluck('classrooms.id')->toArray();
            return in_array($student->classroom_id, $teacherClassroomIds);
        }

        // ولي الأمر يرى أبناءه فقط
        if ($user->role === 'guardian' && $user->guardian) {
            return $user->guardian->students()->where('students.id', $student->id)->exists();
        }

        return false;
    }

    /**
     * التحقق من إمكانية إنشاء طالب
     */
    public function create(User $user): bool
    {
        return $user->school_id !== null && $user->role === 'admin';
    }

    /**
     * التحقق من إمكانية تعديل طالب
     */
    public function update(User $user, Student $student): bool
    {
        return $user->school_id === $student->school_id && $user->role === 'admin';
    }

    /**
     * التحقق من إمكانية حذف طالب
     */
    public function delete(User $user, Student $student): bool
    {
        return $user->school_id === $student->school_id && $user->role === 'admin';
    }

    /**
     * التحقق من إمكانية استعادة طالب محذوف
     */
    public function restore(User $user, Student $student): bool
    {
        return $user->school_id === $student->school_id && $user->role === 'admin';
    }

    /**
     * التحقق من إمكانية الحذف النهائي
     */
    public function forceDelete(User $user, Student $student): bool
    {
        return $user->school_id === $student->school_id && $user->role === 'admin';
    }
}
