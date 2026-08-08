<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    /**
     * عرض الإعدادات العامة
     */
    public function index()
    {
        $settings = [
            'platform_name' => config('app.name', 'إيدو لينك'),
            'platform_description' => Cache::get('platform_description', 'منصة إدارة المدارس الذكية'),
            'contact_email' => Cache::get('contact_email', 'support@edulink.com'),
            'contact_phone' => Cache::get('contact_phone', '920000000'),
            'auto_approve_schools' => Cache::get('auto_approve_schools', false),
            'trial_days' => Cache::get('trial_days', 30),
            'max_students_free' => Cache::get('max_students_free', 50),
            'max_teachers_free' => Cache::get('max_teachers_free', 10),
        ];

        $plans = $this->getPlans();

        return view('superadmin.settings.index', compact('settings', 'plans'));
    }

    /**
     * حفظ الإعدادات
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'platform_name' => 'required|string|max:255',
            'platform_description' => 'nullable|string|max:1000',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'auto_approve_schools' => 'boolean',
            'trial_days' => 'required|integer|min:7|max:90',
            'max_students_free' => 'required|integer|min:10|max:100',
            'max_teachers_free' => 'required|integer|min:5|max:50',
        ]);

        foreach ($validated as $key => $value) {
            Cache::forever($key, $value);
        }

        return redirect()->back()->with('success', 'تم حفظ الإعدادات بنجاح');
    }

    /**
     * تحديث خطط الأسعار
     */
    public function updatePlans(Request $request)
    {
        $validated = $request->validate([
            'plans' => 'required|array',
            'plans.*.price' => 'required|numeric|min:0',
            'plans.*.max_students' => 'required|integer|min:1',
            'plans.*.max_teachers' => 'required|integer|min:1',
            'plans.*.features' => 'nullable|array',
        ]);

        Cache::forever('subscription_plans', $validated['plans']);

        return redirect()->back()->with('success', 'تم تحديث الخطط بنجاح');
    }

    /**
     * الحصول على خطط الأسعار
     */
    protected function getPlans(): array
    {
        return Cache::get('subscription_plans', [
            'basic' => [
                'name' => 'الأساسي',
                'price' => 99,
                'max_students' => 200,
                'max_teachers' => 30,
                'features' => ['إدارة الطلاب', 'إدارة المعلمين', 'الجدول الدراسي', 'الدرجات'],
            ],
            'pro' => [
                'name' => 'المتقدم',
                'price' => 199,
                'max_students' => 500,
                'max_teachers' => 80,
                'features' => ['جميع مميزات الأساسي', 'الرسائل', 'التقارير المتقدمة', 'الدعم الفني'],
            ],
            'enterprise' => [
                'name' => 'المؤسسي',
                'price' => 499,
                'max_students' => 0, // غير محدود
                'max_teachers' => 0,
                'features' => ['جميع المميزات', 'API متقدم', 'دعم مخصص', 'تخصيص كامل'],
            ],
        ]);
    }
}
