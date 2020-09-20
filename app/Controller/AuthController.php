<?php

namespace App\Controller;

use App\Core\View;
use App\Core\Controller;
use App\Model\Entity\User;
use App\Service\Form\Form;
use Doctrine\ORM\ORMException;

class AuthController extends Controller {

    public function before($action): void {
        session_start();

        if ($action !== 'logout') {
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
            'topnavBg'  => '#ffffff',
            'pageTitle' => 'CV Generator | Log in',
        ]);

        $this->view->render();
    }

    public function signup() {
        $this->view = new View('signup.phtml', [
            'topnavBg'  => '#ffffff',
            'pageTitle' => 'CV Generator | Sign up',
        ]);

        $this->view->render();
    }

    public function processLogin() {
        if (empty($_POST[CSRF_TOKEN]) || $_POST[CSRF_TOKEN] !== $_SESSION[CSRF_TOKEN]) {
            $this->redirectTo('login');
        }

        /* Create form */
        $form = new Form('POST');
        $loginField = $form->addField('email')->setNotEmpty()->setWithValidEmail();
        $passwordField = $form->addField('password')->setNotEmpty();

        /* Get user data from database */
        $entityManager = \App\App::getEntityManager();
        $usersRepo = $entityManager->getRepository(User::class);
        $user = $usersRepo->findOneBy(['email' => $form->getValueOf('password')]);

        /* Add rest of field constraints */
        $loginField->setWithConstraint(function () use ($user) {
            if (!$user) {
                return 'Account does not exist';
            }
        });

        $passwordField->setWithConstraint(function ($isEmpty, $name, $value) use ($user) {
            if ($user && !password_verify($value, $user->getPassword())) {
                return 'Incorrect password';
            }
        });

        /* Validate form fields */
        if (!$form->validate()) {
            $view = new View('login.phtml');

            $view->assign([
                'topnavBg'  => '#ffffff',
                'pageTitle' => 'CV Generator | Log in',
            ]);
            $view->assign($form->getFieldValues());
            $view->assign($form->getErrors());
            $view->render();

            return;
        }

        /* Set user session variables and redirect to home page */
        $_SESSION[USER_ID] = $user->getId();
        $_SESSION[CSRF_TOKEN] = bin2hex(random_bytes(32));

        $this->redirectTo('home');
    }

    public function processSignup() {
        if (empty($_POST[CSRF_TOKEN]) || $_POST[CSRF_TOKEN] !== $_SESSION[CSRF_TOKEN]) {
            $this->redirectTo('signup');
        }

        $entityManager = \App\App::getEntityManager();
        $usersRepo = $entityManager->getRepository(User::class);

        /* Create form */
        $form = new Form('POST');
        $form->addField('name')->setNotEmpty();
        $form->addField('lastname')->setNotEmpty();
        $form->addField('password')->setNotEmpty();
        $form->addField('password_confirm')->setEqualTo($form->getValueOf('password'), "Passwords must match");
        $emailField = $form->addField('email')->setWithValidEmail();
    
        /* Get users repository */
        $entityManager = \App\App::getEntityManager();
        $usersRepo = $entityManager->getRepository(User::class);

        /* Add email check */
        $emailField->setWithConstraint(function ($isEmpty, $name, $value) use ($usersRepo) {
            if ($usersRepo->findOneBy(['email' => $value])) {
                return 'Account with this email already exists';
            }
        });

        /* Validate form fields */
        if (!$form->validate()) {
            $view = new View('signup.phtml');

            $view->assign([
                'topnavBg'  => '#ffffff',
                'pageTitle' => 'CV Generator | Sign up',
            ]);
            $view->assign($form->getFieldValues());
            $view->assign($form->getErrors());
            $view->render();

            return;
        }

        /* Create new user and redirect to home page*/
        $user = new User();
        $user->setEmail($form->getValueOf('email'));
        $user->setName($form->getValueOf('name'));
        $user->setLastname($form->getValueOf('lastname'));
        $user->setPassword(password_hash($form->getValueOf('password'), PASSWORD_DEFAULT));
        $user->setBirthDate(new \DateTime());

        try {
            $entityManager->persist($user);
            $entityManager->flush();

            $_SESSION[USER_ID] = $user->getId();
            $_SESSION[CSRF_TOKEN] = bin2hex(random_bytes(32));

            $this->redirectTo('home');
        } catch (ORMException $e) {
            echo 'Databse error, try again later';
            //TODO database error
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