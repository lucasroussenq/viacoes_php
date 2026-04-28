<?php

declare(strict_types=1);

/** Arquivo de registro de rotas web. */
use App\Controllers\TaskController;
use App\Controllers\CoffeeBrandsController;

/** @var App\Core\Router $router */

$router->get('/', [TaskController::class, 'index']);
$router->get('/tasks', [TaskController::class, 'index']);

$router->get('/tasks/create', [TaskController::class, 'create']);
$router->post('/tasks', [TaskController::class, 'store']);

$router->get('/tasks/{id}/edit', [TaskController::class, 'edit']);
$router->post('/tasks/{id}', [TaskController::class, 'update']);
$router->post('/tasks/{id}/delete', [TaskController::class, 'destroy']);

$router->get('/viacoes', [CoffeeBrandsController::class, 'index']);

$router->get('/brands/create', [CoffeeBrandsController::class, 'create']);
$router->post('/brands', [CoffeeBrandsController::class, 'store']);

$router->get('/brands/{id}/edit', [CoffeeBrandsController::class, 'edit']);
$router->post('/brands/{id}', [CoffeeBrandsController::class, 'update']);
$router->post('/brands/{id}/delete', [CoffeeBrandsController::class, 'destroy']);
