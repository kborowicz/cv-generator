<?php 

namespace App\Core\Router;

use \App\Core\Http\Response;

class Router {

    private RouteCollection $routes;

    private Route $route;

    private array $params = [];

    public function __construct(RouteCollection $routes) {
        $this->routes = $routes;
    }

    public function matches($url) {
        //TODO Wywalić błąd o konflikcie pomiędzy wyrażeniami regularnymi dla różnych ścieżek np:
        //TODO /login/new/
        //TODO /login/{userId:\d+}/

        foreach($this->routes->getAll() as $route) {
            if($params = $route->matches($url)) {
                $this->route = $route;
                $this->params = [];

                foreach ($params as $key => $value) {
                    if(is_string($key)) {
                        $this->params[$key] = $value;
                    }
                }

                return true;
            }
        }

        return false;
    }

    public function run($url) {
        if($this->matches($url)) {
            if(!empty($this->route->getController())) {
                $this->processController();
            } else {
                throw new \Exception('Controller class is not defined');
            }
        } else {
            http_response_code(404);
            exit;
        }
    }

    private function processController() {
        if(class_exists($this->route->getController(), true)) {
            $controllerClass = $this->route->getController();
            $controller = new $controllerClass();
            $action = $this->route->getAction();

            if(empty($action)) {
                throw new \Exception('Action is not defined');
            }

            if(method_exists($controller, $action) && is_callable([$controller, $action])) {
                //TODO posortować kolejność argumentów przed call user func array (użyć ReflectionMethod)
                $controller->before($action);
                $result = call_user_func_array([$controller, $action], $this->params);

                if($result instanceof Response) {
                    $result->send();
                }
            } else {
                throw new \Exception("Method '$controller:$action' does not exist");
            }
        } else {
            throw new \Exception("Controller class '" . $this->route->getController() . "' does not exist");
        }
    }

    public function getRoutes() : RouteCollection {
        return $this->routes;
    }

    public function getRoute() : Route { 
        return $this->route;
    }
    
}