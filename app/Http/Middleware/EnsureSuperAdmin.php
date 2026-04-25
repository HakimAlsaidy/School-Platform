<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    /**
     * التحقق من أن المستخدم Super Admin
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'ليس لديك صلاحية الوصول لهذه الصفحة');
        }

        // تسجيل أنه Super Admin حتى لا يتم تطبيق Global Scope عليه
        app()->instance('is_super_admin', true);

        return $next($request);
    }
}
