<?php

namespace App\Core\Router;

//TODO trzeba dorobić coś w stylu kolejności bo może sie zdarzyc sytuacja:
// route: /login        => controller od logowania
// route: /{project}    => controller od projektów
//TODO wtedy trzeba wybrać w jakiej kolejności zrobić przeszukiwanie


class Route {

    protected string $name;

    protected string $pattern;

    protected int $paramsCount;

    protected string $regex;

    protected string $controller;

    protected string $action;
    
    protected array $defaults;

    public function __construct(string $name, string $pattern) {
        $this->setName($name);
        $this->setPattern($pattern);
    }

    public function getName() : string {
        return $this->name;
    }

    public function setName(string $name) : self {
        if(empty($name)) {
            throw new \Exception('Route name cannot be empty');
        }

        $this->name = $name;
        return $this;
    }

    public function getPattern() : string {
        return $this->pattern;
    }

    public function setPattern(string $pattern) : self {
        if(empty($pattern)) {
            throw new \Exception('Route pattern cannot be empty');
        }

        //TODO porobic zabezpieczenia ze znakami unikalnymi z regexa zeby np mozna bylo uzywac kropki

        $this->pattern = $pattern;

        $this->regex = rtrim($pattern, '/') . '/?';
        //Convert the route to a regular expression: escape forward slashes
        $this->regex = preg_replace('/\//', '\\/', $this->regex);
        // Convert variables e.g. {controller}
        $this->regex = preg_replace('/\{([a-zA-Z]+)\}/', '(?<\1>[a-zA-Z0-9_.~\-]+)', $this->regex);
        // Convert variables with custom regular expressions e.g. {id:\d+}
        $this->regex = preg_replace('/\{([a-zA-Z]+):([^\}]+)\}/', '(?<\1>\2)', $this->regex);
        // Add start and end delimiters, and case insensitive flag
        $this->regex = '/^' . $this->regex . '$/i';
        
        return $this;
    }

    public function getRegex() : string {
        return $this->regex;
    }

    public function getController() : string {
        return $this->controller;
    }

    public function setController(string $controller) : self {
        $this->controller = $controller;
        return $this;
    }

    public function getAction() : string {
        return $this->action;
    }

    public function setAction(string $action) : self {
        $this->action = $action;
        return $this;
    }

    public function getDefaults() : array {
        return $this->defaults;
    }

    public function setDefaults(array $defaults) : self {
        $this->defaults = $defaults;
        return $this;
    }

    public function matches($url) {
        preg_match($this->regex, $url, $matches);

        return $matches ?? false;
    }

    //TODO zoptymalizować, np nie wykonywać regexów jeżeli liczba parametrów jest 0
    public function getUrl($params = []) {
        $url = $this->pattern;

        preg_match_all('/\{([a-z]+)\}/', $this->pattern, $matches);
        if($matches) {
            foreach($matches[1] as $index => $groupMatch) {
                if(array_key_exists($groupMatch, $params)) {
                    $url = str_replace($matches[0][$index], $params[$groupMatch], $url);
                } else {
                    //TODO sprawdzanie wartości domyślnych ?
                }
            }
        }

        preg_match_all('/\{([a-z]+):([^\}]+)\}/', $this->pattern, $matches);
        if($matches) {
            foreach($matches[1] as $index => $groupMatch) {
                if(array_key_exists($groupMatch, $params)) {
                    $url = str_replace($matches[0][$index], $params[$groupMatch], $url);
                } else {
                    //TODO sprawdzanie wartości domyślnych ?
                }
            }
        }

        //TODO potem zmienic ten prefix na poprostu roota /
        if($this->matches($url)) {
            return '/cv-generator/' . ltrim($url, '/');
        } else {
            throw new \Exception("Specified parameters do not match route pattern ($url)");
        }
    }

}