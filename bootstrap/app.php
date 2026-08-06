<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Ba trang HTML tĩnh chưa có khả năng gửi CSRF token. Chỉ miễn trừ
        // đúng ba endpoint thay đổi dữ liệu mà các trang này đang sử dụng.
        $middleware->validateCsrfTokens(except: [
            'registration/store',
            'registration/cancel/*',
            'registration/update/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
