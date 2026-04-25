<?php

namespace App\View\Composers;

use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Guardian;
use App\Models\Role;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\View\View;

class AdminNotificationComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return;
        }

        // عدد طلبات التسجيل المعلقة
        $pendingUsersCount = User::where('is_active', false)
            ->whereHas('role', function ($q) {
                $q->whereIn('slug', [Role::TEACHER, Role::PARENT]);
            })
            ->count();

        // عدد الرسائل غير المقروءة
        $unreadMessagesCount = auth()->user()->unread_messages_count ?? 0;

        // إجمالي الإشعارات
        $totalNotifications = $pendingUsersCount + $unreadMessagesCount;

        // الإشعارات الأخيرة (طلبات التسجيل الجديدة)
        $recentPendingUsers = User::where('is_active', false)
            ->whereHas('role', function ($q) {
                $q->whereIn('slug', [Role::TEACHER, Role::PARENT]);
            })
            ->with('role')
            ->latest()
            ->take(5)
            ->get();

        // ===== عدادات الأقسام =====
        
        // عدد المعلمين الجدد (آخر 7 أيام)
        $newTeachersCount = Teacher::where('created_at', '>=', now()->subDays(7))->count();
        
        // عدد أولياء الأمور الجدد (آخر 7 أيام)
        $newGuardiansCount = Guardian::where('created_at', '>=', now()->subDays(7))->count();
        
        // عدد الطلاب الجدد (آخر 7 أيام)
        $newStudentsCount = Student::where('created_at', '>=', now()->subDays(7))->count();
        
        // المعلمين بدون فصول مُعيَّنة
        $teachersWithoutClassrooms = Teacher::whereDoesntHave('classrooms')->count();
        
        // أولياء الأمور بدون أبناء
        $guardiansWithoutStudents = Guardian::whereDoesntHave('students')->count();
        
        // الطلاب بدون فصل
        $studentsWithoutClassroom = Student::whereNull('classroom_id')->count();

        // إجمالي لكل قسم
        $totalTeachers = Teacher::count();
        $totalGuardians = Guardian::count();
        $totalStudents = Student::count();
        $totalClassrooms = Classroom::count();
        $totalGrades = Grade::count();
        $totalSubjects = Subject::count();

        $view->with([
            // الإشعارات
            'pendingUsersCount' => $pendingUsersCount,
            'unreadMessagesCount' => $unreadMessagesCount,
            'totalNotifications' => $totalNotifications,
            'recentPendingUsers' => $recentPendingUsers,
            
            // عدادات جديدة
            'newTeachersCount' => $newTeachersCount,
            'newGuardiansCount' => $newGuardiansCount,
            'newStudentsCount' => $newStudentsCount,
            
            // تنبيهات
            'teachersWithoutClassrooms' => $teachersWithoutClassrooms,
            'guardiansWithoutStudents' => $guardiansWithoutStudents,
            'studentsWithoutClassroom' => $studentsWithoutClassroom,
            
            // إجماليات
            'totalTeachers' => $totalTeachers,
            'totalGuardians' => $totalGuardians,
            'totalStudents' => $totalStudents,
            'totalClassrooms' => $totalClassrooms,
            'totalGrades' => $totalGrades,
            'totalSubjects' => $totalSubjects,
        ]);
    }
}
