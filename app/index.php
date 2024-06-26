<?php
require_once '../vendor/autoload.php';
require_once 'controllers/ProductoController.php';
require_once 'controllers/VentaController.php';

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$app = AppFactory::create();

// Set base path
$app->setBasePath('/Parcial2Prog3/app/index.php');

// Añadiendo middleware para manejo de errores en modo desarrollo
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
// Grupo de Rutas para "tienda"
$app->group('/tienda', function ($group) {
    $group->post('/alta', \ProductoController::class . ':alta');
    $group->post('/consultar', \ProductoController::class . ':consultar');
    $group->get('/consultar/productos/entreValores', \ProductoController::class . ':productosEntreValores');
});

// Grupo de Rutas para "ventas"
$app->group('/ventas', function ($group) {
    $group->post('/alta', \VentaController::class . ':alta');
    $group->get('/consultar/vendidos', \VentaController::class . ':productosVendidos');
    $group->get('/consultar/ventas/porUsuario', \VentaController::class . ':ventasPorUsuario');
    $group->get('/consultar/ventas/porProducto', \VentaController::class . ':ventasPorProducto');
    $group->get('/consultar/ventas/ingresos', \VentaController::class . ':ingresosPorDia');
    $group->get('/consultar/productos/masVendido', \VentaController::class . ':productoMasVendido');
    $group->put('/modificar', \VentaController::class . ':modificar'); //no está hecho todavía :D
});

$app->run();
