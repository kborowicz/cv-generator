<?php

namespace App\Controller;

use App\Core\View;
use App\Core\Controller;
use App\Model\Entity\User;
use App\Service\Form\Form;
use App\Service\Form2\Form as Form2;
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
        $loginField = $form->addField('email')->notEmpty()->validEmail();
        $passwordField = $form->addField('password')->notEmpty();

        /* Get user data from database */
        $entityManager = \App\App::getEntityManager();
        $usersRepo = $entityManager->getRepository(User::class);
        $user = $usersRepo->findOneBy(['email' => $form->getValueOf('email')]);

        /* Add rest of field constraints */
        $loginField->addConstraint(function () use ($user) {
            if (!$user) {
                return 'Account does not exist';
            }
        });

        $passwordField->addConstraint(function ($value) use ($user) {
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

        // Create form //TODO csrf token
        $form2 = new Form2('post');
        $form2->addField('name')->addRule('required');
        $form2->addField('lastname')->addRule('required');
        $form2->addField('email')->addRule('email')
            ->addRule(function($value) use ($usersRepo) {
                return $usersRepo->findOneBy(['email' => $value]) != null; //TODO coś tu nie działa
            }, 'User with this email already exists');

        $form2->addField('password')->addRule('length', [5], 'Password must be at least 5 characters');
        $form2->addField('confirm_password')->addRule('equals', ['password'], 'Passwords does not match');

        // If validation fails then render signup view with errors
        if(!$form2->validate()) {
            $view = new View('signup.phtml');

            $view->assign(['topnavBg'  => '#ffffff', 'pageTitle' => 'CV Generator | Sign up']);
            $view->assign($form2->getFieldValues());
            $view->assign($form2->getErrors());
            $view->render();

            return;
        }

        // Create new user and redirect to home page
        $user = new User();
        $user->setEmail($form2->getFieldValue('email'));
        $user->setName($form2->getFieldValue('name'));
        $user->setLastname($form2->getFieldValue('lastname'));
        $user->setPassword(password_hash($form2->getFieldValue('password'), PASSWORD_DEFAULT));
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