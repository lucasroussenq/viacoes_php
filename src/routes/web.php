<?php

declare(strict_types=1);

/** Arquivo de registro de rotas web. */
use App\Controllers\TaskController;
use App\Controllers\ViacaoController;
use App\Controllers\HomeController;
use App\Controllers\UsuarioController;


/** @var App\Core\Router $router */

$router->get('/', [TaskController::class, 'index']);
$router->get('/tasks', [TaskController::class, 'index']);

$router->get('/tasks/create', [TaskController::class, 'create']);
$router->post('/tasks', [TaskController::class, 'store']);
$router->get('/tasks/{id}/edit', [TaskController::class, 'edit']);
$router->post('/tasks/{id}', [TaskController::class, 'update']);
$router->post('/tasks/{id}/delete', [TaskController::class, 'destroy']);

$router->get('/viacoes', [ViacaoController::class, 'index']);
$router->get('/viacoes/create', [ViacaoController::class, 'create']);
$router->get('/viacoes/historico', [ViacaoController::class, 'historico']);
$router->post('/viacoes', [ViacaoController::class, 'store']);

$router->get('/viacoes/{id}/edit', [ViacaoController::class, 'edit']);
$router->put('/viacoes/{id}', [ViacaoController::class, 'update']);
$router->post('/viacoes/{id}/delete', [ViacaoController::class, 'destroy']);

$router->get('/home', [HomeController::class, 'index']);

$router->post('/login', [UsuarioController::class, 'login']);
$router->get('/downgrade-acesso', [UsuarioController::class, 'downgrade']);
$router->get('/upgrade-acesso', [UsuarioController::class, 'upgrade']);