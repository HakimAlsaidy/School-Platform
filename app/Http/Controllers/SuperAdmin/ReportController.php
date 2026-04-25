<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Guardian;
use App\Models\SchoolSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * عرض التقارير العامة
     */
    public function index()
    {
        // إحصائيات الأشهر الـ 12 الأخيرة
        $monthlyStats = $this->getMonthlyStats();
        
        // توزيع المدارس حسب النوع
        $schoolsByType = School::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get()
            ->mapWithKeys(fn($item) => [$item->type => $item->count]);

        // توزيع المدارس حسب المرحلة
        $schoolsByLevel = School::select('level', DB::raw('count(*) as count'))
            ->groupBy('level')
            ->get()
            ->mapWithKeys(fn($item) => [$item->level => $item->count]);

        // أكبر 10 مدارس
        $topSchools = School::withCount(['students', 'teachers'])
            ->orderByDesc('students_count')
            ->take(10)
            ->get();

        // إحصائيات الاشتراكات
        $subscriptionStats = [
            'free' => SchoolSubscription::where('plan', 'free')->count(),
            'basic' => SchoolSubscription::where('plan', 'basic')->count(),
            'pro' => SchoolSubscription::where('plan', 'pro')->count(),
            'enterprise' => SchoolSubscription::where('plan', 'enterprise')->count(),
        ];

        // معدل النمو الشهري
        $currentMonth = School::whereMonth('created_at', now()->month)->count();
        $lastMonth = School::whereMonth('created_at', now()->subMonth()->month)->count();
        $growthRate = $lastMonth > 0 ? (($currentMonth - $lastMonth) / $lastMonth) * 100 : 0;

        return view('superadmin.reports.index', compact(
            'monthlyStats',
            'schoolsByType',
            'schoolsByLevel',
            'topSchools',
            'subscriptionStats',
            'growthRate'
        ));
    }

    /**
     * الحصول على إحصائيات الأشهر
     */
    protected function getMonthlyStats(): array
    {
        $stats = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('Y-m');
            
            $stats[$month] = [
                'month' => $date->translatedFormat('M Y'),
                'schools' => School::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'students' => Student::withoutGlobalScope('school')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }

        return $stats;
    }

    /**
     * تقرير النشاط
     */
    public function activity()
    {
        // آخر المدارس المسجلة
        $recentSchools = School::latest()->take(20)->get();

        // آخر الاشتراكات
        $recentSubscriptions = SchoolSubscription::with('school')
            ->latest()
            ->take(20)
            ->get();

        return view('superadmin.reports.activity', compact('recentSchools', 'recentSubscriptions'));
    }
}
