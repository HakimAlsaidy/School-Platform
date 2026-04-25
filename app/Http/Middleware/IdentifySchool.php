<?php

namespace App\Http\Middleware;

use App\Models\School;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IdentifySchool
{
    /**
     * تحديد المدرسة من الـ Subdomain أو حساب المستخدم
     */
    public function handle(Request $request, Closure $next): Response
    {
        // أولاً: تحقق إذا المستخدم Super Admin
        if (Auth::check() && Auth::user()->isSuperAdmin()) {
            app()->instance('current_school', null);
            app()->instance('is_super_admin', true);
            return $next($request);
        }

        // ثانياً: محاولة تحديد المدرسة من الـ Subdomain
        $host = $request->getHost();
        $subdomain = $this->extractSubdomain($host);
        
        $school = null;

        // إذا وجد subdomain، ابحث عن المدرسة
        if ($subdomain && $subdomain !== 'www' && $subdomain !== 'admin') {
            $school = School::where('subdomain', $subdomain)
                ->where('is_active', true)
                ->first();
        }

        // ثالثاً: إذا لم يوجد subdomain، استخدم مدرسة المستخدم المسجل
        if (!$school && Auth::check() && Auth::user()->school_id) {
            $school = School::where('id', Auth::user()->school_id)
                ->where('is_active', true)
                ->first();
        }

        // رابعاً: إذا لم توجد مدرسة والمستخدم مسجل دخول وليس Super Admin
        if (!$school && Auth::check() && !Auth::user()->isSuperAdmin()) {
            // المستخدم ليس له مدرسة - ربما لم يتم تعيينه بعد
            app()->instance('current_school', null);
            app()->instance('is_super_admin', false);
            return $next($request);
        }

        // خامساً: إذا كان زائر بدون تسجيل دخول من الدومين الرئيسي
        if (!$school && !Auth::check()) {
            app()->instance('current_school', null);
            app()->instance('is_super_admin', false);
            return $next($request);
        }

        // إذا وجدت مدرسة، تحقق من الترخيص
        if ($school) {
            if (!$school->isLicenseValid()) {
                abort(403, 'انتهت صلاحية ترخيص المدرسة. يرجى التواصل مع الإدارة.');
            }

            // تخزين المدرسة في Container
            app()->instance('current_school', $school);
            app()->instance('is_super_admin', false);

            // إضافة المدرسة للـ Request
            $request->merge(['school' => $school]);

            // مشاركة المدرسة مع جميع الـ Views
            view()->share('currentSchool', $school);
        }

        return $next($request);
    }

    /**
     * استخراج الـ Subdomain من الـ Host
     */
    protected function extractSubdomain(string $host): ?string
    {
        // إزالة المنفذ إن وجد
        $host = explode(':', $host)[0];
        
        // الحصول على الدومين الرئيسي من الإعدادات
        $mainDomain = config('app.domain', 'localhost');
        
        // إذا كان localhost للتطوير
        if ($host === 'localhost' || $host === '127.0.0.1') {
            // استخدام query parameter للتطوير
            return request()->query('school');
        }

        // إزالة الدومين الرئيسي للحصول على الـ subdomain
        $subdomain = str_replace('.' . $mainDomain, '', $host);
        
        // إذا كان نفس الدومين بدون subdomain
        if ($subdomain === $mainDomain || $subdomain === $host) {
            return null;
        }

        return $subdomain;
    }
}
