<?php

namespace App\Core\Router;

class RouteCollection {

    protected $routes = [];

    public function add(string $name, string $pattern, $methods = null) : Route {
        if($this->contains($name)) {
            throw new \Exception("Route with name '$name' already exists");
        }

        $route = new Route($name, $pattern, $methods);
        $this->routes[$name] = $route;

        return $route;
    }

    public function remove(string $routeName) : bool {
        if($this->contains($routeName)) {
            unset($this->routes[$routeName]);
        }

        return false;
    }

    public function contains($routeName) : bool {
        return array_key_exists($routeName, $this->routes);
    }

    public function get($routeName) : Route {
        if($this->contains($routeName)) {
            return $this->routes[$routeName];
        } else {
            throw new \Exception("Route '$routeName' not found");
        }
    }

    public function getAll() : array {
        return $this->routes;
    }
    
    public function print() : void {
        //TODO 
    }

}