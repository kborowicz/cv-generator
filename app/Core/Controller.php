<?php

namespace App\Core;

class Controller {

    /**
     * Method called before action method specified in route
     *
     * @param [type] $action
     * @return void
     */
    public function before($action): void { }

    protected function redirectTo($routeName, $params = []) {
        $route = \App\App::getRouter()->getRoutes()->get($routeName);
        header('Location:' . $route->getUrl($params));
        exit;
    }

}
