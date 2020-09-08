<?php

namespace App\Controller;

use App\Core\View;
use App\Core\Controller;

class AuthController extends Controller {

    public function before($action): void {
        session_start();

        if (isset($_SESSION[USER_ID])) {
            $this->redirectTo('base');
        }
    }

    public function login() {
        $view = new View('login.phtml', [
            'pageTitle' => 'CV Generator | Login',
            'topnavBg'  => '#343a40',
        ]);

        $view->render();
    }

}