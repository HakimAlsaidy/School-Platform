<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Services\SchoolRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class SchoolRegisterController extends Controller
{
    protected SchoolRegistrationService $registrationService;

    public function __construct(SchoolRegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    /**
     * عرض نموذج تسجيل المدرسة
     */
    public function showForm()
    {
        return view('auth.school-register');
    }

    /**
     * تسجيل مدرسة جديدة - مع طبقات أمان متعددة
     */
    public function register(Request $request)
    {
        // التحقق من الـ Honeypot (حماية من البوتات)
        if ($request->filled('website') || $request->filled('company')) {
            // حقل مخفي - إذا تم ملؤه فهو بوت
            abort(403, 'تم رفض الطلب.');
        }

        // Rate Limiting: منع تكرار التسجيل من نفس الـ IP
        $rateLimitKey = 'school-register:' . $request->ip();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return back()->withErrors([
                'error' => 'لقد تجاوزت عدد محاولات التسجيل المسموحة. حاول مرة أخرى بعد ' . ceil($seconds / 60) . ' دقيقة.'
            ]);
        }

        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:50|unique:schools,subdomain|regex:/^[a-z0-9-]+$/',
            'type' => 'required|in:public,private,international',
            'level' => 'required|in:elementary,middle,high,all',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'admin_name' => 'required|string|max:255',
            'admin_phone' => 'required|string|max:20|unique:users,phone',
            'admin_password' => 'required|string|min:8|max:72|confirmed',
        ], [
            'subdomain.regex' => 'النطاق الفرعي يجب أن يحتوي على أحرف إنجليزية صغيرة وأرقام فقط',
            'subdomain.unique' => 'هذا النطاق الفرعي مستخدم بالفعل',
            'admin_phone.unique' => 'رقم الجوال مسجل مسبقاً',
            'admin_password.max' => 'كلمة المرور طويلة جداً',
        ]);

        try {
            // تسجيل محاولة التسجيل في الـ Rate Limiter
            RateLimiter::hit($rateLimitKey, 3600); // ساعة واحدة

            // استخدام الخدمة للتسجيل مع طبقات الأمان
            $school = $this->registrationService->register($validated, $request);

            return redirect()->route('school.register.success')
                ->with('school_name', $school->name)
                ->with('subdomain', $school->subdomain);

        } catch (\Exception $e) {
            // تسجيل الخطأ
            report($e);

            return back()->withInput()->withErrors([
                'error' => 'حدث خطأ أثناء التسجيل: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * صفحة نجاح التسجيل
     */
    public function success()
    {
        if (!session('school_name')) {
            return redirect()->route('school.register');
        }

        return view('auth.school-register-success');
    }

    /**
     * التحقق من توفر النطاق الفرعي (AJAX)
     */
    public function checkSubdomain(Request $request)
    {
        $subdomain = strtolower($request->input('subdomain'));

        if (empty($subdomain)) {
            return response()->json(['available' => false, 'message' => 'النطاق الفرعي مطلوب']);
        }

        // التحقق من الصيغة
        if (!preg_match('/^[a-z0-9-]+$/', $subdomain)) {
            return response()->json(['available' => false, 'message' => 'النطاق يجب أن يحتوي على أحرف إنجليزية صغيرة وأرقام فقط']);
        }

        // التحقق من الطول
        if (strlen($subdomain) < 3 || strlen($subdomain) > 50) {
            return response()->json(['available' => false, 'message' => 'النطاق يجب أن يكون بين 3 و 50 حرفاً']);
        }

        // التحقق من الكلمات المحجوزة والخطرة
        $reserved = [
            'admin', 'superadmin', 'api', 'www', 'mail', 'ftp', 'smtp',
            'support', 'help', 'login', 'register', 'app', 'dashboard',
            'billing', 'pay', 'payment', 'secure', 'auth', 'oauth',
            'ns1', 'ns2', 'dns', 'webmail', 'cpanel', 'ssl', 'vpn',
        ];
        if (in_array($subdomain, $reserved)) {
            return response()->json(['available' => false, 'message' => 'هذا النطاق محجوز']);
        }

// منع النطاقات التي تبدأ أو تنتهي بعلامة
        if (preg_match('/^[-_]/', $subdomain) || preg_match('/[-_]$/', $subdomain)) {
            return response()->json(['available' => false, 'message' => 'النطاق يجب أن يبدأ وينتهي بحرف أو رقم']);
        }

        // التحقق من الاستخدام
        $exists = School::where('subdomain', $subdomain)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'هذا النطاق مستخدم بالفعل' : 'النطاق متاح ✓'
        ]);
    }
}
