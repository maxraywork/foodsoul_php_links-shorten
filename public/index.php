<?php
require __DIR__ . '/../src/config/config.php';
require __DIR__ . '/../src/Core/Autoloader.php';

use App\Controllers\ApiController;
use App\Core\Autoloader;

Autoloader::register();

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\UrlController;
use App\Core\Router;

session_start();

$router = new Router();

//index
$router->addRoute('GET', '/', [HomeController::class, 'index']);

//Login page
$router->addRoute('GET', '/login', [AuthController::class, 'showLoginView']);
$router->addRoute('POST', '/login/verify', [AuthController::class, 'showVerifyView']);

//Logout
$router->addRoute('GET', '/logout', [AuthController::class, 'logout']);

//Create link
$router->addRoute('POST', '/url/create', [UrlController::class, 'showShortLinkView']);
$router->addRoute('POST', '/url/delete', [UrlController::class, 'deleteUrl']);

//Link redirect
$router->addRoute('GET', '/{shortUrl}', [UrlController::class, 'redirectToLongUrl']);

//Api
$router->addRoute('GET', '/api/v1/url/all', [ApiController::class, 'listAllUrls']);
$router->addRoute('POST', '/api/v1/url/create', [ApiController::class, 'createUrl']);
$router->addRoute('DELETE', '/api/v1/url/{id}', [ApiController::class, 'deleteUrl']);


try {
    return $router->resolve();
} catch (Exception $e) {
    http_response_code(500);
    return "Internal Server Error: " . $e->getMessage();
}