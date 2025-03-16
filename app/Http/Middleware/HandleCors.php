<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleCors
{
    /**
     * Обрабатывает входящий запрос и добавляет CORS-заголовки.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Разрешаем доступ с конкретных доменов
        $allowedOrigins = ['http://localhost', 'http://localhost:8000']; // Укажите ваш домен, например http://localhost

        // Проверка, если запрос идет с разрешенного домена
        $origin = $request->headers->get('Origin');
        if (in_array($origin, $allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        }

        // Разрешаем методы
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

        // Разрешаем необходимые заголовки
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

        // Разрешаем отправку cookies
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        // Обработка preflight запросов (OPTIONS)
        if ($request->getMethod() == 'OPTIONS') {
            $response->setStatusCode(200);
        }

        return $response;
    }
}
