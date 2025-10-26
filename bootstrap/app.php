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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\Admin::class,
            'instructor' => \App\Http\Middleware\InstructorMiddleware::class,
            'checkpostsize' => \App\Http\Middleware\CheckPostSize::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'streaming.throttle' => \App\Http\Middleware\StreamingThrottle::class,
            'secure.content' => \App\Http\Middleware\SecureContent::class,
        ]);

        // Rate limiting for streaming
        $middleware->throttleApi('streaming', [
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':60,1', // 60 requests per minute
        ]);

        // Add secure content middleware to web group
        $middleware->web(append: [
            \App\Http\Middleware\SecureContent::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Custom exception handling for streaming
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, $request) {
            if ($request->is('api/secure-stream/*') || $request->is('api/video/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many requests. Please try again later.'
                ], 429);
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) {
            if ($request->is('api/secure-stream/*') || $request->is('api/video/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this content.'
                ], 403);
            }
        });
    })->create();