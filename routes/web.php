<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SchoolRegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\StudentController as AdminStudent;
use App\Http\Controllers\Admin\TeacherController as AdminTeacher;
use App\Http\Controllers\Admin\GuardianController as AdminGuardian;
use App\Http\Controllers\Admin\ClassroomController as AdminClassroom;
use App\Http\Controllers\Admin\GradeController as AdminGrade;
use App\Http\Controllers\Admin\SubjectController as AdminSubject;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncement;
use App\Http\Controllers\Admin\ReportController as AdminReport;
use App\Http\Controllers\Admin\ScheduleController as AdminSchedule;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboard;
use App\Http\Controllers\Teacher\AttendanceController as TeacherAttendance;
use App\Http\Controllers\Teacher\ScoreController as TeacherScore;
use App\Http\Controllers\Teacher\AssignmentController as TeacherAssignment;
use App\Http\Controllers\Teacher\BehaviorController as TeacherBehavior;
use App\Http\Controllers\Teacher\ScheduleController as TeacherSchedule;
use App\Http\Controllers\Guardian\DashboardController as GuardianDashboard;
use App\Http\Controllers\Guardian\StudentController as GuardianStudent;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboard;
use App\Http\Controllers\SuperAdmin\SchoolController as SuperAdminSchool;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// Route مؤقت لمزامنة الحسابات - احذفه بعد الاستخدام
Route::get('/sync-accounts', function () {
    $count = ['teachers' => 0, 'guardians' => 0];
    
    // مزامنة المعلمين
    \App\Models\User::where('is_active', true)
        ->whereHas('role', fn($q) => $q->where('slug', 'teacher'))
        ->each(function($user) use (&$count) {
            $created = \App\Models\Teacher::firstOrCreate(
                ['user_id' => $user->id],
                ['phone' => $user->phone, 'hire_date' => now()]
            );
            if ($created->wasRecentlyCreated) $count['teachers']++;
        });
    
    // مزامنة أولياء الأمور
    \App\Models\User::where('is_active', true)
        ->whereHas('role', fn($q) => $q->where('slug', 'parent'))
        ->each(function($user) use (&$count) {
            $created = \App\Models\Guardian::firstOrCreate(
                ['user_id' => $user->id],
                ['phone' => $user->phone]
            );
            if ($created->wasRecentlyCreated) $count['guardians']++;
        });
    
    return "تم مزامنة الحسابات! 🎉<br>معلمين جدد: {$count['teachers']}<br>أولياء أمور جدد: {$count['guardians']}";
});

// الصفحة الرئيسية
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isSuperAdmin()) return redirect()->route('superadmin.dashboard');
        if ($user->isAdmin()) return redirect()->route('admin.dashboard');
        if ($user->isTeacher()) return redirect()->route('teacher.dashboard');
        if ($user->isParent()) return redirect()->route('parent.dashboard');
    }
    return view('welcome');
})->name('home');

// تسجيل مدرسة جديدة
Route::get('/school/register', [SchoolRegisterController::class, 'showForm'])->name('school.register');
Route::post('/school/register', [SchoolRegisterController::class, 'register'])->name('school.register.store');
Route::get('/school/register/success', [SchoolRegisterController::class, 'success'])->name('school.register.success');

// مسارات المصادقة
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// API لتحقق من النطاق الفرعي
Route::post('/api/check-subdomain', [SchoolRegisterController::class, 'checkSubdomain'])->name('api.check-subdomain');

