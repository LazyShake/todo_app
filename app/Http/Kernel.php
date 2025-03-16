<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Глобальные middleware, применяемые ко всем запросам.
     */
    protected $middleware = [
        // Встроенные middleware Laravel
        \Illuminate\Http\Middleware\TrustHosts::class,
        \Illuminate\Http\Middleware\TrustProxies::class,
        \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Http\Middleware\ValidatePostSize::class,
        \Illuminate\Http\Middleware\TrimStrings::class,
        \Illuminate\Http\Middleware\ConvertEmptyStringsToNull::class,

        // Наш CORS middleware (если нужно для работы с API на другом домене)
        \App\Http\Middleware\HandleCors::class,
    ];

    /**
     * Группы middleware для API и web.
     */
    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, // CSRF защита для веб-запросов
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, // Для работы с cookies в Sanctum
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Индивидуальные middleware, которые можно применять к маршрутам.
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];

    /**
     * Метод для загрузки маршрутов API.
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace('App')
             ->group(base_path('routes/api.php')); // Подключаем файл api.php для API маршрутов
    }

    /**
     * Метод для загрузки маршрутов веба.
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace('App')
             ->group(base_path('routes/web.php')); // Подключаем файл web.php для веб маршрутов
    }

    protected function bootstrappers()
{
    return array_merge(parent::bootstrappers(), [
        \Illuminate\Foundation\Bootstrap\LoadRoutes::class,
    ]);
}

    /**
     * Конструктор класса, для вызова методов mapApiRoutes и mapWebRoutes.
     */
    public function __construct()
    {
        parent::__construct();

        $this->mapApiRoutes();  // Подключаем маршруты для API
        $this->mapWebRoutes();  // Подключаем маршруты для веба
 //       $this->bootstrappers();
    }
}
