<?php

declare(strict_types=1);

/** Front controller do app. Recebe o request e envia para o Router. */
$uri = (string)($_SERVER['REQUEST_URI'] ?? '/');
// Remove a query string da URL para não quebrar o roteamento (ex: /tasks?id=1 vira /tasks)
$path = parse_url($uri, PHP_URL_PATH);
$path = is_string($path) ? $path : '/';

// Não inicia sessão para rotas de API (melhora performance e isolamento)
if (!str_starts_with($path, '/api')) {
	session_start();
}

// Autoload do Composer: carrega todas as classes do projeto automaticamente via PSR-4
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../database/db.php';

use App\Core\Router;

$router = new Router();

require __DIR__ . '/../routes/web.php';
require __DIR__ . '/../routes/api.php';

$method = (string)($_SERVER['REQUEST_METHOD'] ?? 'GET');

$router->dispatch($method, $uri);
