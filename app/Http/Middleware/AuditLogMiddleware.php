<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware لتسجيل جميع طلبات POST/PUT/DELETE
 * يسجّل العمليات الحساسة تلقائياً
 */
class AuditLogMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // تسجيل فقط للعمليات الحساسة (غير GET)
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $this->logRequest($request, $response);
        }

        return $response;
    }

    /**
     * تسجيل الطلب في Audit Log
     */
    protected function logRequest(Request $request, Response $response): void
    {
        // لا تسجل طلبات AJAX للإشعارات أو البيانات المتكررة
        if ($this->shouldSkip($request)) {
            return;
        }

        $action = $this->getActionFromMethod($request->method());
        $description = $this->getDescription($request);

        try {
            ActivityLog::log(
                action: $action,
                description: $description,
                model: null,
                userId: auth()->id(),
                oldValues: null,
                newValues: $this->sanitizeInput($request->all())
            );
        } catch (\Exception $e) {
            // لا تفشل الطلب إذا فشل التسجيل
            logger()->warning('Audit log failed: ' . $e->getMessage());
        }
    }

    /**
     * تحديد نوع العملية من HTTP Method
     */
    protected function getActionFromMethod(string $method): string
    {
        return match ($method) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'unknown',
        };
    }

    /**
     * توليد وصف للعملية
     */
    protected function getDescription(Request $request): string
    {
        $route = $request->route()?->getName() ?? $request->path();
        $method = $request->method();

        return "{$method} {$route}";
    }

    /**
     * تنظيف المدخلات من البيانات الحساسة
     */
    protected function sanitizeInput(array $input): array
    {
        $sensitiveFields = ['password', 'password_confirmation', 'token', 'secret', '_token'];

        foreach ($sensitiveFields as $field) {
            if (isset($input[$field])) {
                $input[$field] = '***HIDDEN***';
            }
        }

        return $input;
    }

    /**
     * التحقق من تخطي بعض الطلبات
     */
    protected function shouldSkip(Request $request): bool
    {
        $skipRoutes = [
            'notifications/*',
            'broadcasting/*',
            'livewire/*',
        ];

        foreach ($skipRoutes as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }
}
