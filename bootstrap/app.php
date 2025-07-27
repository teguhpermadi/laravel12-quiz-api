<?php

use App\Http\Middleware\ProcessApiQueryParameters;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Exceptions\UnauthorizedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Tambahkan ini untuk menangani UnauthorizedException
        $exceptions->renderable(function (UnauthorizedException $e, $request) {
            if ($request->is('api/*')) { // Hanya untuk request API
                return new JsonResponse([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'code' => 403 // Kode status kustom jika Anda mau
                ], 403);
            }
        });
    })->create();
