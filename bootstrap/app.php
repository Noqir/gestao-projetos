<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Proxies confiáveis: repassam HTTPS e o endereço público nos cabeçalhos X-Forwarded-*.
        // Padrão: só o próprio computador (Cloudflare Tunnel rodando nesta máquina).
        // Na hospedagem (Railway), TRUSTED_PROXIES=* porque todo acesso passa pelo proxy dela.
        $middleware->trustProxies(at: env('TRUSTED_PROXIES', '127.0.0.1,::1'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
