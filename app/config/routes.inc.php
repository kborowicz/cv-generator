<?php

use App\Core\Router\Route;
use App\Controller\AuthController;
use App\Controller\HomeController;
use App\Core\Router\RouteCollection;

Route::setPrefix('cv-generator');
$routes = new RouteCollection();

/* Auth routes */

$routes->add('login', "login/")
->setMethod('GET', AuthController::class, 'login')
->setMethod('POST', AuthController::class, 'processLogin');

$routes->add('signup', "signup/")
->setMethod('GET', AuthController::class, 'signup')
->setMethod('POST', AuthController::class, 'processSignup');

$routes->add('logout', "logout/")
->setMethod('GET', AuthController::class, 'logout');

/* Home routes */

$routes->add('home', "/")
->setMethod('ANY', HomeController::class, 'home');

$routes->add('generate', "generate/{name}.{lastname}.{id}/")
->setMethod('GET', HomeController::class, 'generateCV');

$routes->add('open-image', "image/{imageFile}/")
->setMethod('GET', HomeController::class, 'openImage');

/* Home AJAX routes */

$routes->add('ajax-get-data', "get-data/")
->setMethod('GET', HomeController::class, 'getData');

$routes->add('ajax-save-data', "save-data/")
->setMethod('POST', HomeController::class, 'saveData');

$routes->add('ajax-upload-image', "upload-image/")
->setMethod('POST', HomeController::class, 'uploadImage');

return $routes;