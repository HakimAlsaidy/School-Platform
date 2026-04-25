<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSchoolAccess
{
    /**
     * التحقق من أن المستخدم ينتمي لنفس المدرسة
     */
    public function handle(Request $request, Closure $next): Response
    {
        // إذا كان Super Admin، السماح بالوصول
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return $next($request);
        }

        // إذا لم تكن هناك مدرسة محددة
        if (!app()->bound('current_school') || !app('current_school')) {
            return redirect('/');
        }

        $currentSchool = app('current_school');

        // إذا كان مستخدم مسجّل
        if (auth()->check()) {
            $user = auth()->user();
            
            // التحقق من أن المستخدم ينتمي لنفس المدرسة
            if ($user->school_id !== $currentSchool->id) {
                auth()->logout();
                return redirect()->route('login')
                    ->with('error', 'ليس لديك صلاحية الوصول لهذه المدرسة');
            }
        }

        return $next($request);
    }
}
