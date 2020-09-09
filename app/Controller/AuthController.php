<?php

namespace App\Controller;

use App\Core\View;
use App\Core\Controller;
use App\Model\Entity\User;
use Doctrine\ORM\ORMException;

class AuthController extends Controller {

    public function before($action): void {
        session_start();

        if($action !== 'logout') {
            if (isset($_SESSION[USER_ID])) {
                $this->redirectTo('home');
            }
    
            if (!isset($_SESSION[CSRF_TOKEN])) {
                $_SESSION[CSRF_TOKEN] = bin2hex(random_bytes(32));
            }
        }
    }

    public function login() {
        $this->view = new View('login.phtml', [
            'pageTitle' => 'CV Generator | Log in',
            'topnavBg'  => '#343a40',
        ]);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->actionLogin();
        } else {
            $this->view->render();
        }
    }

    public function actionLogin() {
        if (empty($_POST[CSRF_TOKEN]) || $_POST[CSRF_TOKEN] !== $_SESSION[CSRF_TOKEN]) {
            $this->redirectTo('login');
        }

        if (empty($_POST['email'])) {
            $this->view->render(['emailError' => 'Email is required']);

            return;
        } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $this->view->render([
                'email'      => $_POST['email'],
                'emailError' => 'Email is invalid',
            ]);

            return;
        } else if (empty($_POST['password'])) {
            $this->view->render([
                'email'         => $_POST['email'],
                'passwordError' => 'Password is required',
            ]);

            return;
        }

        $em = \App\App::getEntityManager();
        $usersRepo = $em->getRepository(User::class);
        $user = $usersRepo->findOneBy(['email' => $_POST['email']]);

        if (!$user) {
            $this->view->render([
                'email'      => $_POST['email'],
                'emailError' => 'Email does not exist',
            ]);

            return;
        }

        if (!password_verify($_POST['password'], $user->getPassword())) {
            $this->view->render([
                'email'         => $_POST['email'],
                'passwordError' => 'Incorrect password',
            ]);

            return;
        }

        $_SESSION[USER_ID] = $user->getId();
        $_SESSION[CSRF_TOKEN] = bin2hex(random_bytes(32));

        $this->redirectTo('home');
    }

    public function signup() {
        $this->view = new View('signup.phtml', [
            'pageTitle' => 'CV Generator | Sign up',
            'topnavBg'  => '#343a40',
        ]);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->actionSignup();
        } else {
            $this->view->render();
        }
    }

    public function actionSignup() {
        if (empty($_POST['name'])) {
            $this->view->render(['nameError' => 'Name is required']);

            return;
        } else if (empty($_POST['lastname'])) {
            $this->view->render([
                'name'          => $_POST['name'],
                'lastnameError' => 'Lastname is required',
            ]);

            return;
        } else if (empty($_POST['email'])) {
            $this->view->render([
                'name'       => $_POST['name'],
                'lastname'   => $_POST['lastname'],
                'emailError' => 'Email is required',
            ]);

            return;
        } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $this->view->render([
                'name'       => $_POST['name'],
                'lastname'   => $_POST['lastname'],
                'emailError' => 'Email is invalid',
            ]);

            return;
        } else if (empty($_POST['password'])) {
            $this->view->render([
                'name'          => $_POST['name'],
                'lastname'      => $_POST['lastname'],
                'email'         => $_POST['email'],
                'passwordError' => 'Password is required',
            ]);

            return;
        } else if (empty($_POST['password-confirm'])) {
            $this->view->render([
                'name'                 => $_POST['name'],
                'lastname'             => $_POST['lastname'],
                'email'                => $_POST['email'],
                'passwordConfirmError' => 'Password confirmation is required',
            ]);

            return;
        } else if ($_POST['password'] !== $_POST['password-confirm']) {
            $this->view->render([
                'name'                 => $_POST['name'],
                'lastname'             => $_POST['lastname'],
                'email'                => $_POST['email'],
                'passwordConfirmError' => 'Password confirmation failed',
            ]);

            return;
        }

        $em = \App\App::getEntityManager();
        $usersRepo = $em->getRepository(User::class);
        $user = $usersRepo->findOneBy(['email' => $_POST['email']]);

        if ($user) {
            $this->view->render([
                'name'       => $_POST['name'],
                'lastname'   => $_POST['lastname'],
                'emailError' => 'Account with this Email already exists',
            ]);

            return;
        }

        $user = new User();
        $user->setEmail($_POST['email']);
        $user->setName($_POST['name']);
        $user->setLastname($_POST['lastname']);
        $user->setPassword(password_hash($_POST['password'], PASSWORD_DEFAULT));
        $user->setBirthDate(new \DateTime());
        $user->setAdressStreet('[Street]');
        $user->setAdressHouseNumber('[House number]');
        $user->setAdressZipCode('[Zip code]');
        //$user->setAdressTown('[Town]');

        try {
            $em->persist($user);
            $em->flush();

            $_SESSION[USER_ID] = $user->getId();
            $_SESSION[CSRF_TOKEN] = bin2hex(random_bytes(32));

            $this->redirectTo('home');
        } catch (ORMException $e) {
            $this->view->render([
                'name'                 => $_POST['name'],
                'lastname'             => $_POST['lastname'],
                'email'                => $_POST['lastname'],
                'passwordConfirmError' => 'Database error, try again later',
            ]);
        }
    }

    public function logout() {
        session_start();

        if (isset($_SESSION[USER_ID])) {
            unset($_SESSION[USER_ID]);
        }

        if (isset($_SESSION[CSRF_TOKEN])) {
            unset($_SESSION[CSRF_TOKEN]);
        }

        $this->redirectTo('login');
    }

}