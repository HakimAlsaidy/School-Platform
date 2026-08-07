<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'active' => \App\Http\Middleware\CheckActive::class,
            'superadmin' => \App\Http\Middleware\EnsureSuperAdmin::class,
            'identify.school' => \App\Http\Middleware\IdentifySchool::class,
            'school.access' => \App\Http\Middleware\EnsureSchoolAccess::class,
            'audit' => \App\Http\Middleware\AuditLogMiddleware::class,
            'school.schema' => \App\Http\Middleware\SetSchoolSchema::class,
        ]);

        // تفعيل الـ Schema Middleware لجميع الطلبات
        $middleware->appendToGroup('web', \App\Http\Middleware\SetSchoolSchema::class);
        
        // تفعيل Audit Log لجميع الطلبات
        $middleware->appendToGroup('web', \App\Http\Middleware\AuditLogMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
