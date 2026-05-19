<?php

declare(strict_types=1);

/** Arquivo de registro de rotas web. */
use App\Controllers\ViacaoController;
use App\Controllers\HomeController;
use App\Controllers\AutenticadorController;
use App\Controllers\SetupController;

/** @var App\Core\Router $router */

// -----------------------------------------------------------------------------
// Setup — cria o primeiro usuário quando o banco está vazio.
// A rota se bloqueia automaticamente (403) assim que qualquer usuário existir.
// Remova ou comente estas linhas após o primeiro deploy em produção.
// -----------------------------------------------------------------------------
$router->get('/setup',  [SetupController::class, 'index']);
$router->post('/setup', [SetupController::class, 'criar']);

// Autenticação
$router->get('/login',   [AutenticadorController::class, 'login']);
$router->post('/login',  [AutenticadorController::class, 'autenticar']);
$router->get('/logout',  [AutenticadorController::class, 'logout']);

// Home pública
$router->get('/home', [HomeController::class, 'index']);

// CRUD de viações (área protegida — requer login)
$router->get('/viacoes',           [ViacaoController::class, 'index']);
$router->get('/viacoes/create',    [ViacaoController::class, 'create']);
$router->get('/viacoes/historico', [ViacaoController::class, 'historico']);
$router->post('/viacoes',          [ViacaoController::class, 'store']);
$router->get('/viacoes/{id}/edit', [ViacaoController::class, 'edit']);
$router->put('/viacoes/{id}',      [ViacaoController::class, 'update']);
$router->post('/viacoes/{id}/delete', [ViacaoController::class, 'destroy']);