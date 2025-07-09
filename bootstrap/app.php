<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Dotenv\Dotenv;

//
// 🔹 Carrega variáveis do .env
//
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 🔹 Aqui você pode adicionar middleware globais ou por grupo, se necessário
        // Exemplo para adicionar middleware global:
        // $middleware->append(\App\Http\Middleware\ExampleMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 🔹 Aqui você pode configurar manipuladores de exceções personalizados, se necessário
        // Exemplo:
        // $exceptions->reportable(function (Throwable $e) {
        //     //
        // });
    })
    ->create();
