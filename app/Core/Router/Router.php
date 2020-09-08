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
            }
        } else {
            //TODO dorobic cos w rodzaju domyślnej ścieżki tzn {controller}/{action}/... params 
            //TODO Jeżeli sciezka i tak nie zostanie znaleziona to przekierowanie zdefiniowane w route (jeszcze nie zrobione) albo 404
            throw new \Exception('No match 404');
        }
    }

    private function processController() {
        if(class_exists($this->route->getController(), true)) {
            $controllerClass = $this->route->getController();
            $controller = new $controllerClass();
            $action = $this->route->getAction();

            if(!empty($action)) {
                if(method_exists($controller, $action) && is_callable([$controller, $action])) {
                    //TODO uwzględnić $router defaults
                    //TODO posortować kolejność argumentów przed call user func array (użyć ReflectionMethod)
                    $controller->before($action);
                    $result = call_user_func_array([$controller, $action], $this->params);

                    if($result instanceof Response) {
                        $result->send();
                    }
                } else {
                    throw new \Exception("Method '$action' does not exist");
                    //TODO 404 albo przekierowanie zdefiniowane w route
                }
            } else {
                throw new \Exception('action not defined');
                //TODO 404 albo przekierowanie zdefiniowane w route
            }
        } else {
            throw new \Exception("Controller class '" 
                . $this->route->getController() . "' does not exist");
        }
    }

    public function getRoutes() : RouteCollection {
        return $this->routes;
    }

    public function getRoute() : Route { 
        return $this->route;
    }
    
}