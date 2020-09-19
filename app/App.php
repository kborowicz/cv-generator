<?php

namespace App;

use App\Core\Router\Router;
use Doctrine\ORM\EntityManager;

final class App {

    private static App $instance;

    private static Router $router;

    private static EntityManager $entityManager;

    private function __construct() { }

    private function __clone() { }

    public static function start() {
        if(isset(self::$instance)) {
            return;
        }

        require __DIR__ . '/config/constants.inc.php';

        self::$entityManager = require __DIR__ . '/config/database.inc.php';
        self::$router = new Router(require __DIR__ . '/config/routes.inc.php');
        self::$router->run($_GET['url'] ?? '/');

        date_default_timezone_set('Europe/Warsaw');
    }

    public static function getEntityManager(): EntityManager {
        return self::$entityManager;
    }

    public static function getRouter(): Router {
        return self::$router;
    }

}