<?php

declare(strict_types=1);

/** Arquivo de registro de rotas web. */
use App\Controllers\ViacaoController;
use App\Controllers\HomeController;
use App\Controllers\AutenticadorController;

/** @var App\Core\Router $router */

// Autenticação
$router->get('/login',   [AutenticadorController::class, 'login']);
$router->post('/login',  [AutenticadorController::class, 'autenticar']);
$router->get('/logout',  [AutenticadorController::class, 'logout']);

// Home pública
$router->get('/home', [HomeController::class, 'index']);

// CRUD de viações (área protegida — requer login)
$router->get('/',           [ViacaoController::class, 'index']);
$router->get('/viacoes',           [ViacaoController::class, 'index']);
$router->get('/viacoes/create',    [ViacaoController::class, 'create']);
$router->get('/viacoes/historico', [ViacaoController::class, 'historico']);
$router->post('/viacoes',          [ViacaoController::class, 'store']);
$router->get('/viacoes/{id}/edit', [ViacaoController::class, 'edit']);
$router->put('/viacoes/{id}',      [ViacaoController::class, 'update']);
$router->post('/viacoes/{id}/delete', [ViacaoController::class, 'destroy']);