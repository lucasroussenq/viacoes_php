<?php
declare(strict_types=1);


/*
arquivo que o servidor web enxerga;
URl do site chega aqui primeiro pelo .htaccess
inicia a sessão, carrega o autoloader e o banco cria o Router, registra as rotas e chama dispatch()

*/
$uri  = (string) ($_SERVER['REQUEST_URI'] ?? '/');
$path = parse_url($uri, PHP_URL_PATH);
$path = is_string($path) ? $path : '/';

if (!str_starts_with($path, '/api')) {
    session_start(); //inicia a sessão
}

//carrega as classes automaticamente:
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../database/db.php';

use App\Core\Router;


//cria o roteador e registra as rotas:
$router = new Router();

require __DIR__ . '/../routes/web.php';
require __DIR__ . '/../routes/api.php';

$method = (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET');

//encontra qual controller responder
$router->dispatch($method, $uri);