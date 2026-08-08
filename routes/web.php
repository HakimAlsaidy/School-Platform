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
use App\Http\Controllers\Admin\FinanceController as AdminFinance;
use App\Http\Controllers\Admin\LibraryController as AdminLibrary;
use App\Http\Controllers\Admin\TransportController as AdminTransport;
use App\Http\Controllers\Guardian\FinanceController as GuardianFinance;
use App\Http\Controllers\Guardian\TransportController as GuardianTransport;
use App\Http\Controllers\Guardian\QuizController as GuardianQuiz;
use App\Http\Controllers\Teacher\QuestionBankController as TeacherQuestionBank;
use App\Http\Controllers\Teacher\QuizController as TeacherQuiz;
use App\Http\Controllers\Teacher\MaterialController as TeacherMaterial;
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
use App\Http\Controllers\SuperAdmin\FeatureOverviewController as SuperAdminFeatureOverview;
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

    // نظرة عامة على الميزات (الإشراف على استخدام الميزات في جميع المدارس)
    Route::get('/features', [SuperAdminFeatureOverview::class, 'index'])->name('features.index');
    
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

// مسارات الذكاء الاصطناعي
Route::middleware(['auth', 'identify.school'])->prefix('ai')->name('ai.')->group(function () {
    // لوحة التحليلات الذكية
    Route::get('/analytics', [\App\Http\Controllers\AIController::class, 'analytics'])->name('analytics');
    Route::get('/analytics/data', [\App\Http\Controllers\AIController::class, 'schoolAnalytics'])->name('analytics.data');

    // المساعد الذكي
    Route::get('/assistant', [\App\Http\Controllers\AIController::class, 'assistant'])->name('assistant');
    Route::post('/ask', [\App\Http\Controllers\AIController::class, 'ask'])->name('ask');

    // رؤى طالب
    Route::get('/students/{student}/insights', [\App\Http\Controllers\AIController::class, 'studentInsights'])->name('students.insights');
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

    // المالية (الرسوم والمدفوعات والمصروفات)
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/fees', [AdminFinance::class, 'fees'])->name('fees');
        Route::post('/fees', [AdminFinance::class, 'feesStore'])->name('fees.store');
        Route::put('/fees/{fee}', [AdminFinance::class, 'feesUpdate'])->name('fees.update');
        Route::delete('/fees/{fee}', [AdminFinance::class, 'feesDestroy'])->name('fees.destroy');
        Route::post('/fees/{fee}/assign', [AdminFinance::class, 'assignFee'])->name('fees.assign');

        Route::get('/payments', [AdminFinance::class, 'payments'])->name('payments');
        Route::post('/payments', [AdminFinance::class, 'paymentsStore'])->name('payments.store');

        Route::get('/accounting', [AdminFinance::class, 'accounting'])->name('accounting');
        Route::post('/expenses', [AdminFinance::class, 'expenseStore'])->name('expenses.store');
        Route::delete('/expenses/{expense}', [AdminFinance::class, 'expenseDestroy'])->name('expenses.destroy');
        Route::post('/incomes', [AdminFinance::class, 'incomeStore'])->name('incomes.store');
        Route::delete('/incomes/{income}', [AdminFinance::class, 'incomeDestroy'])->name('incomes.destroy');
    });

    // المكتبة
    Route::prefix('library')->name('library.')->group(function () {
        Route::get('/', [AdminLibrary::class, 'index'])->name('index');
        Route::post('/books', [AdminLibrary::class, 'store'])->name('books.store');
        Route::delete('/books/{book}', [AdminLibrary::class, 'destroy'])->name('books.destroy');
        Route::post('/loans', [AdminLibrary::class, 'loan'])->name('loans.store');
        Route::post('/loans/{loan}/return', [AdminLibrary::class, 'returnLoan'])->name('loans.return');
    });

    // النقل المدرسي
    Route::prefix('transport')->name('transport.')->group(function () {
        Route::get('/', [AdminTransport::class, 'index'])->name('index');
        Route::post('/buses', [AdminTransport::class, 'busStore'])->name('buses.store');
        Route::delete('/buses/{bus}', [AdminTransport::class, 'busDestroy'])->name('buses.destroy');
        Route::post('/routes', [AdminTransport::class, 'routeStore'])->name('routes.store');
        Route::delete('/routes/{route}', [AdminTransport::class, 'routeDestroy'])->name('routes.destroy');
        Route::post('/students', [AdminTransport::class, 'assignStudent'])->name('students.assign');
        Route::delete('/students/{transportStudent}', [AdminTransport::class, 'removeStudent'])->name('students.remove');
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

    // بنك الأسئلة
    Route::prefix('question-bank')->name('question-bank.')->group(function () {
        Route::get('/', [TeacherQuestionBank::class, 'index'])->name('index');
        Route::post('/', [TeacherQuestionBank::class, 'store'])->name('store');
        Route::delete('/{question}', [TeacherQuestionBank::class, 'destroy'])->name('destroy');
    });

    // الاختبارات الإلكترونية
    Route::prefix('quizzes')->name('quizzes.')->group(function () {
        Route::get('/', [TeacherQuiz::class, 'index'])->name('index');
        Route::get('/create', [TeacherQuiz::class, 'create'])->name('create');
        Route::post('/', [TeacherQuiz::class, 'store'])->name('store');
        Route::get('/{quiz}', [TeacherQuiz::class, 'show'])->name('show');
        Route::post('/{quiz}/toggle', [TeacherQuiz::class, 'togglePublish'])->name('toggle');
        Route::post('/{quiz}/import', [TeacherQuiz::class, 'importFromBank'])->name('import');
        Route::delete('/{quiz}', [TeacherQuiz::class, 'destroy'])->name('destroy');
    });

    // المواد الدراسية
    Route::prefix('materials')->name('materials.')->group(function () {
        Route::get('/', [TeacherMaterial::class, 'index'])->name('index');
        Route::post('/', [TeacherMaterial::class, 'store'])->name('store');
        Route::delete('/{material}', [TeacherMaterial::class, 'destroy'])->name('destroy');
    });
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

    // الرسوم والمدفوعات
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/fees', [GuardianFinance::class, 'fees'])->name('fees');
        Route::post('/fees/{studentFee}/pay', [GuardianFinance::class, 'pay'])->name('fees.pay');
        Route::get('/payments', [GuardianFinance::class, 'payments'])->name('payments');
    });

    // النقل المدرسي
    Route::get('/transport', [GuardianTransport::class, 'index'])->name('transport.index');

    // الاختبارات الإلكترونية
    Route::prefix('quizzes')->name('quizzes.')->group(function () {
        Route::get('/{student}', [GuardianQuiz::class, 'index'])->name('index');
        Route::get('/{student}/{quiz}', [GuardianQuiz::class, 'show'])->name('show');
        Route::post('/{student}/{quiz}/submit', [GuardianQuiz::class, 'submit'])->name('submit');
        Route::get('/{student}/results/{attempt}', [GuardianQuiz::class, 'results'])->name('results');
    });
});
