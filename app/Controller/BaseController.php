<?php

namespace App\Controller;

use App\App;
use App\Core\View;
use App\Core\Controller;
use App\Model\Entity\User;

abstract class BaseController extends Controller {

    protected User $user;

    protected View $view;

    public function before($action): void {
        session_start();

        if (isset($_SESSION[USER_ID])) {
            $entityManager = App::getEntityManager();
            $usersRepo = $entityManager->getRepository(User::class);
            $this->user = $usersRepo->find($_SESSION[USER_ID]);

            $this->view = new View('base.phtml', [
                'pageTitle' => 'CV Generator',
                'topnavBg'  => '#343a40',
                'user'      => $this->user,
            ]);
        } else {
            $this->redirectTo('login');
        }
    }

    public function base() {
        $this->view->render();
    }

}