<?php

namespace App\Http\Middleware;

use App\Models\School;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware لتحديد المدرسة الحالية للمستخدم
 * يعمل مع MySQL/MariaDB باستخدام school_id
 */
class SetSchoolSchema
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->school_id) {
            // جلب المدرسة وحفظها في الـ Container
            $school = School::find($user->school_id);
            
            if ($school) {
                app()->instance('current_school', $school);
                app()->instance('current_school_id', $user->school_id);
                
                // حفظ المدرسة في الـ Request للوصول السهل
                $request->attributes->set('school_id', $user->school_id);
                $request->attributes->set('current_school', $school);
            }
            
            // تحديد إذا كان المستخدم Super Admin
            app()->instance('is_super_admin', $user->is_super_admin ?? false);
        }

        return $next($request);
    }
}
