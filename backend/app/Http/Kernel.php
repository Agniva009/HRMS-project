<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

/**
 * HRMS HTTP Kernel
 *
 * This is a reference showing where to register the UppercaseInput middleware.
 * In a full Laravel installation, extend the generated Kernel and add the
 * middleware entry to the appropriate group.
 */
class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware stack.
     *
     * These middleware run on every request.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustProxies::class,
        // \Illuminate\Http\Middleware\HandleCors::class,
        // ...

        // ⬇ Global uppercase enforcement for all incoming requests
        \App\Http\Middleware\UppercaseInput::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            // default Laravel web middleware ...
        ],

        'api' => [
            // If you prefer to apply uppercase only to API routes:
            // \App\Http\Middleware\UppercaseInput::class,
        ],
    ];
}