// مسارات Super Admin
Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminDashboard::class, 'index'])->name('dashboard');
    
    // إدارة المدارس
    Route::patch('/schools/{school}/approve', [SuperAdminSchool::class, 'approve'])->name('schools.approve');
    Route::delete('/schools/{school}/reject', [SuperAdminSchool::class, 'reject'])->name('schools.reject');
    Route::patch('/schools/{school}/suspend', [SuperAdminSchool::class, 'suspend'])->name('schools.suspend');
    Route::resource('schools', SuperAdminSchool::class);

    // إدارة الاشتراكات
    Route::get('/subscriptions', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::patch('/subscriptions/{subscription}/renew', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'renew'])->name('subscriptions.renew');
    Route::delete('/subscriptions/{subscription}/cancel', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');

    // التقارير
    Route::get('/reports', [\App\Http\Controllers\SuperAdmin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/activity', [\App\Http\Controllers\SuperAdmin\ReportController::class, 'activity'])->name('reports.activity');

    // الإعدادات
    Route::get('/settings', [\App\Http\Controllers\SuperAdmin\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\SuperAdmin\SettingController::class, 'update'])->name('settings.update');
    Route::put('/settings/plans', [\App\Http\Controllers\SuperAdmin\SettingController::class, 'updatePlans'])->name('settings.plans');
});

// مسارات مشتركة للمستخدمين المسجلين (مع identify.school للمستخدمين العاديين)
Route::middleware(['auth', 'identify.school'])->group(function () {
    // الرسائل
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/inbox', [MessageController::class, 'inbox'])->name('inbox');
        Route::get('/sent', [MessageController::class, 'sent'])->name('sent');
        Route::get('/create', [MessageController::class, 'create'])->name('create');
        Route::post('/', [MessageController::class, 'store'])->name('store');
        Route::get('/{message}', [MessageController::class, 'show'])->name('show');
        Route::post('/{message}/reply', [MessageController::class, 'reply'])->name('reply');
        Route::delete('/{message}', [MessageController::class, 'destroy'])->name('destroy');
    });

    // الإشعارات
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/get', [NotificationController::class, 'get'])->name('get');
        Route::get('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('/', [NotificationController::class, 'destroyAll'])->name('destroy-all');
    });
});

// مسارات الإدارة (مدير المدرسة) - مع فلتر المدرسة
Route::middleware(['auth', 'role:admin', 'identify.school'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    
    // الطلاب
    Route::get('/students/trash', [AdminStudent::class, 'trash'])->name('students.trash');
    Route::post('/students/{student}/restore', [AdminStudent::class, 'restore'])->name('students.restore');
    Route::delete('/students/{student}/force-delete', [AdminStudent::class, 'forceDelete'])->name('students.force-delete');
    Route::get('/students/grade/{grade}', [AdminStudent::class, 'byGrade'])->name('students.grade');
    Route::post('/students/grade/{grade}', [AdminStudent::class, 'storeInGrade'])->name('students.store-in-grade');
    Route::post('/students/guardian-quick', [AdminStudent::class, 'storeGuardianQuick'])->name('students.guardian-quick');
    Route::post('/students/assign-classroom', [AdminStudent::class, 'assignClassroom'])->name('students.assign-classroom');
    Route::post('/students/{student}/remove-classroom', [AdminStudent::class, 'removeFromClassroom'])->name('students.remove-classroom');
    Route::resource('students', AdminStudent::class);
    
    // المعلمين
    Route::resource('teachers', AdminTeacher::class);
    
    // أولياء الأمور
    Route::resource('guardians', AdminGuardian::class);
    Route::get('/pending-users', [AdminGuardian::class, 'pending'])->name('pending-users');
    Route::post('/users/{user}/approve', [AdminGuardian::class, 'approve'])->name('users.approve');
    Route::delete('/users/{user}/reject', [AdminGuardian::class, 'reject'])->name('users.reject');
    
    // الفصول
    Route::resource('classrooms', AdminClassroom::class);
    Route::get('/classrooms/grade/{grade}/students-count', [AdminClassroom::class, 'getGradeStudentsCount'])->name('classrooms.grade-students-count');
    
    // الصفوف
    Route::resource('grades', AdminGrade::class)->except(['show']);
    
    // المواد
    Route::resource('subjects', AdminSubject::class)->except(['show']);
    
    // الإعلانات
    Route::resource('announcements', AdminAnnouncement::class)->except(['show']);
    
    // التقارير
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [AdminReport::class, 'index'])->name('index');
        Route::get('/attendance', [AdminReport::class, 'attendance'])->name('attendance');
        Route::get('/scores', [AdminReport::class, 'scores'])->name('scores');
        Route::get('/students', [AdminReport::class, 'students'])->name('students');
        Route::get('/teachers', [AdminReport::class, 'teachers'])->name('teachers');
        Route::get('/student/{student}', [AdminReport::class, 'studentReport'])->name('student');
        Route::post('/export', [AdminReport::class, 'export'])->name('export');
    });

    // الجدول الدراسي
    Route::prefix('schedules')->name('schedules.')->group(function () {
        Route::get('/', [AdminSchedule::class, 'index'])->name('index');
        Route::get('/classrooms', [AdminSchedule::class, 'classrooms'])->name('classrooms');
        Route::get('/teachers', [AdminSchedule::class, 'teachers'])->name('teachers');
        Route::post('/', [AdminSchedule::class, 'store'])->name('store');
        Route::put('/{schedule}', [AdminSchedule::class, 'update'])->name('update');
        Route::delete('/{schedule}', [AdminSchedule::class, 'destroy'])->name('destroy');
        Route::post('/copy', [AdminSchedule::class, 'copy'])->name('copy');
        Route::post('/clear', [AdminSchedule::class, 'clearClassroom'])->name('clear');
    });
});

