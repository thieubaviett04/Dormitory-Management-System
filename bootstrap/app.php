<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Tắt bảo vệ CSRF cho các đường dẫn của Module Đăng ký
        $middleware->validateCsrfTokens(except: [
            'registration/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();