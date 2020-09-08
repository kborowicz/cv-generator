<?php

use App\Core\Router\RouteCollection;

$routes = new RouteCollection();

$routes->add('login', "/")
    ->setController(\App\Controller\AuthController::class)
    ->setAction('login');

return $routes;