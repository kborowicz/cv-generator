<?php

use App\Core\Router\RouteCollection;

$routes = new RouteCollection();

$routes->add('home', "/")
    ->setController(\App\Controller\HomeController::class)
    ->setAction('home');

$routes->add('login', "login/")
    ->setController(\App\Controller\AuthController::class)
    ->setAction('login');

$routes->add('signup', "signup/")
    ->setController(\App\Controller\AuthController::class)
    ->setAction('signup');

$routes->add('logout', "logout/")
    ->setController(\App\Controller\AuthController::class)
    ->setAction('logout');

return $routes;