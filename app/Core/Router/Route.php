<?php

namespace App\Core\Router;

final class Route {

    private static array $METHODS = ['GET', 'POST', 'PUT', 'DELETE'];

    private static string $PREFIX = '';

    private string $name;

    private string $pattern;

    private string $regex;

    private array $methods = [];

    private array $parameters = [];

    private array $defaults = [];

    public function __construct(string $name, string $pattern, array $methods = null) {
        $this->setName($name);
        $this->setPattern($pattern);

        if($methods) {
            $this->setMethods($methods);
        }
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): self {
        if (empty($name)) {
            throw new \Exception('Name cannot be empty');
        }

        $this->name = $name;

        return $this;
    }

    public function getPattern(): string {
        return $this->pattern;
    }

    public function setPattern(string $pattern): self {
        if (empty($pattern)) {
            throw new \Exception('Pattern cannot be empty');
        }

        $pattern = trim($pattern, '/');
        $variableRegex = '/\{([a-zA-Z0-9]+):?([^\}]+)?\}/'; //e.g. /page1/page2/{a}/{b:\d+}

        // Split pattern by parameter regex
        preg_match_all($variableRegex, $pattern, $matches);
        $urlParts = preg_split($variableRegex, $pattern);

        // Validate pattern
        if (count($urlParts) - 1 != count($matches[0])) {
            throw new \Exception('Invalid pattern');
        }

        // Remove custom regex from pattern string: {a:[abc]+}, {b:\d+} => {a}, {b}
        $this->pattern = preg_replace($variableRegex, '{\1}', $pattern);
        $this->regex = '';

        // Build regex and parameters array
        for ($i = 0; $i < count($matches[0]); $i++) {
            $parameterName = $matches[1][$i];
            $parameterRegex = !empty($matches[2][$i]) ? $matches[2][$i] : '[a-zA-Z0-9._=+#\-\^]+';

            $this->parameters[$parameterName] = $parameterRegex;
            $this->regex .= preg_quote($urlParts[$i], '/') . "(?<$parameterName>$parameterRegex)";
        }

        $this->regex .= preg_quote($urlParts[count($urlParts) - 1], '/');
        $this->regex = rtrim($this->regex, '\/') . '\/?';
        $this->regex = '/^' . $this->regex . '$/';

        return $this;
    }

    public function getRegex(): string {
        return $this->regex;
    }

    public function getMethods() {
        return $this->methods;
    }

    public function setMethod(string $method, string $controller, string $action) {
        $method = strtoupper($method);

        if(!in_array($method, self::$METHODS)) {
            if($method == 'ANY') {
                foreach(self::$METHODS as $method) {
                    $this->methods[$method] = [
                        'controller' => $controller,
                        'action' => $action
                    ];
                }
            } else {
                throw new \Exception("Uknnown request method '$method'");
            }
        } else if(empty($controller)) {
            throw new \Exception('Controller class name cannot be empty');
        } else if(empty($action)) {
            throw new \Exception('Controller action name cannot be empty');
        }

        $this->methods[$method] = [
            'controller' => $controller,
            'action' => $action
        ];

        return $this;
    }

    public function setMethods(array $methods) {
        foreach ($methods as $method => $settings) {
            $this->setMethod($method, $settings['controller'] ?? null, $settings['action'] ?? null);
        }

        return $this;
    }

    private function canBeString($value): bool {
        return is_scalar($value) || (is_object($value) && method_exists($value, '__toString'));
    }

    public function getDefaults(): array{
        return $this->defaults;
    }

    public function setDefaults(array $defaults): self {
        foreach ($defaults as $key => $value) {
            if (array_key_exists($key, $this->parameters)) {
                if (!$this->canBeString($value)) {
                    throw new \Exception("Default value of '$key' cannot be converted to string");
                }

                if (!preg_match('/' . $this->parameters[$key] . '/', (string) $value)) {
                    throw new \Exception("Default value of '$key' does not match the regex pattern");
                }
            } else {
                throw new \Exception("Parameter '$key' is not present in pattern");
            }
        }

        $this->defaults = $defaults;

        return $this;
    }

    public function getUrl($parameters = [], $absolute = false) {
        $absolutePrefix = '';

        if ($absolute) {
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                $absolutePrefix .= 'https://' . $_SERVER['HTTP_HOST'];
            } else {
                $absolutePrefix .= 'http://' . $_SERVER['HTTP_HOST'];
            }
        }

        if (count($this->parameters) == 0) {
            return $absolutePrefix . '/' . self::$PREFIX . '/' . $this->pattern;
        }

        $replacePairs = [];

        foreach ($this->parameters as $param => $pattern) {
            if (array_key_exists($param, $parameters)) {
                if (preg_match('/' . $pattern . '/', $parameters[$param])) {
                    $replacePairs['{' . $param . '}'] = $parameters[$param];
                } else {
                    throw new \Exception("Value of '$param' does not match the regex pattern");
                }
            } else if (array_key_exists($param, $this->defaults)) {
                $replacePairs['{' . $param . '}'] = $this->defaults[$param];
            } else {
                throw new \Exception("Parameter '$param' is required, but not found in parameters array");
            }
        }

        if (count($replacePairs) != count($this->parameters)) {
            throw new \Exception('Too few parameters');
        }

        $url = strtr($this->pattern, $replacePairs);

        return $absolutePrefix . '/' . self::$PREFIX . '/' . $url;
    }

    public function matches($url) {
        preg_match($this->regex, $url, $matches);

        return $matches ?? false;
    }

    public static function setPrefix(string $prefix) {
        self::$PREFIX = trim($prefix, '/');
    }

}