<?php 

namespace App\Core\Router;

use \App\Core\Http\Response;
use \App\Core\Controller;
use BadMethodCallException;

class Router {

    private RouteCollection $routes;

    private Route $route;

    private array $parameters;

    public function __construct(RouteCollection $routes) {
        $this->routes = $routes;
    }

    //TODO Wyrzucić sprawdzanie konfliktów pomiędzy ścieżkami
    public function matches($url) {
        $matches = [];

        foreach($this->routes->getAll() as $route) {
            if($parameters = $route->matches($url)) {
                $matches[] = [
                    'route' => $route,
                    'parameters' => array_filter($parameters, function($key) { 
                        return is_string($key);
                    })
                ];
            }
        }

        $matchesCount = count($matches);

        if($matchesCount > 0) {
            if($matchesCount == 1) {
                $this->route = $matches[0]['route'];
                $this->parameters = $matches[0]['parameters'];

                return true;
            } else {
                $routes = implode(', ', array_map(function ($match) {
                    return "'" . $match['route']->getName() . "'";
                }, $matches));
                throw new \Exception("Routes conflict, $matchesCount routes 
                    matches specified url ($routes)");
            }
        } else {
            return false;
        }
    }

    public function run($url) {
        if($this->matches($url)) {
            $controllerClass = $this->route->getController();
            $controllerAction = $this->route->getAction();

            if(empty($controllerClass)) {
                throw new \Exception('Controller class is not defined');
            }

            if(empty($controllerAction)) {
                throw new \Exception('Controller action is not defined');
            }

            $this->processController($controllerClass, $controllerAction);
        } else {
            http_response_code(404);
            exit;
        }
    }

    private function processController($class, $action) {
        if(class_exists($class, true)) {
            $controller = new $class();

            if(!($controller instanceof Controller)) {
                throw new \Exception('Controller must be an instance of \App\Core\Controller class');
            }

            $reflectionMethod = new \ReflectionMethod($controller, $action);
            $givenParameters = $this->parameters;
            $sortedParameters = array_map(function($param) use ($givenParameters) {
                $name = $param->getName();
                
                if(array_key_exists($name, $givenParameters)) {
                    return $givenParameters[$name];
                } else {
                    if($param->isOptional()) {
                        return $param->getDefaultValue();
                    } else {
                        throw new BadMethodCallException("Argument '$name' is mandatory");
                    }
                }
            }, $reflectionMethod->getParameters());

            $controller->before($action);
            $result = $reflectionMethod->invokeArgs($controller, $sortedParameters);

            if($result instanceof Response) {
                $result->send();
            }
        } else {
            throw new \Exception("Controller class '$class' does not exist");
        }
    }

    public function getRoutes() : RouteCollection {
        return $this->routes;
    }

    public function getRoute() : Route { 
        return $this->route;
    }
    
}