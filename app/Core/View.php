<?php

namespace App\Core;

class View {

    protected $file;

    protected $data;

    public function __construct(string $file, array $data = []) {
        $file = TEMPLATES_DIR . $file;

        if(!file_exists($file)) {
            throw new \Exception("Template file '$file' does not exist");
        }

        $this->file = $file;
        $this->assign($data);
    }

    public function assign(array $data) {
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }
    }

    public function assignHelper(string $name, \Closure $func) {
        $this->data[$name] = \Closure::bind($func, $this, self::class);
    }

    public function isset(string $property) : bool {
        return isset($this->data[$property]) && null !== $this->data[$property];
    }

    public function render($data = []) {
        if(!empty($data)) {
            $this->assign($data);
        }

        include $this->file;
    }

    public function renderToString($data = []) : string {
        if(!empty($data)) {
            $this->assign($data);
        }

        ob_start();
        include $this->file;
        return ob_get_clean();
    }

    public function include(string $file) {
        $file = TEMPLATES_DIR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $file);

        if(file_exists($file)) {
            include $file;
        } else {
            throw new \Exception("Template file '$file' does not exist");
        }
    }

    public function asset(string $name) {
        $path = PUBLIC_DIR . $name;

        if(!file_exists($path)) {
            return '{File not exists}';
        }

        return PUBLIC__RELATIVE_DIR . $name . '?' . rand();
    }

    public function route(string $name, $params = []) {
        return \App\App::getRouter()->getRoutes()->get($name)->getUrl($params);
    }

    public function csrf() {
        if(!isset($_SESSION[CSRF_TOKEN])) {
            return "<p style='color: red'>{Undefined token}</p>";
        }

        return '<input type="hidden" name="' . CSRF_TOKEN . '" value="' . $_SESSION[CSRF_TOKEN] . '"/>';
    }

    public function __get($name) {
        if(!isset($this->data[$name])) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
            $file = basename($backtrace['file']);
            $line = $backtrace['line'];

            echo "<p style='color: red'><b>{Undefined variable '$name'</b> ($file:$line)}</p>";
            //TODO można to zamienić na zwykłego error (exception)
            
            return null;
        } else {
            return $this->data[$name];
        }
    }

}