<?php

namespace App\Providers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Score;
use App\Models\Attendance;
use App\Policies\StudentPolicy;
use App\Policies\TeacherPolicy;
use App\Policies\ScorePolicy;
use App\Policies\AttendancePolicy;
use App\Services\SchemaManager;
use App\View\Composers\AdminNotificationComposer;
use App\View\Composers\NotificationComposer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // تسجيل SchemaManager كـ Singleton
        $this->app->singleton(SchemaManager::class, function ($app) {
            return new SchemaManager();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // تسجيل الـ Policies للتحقق من الصلاحيات
        Gate::policy(Student::class, StudentPolicy::class);
        Gate::policy(Teacher::class, TeacherPolicy::class);
        Gate::policy(Score::class, ScorePolicy::class);
        Gate::policy(Attendance::class, AttendancePolicy::class);

        // تسجيل View Composer للإشعارات في صفحات الإدارة
        View::composer([
            'layouts.dashboard',
            'layouts.partials.admin-sidebar',
            'layouts.partials.dashboard-mobile-navbar',
            'admin.*',
        ], AdminNotificationComposer::class);

        // تسجيل View Composer للإشعارات العامة لجميع المستخدمين
        View::composer([
            'layouts.dashboard',
            'layouts.partials.dashboard-mobile-navbar',
            'teacher.*',
            'guardian.*',
        ], NotificationComposer::class);
    }
}