// مسارات المعلم - مع فلتر المدرسة
Route::middleware(['auth', 'role:teacher', 'identify.school'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboard::class, 'index'])->name('dashboard');
    
    // الحضور
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [TeacherAttendance::class, 'index'])->name('index');
        Route::post('/', [TeacherAttendance::class, 'store'])->name('store');
        Route::get('/report', [TeacherAttendance::class, 'report'])->name('report');
    });
    
    // الدرجات
    Route::prefix('scores')->name('scores.')->group(function () {
        Route::get('/', [TeacherScore::class, 'index'])->name('index');
        Route::get('/create', [TeacherScore::class, 'create'])->name('create');
        Route::post('/', [TeacherScore::class, 'store'])->name('store');
        Route::get('/final', [TeacherScore::class, 'finalScores'])->name('final');
        Route::post('/final', [TeacherScore::class, 'storeFinal'])->name('storeFinal');
        Route::get('/{score}/edit', [TeacherScore::class, 'edit'])->name('edit');
        Route::put('/{score}', [TeacherScore::class, 'update'])->name('update');
        Route::delete('/{score}', [TeacherScore::class, 'destroy'])->name('destroy');
        Route::get('/report', [TeacherScore::class, 'report'])->name('report');
    });
    
    // الواجبات
    Route::resource('assignments', TeacherAssignment::class);
    Route::post('/submissions/{submission}/grade', [TeacherAssignment::class, 'gradeSubmission'])->name('submissions.grade');
    
    // السلوك
    Route::resource('behaviors', TeacherBehavior::class)->except(['show', 'edit', 'update']);
    
    // الجدول الدراسي
    Route::get('/schedule', [TeacherSchedule::class, 'index'])->name('schedule.index');
});

// مسارات ولي الأمر - مع فلتر المدرسة
Route::middleware(['auth', 'role:parent', 'identify.school'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/dashboard', [GuardianDashboard::class, 'index'])->name('dashboard');
    
    // الأبناء
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', [GuardianStudent::class, 'index'])->name('index');
        Route::get('/{student}', [GuardianStudent::class, 'show'])->name('show');
        Route::get('/{student}/attendance', [GuardianStudent::class, 'attendance'])->name('attendance');
        Route::get('/{student}/scores', [GuardianStudent::class, 'scores'])->name('scores');
        Route::get('/{student}/behaviors', [GuardianStudent::class, 'behaviors'])->name('behaviors');
        Route::get('/{student}/schedule', [GuardianStudent::class, 'schedule'])->name('schedule');
    });
});
