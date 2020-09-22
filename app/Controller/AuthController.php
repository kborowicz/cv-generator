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

    private function setSessionVariables(int $userId) {
        $_SESSION[USER_ID] = $userId;
        $_SESSION[CSRF_TOKEN] = bin2hex(random_bytes(32));
    }

    public function processLogin() {
        //Create form
        $form = new Form('post');
        $loginField = $form->addField('email')->addRule('email');
        $passwordField = $form->addField('password');
        $form->addField(CSRF_TOKEN)->addRule('csrfToken', [$_SESSION[CSRF_TOKEN]]);

        // Get user data from database
        $entityManager = \App\App::getEntityManager();
        $usersRepo = $entityManager->getRepository(User::class);
        $user = $usersRepo->findOneBy(['email' => $form->getFieldValue('email')]);

        // Add rest of fields rules
        $loginField->addRule('paramsNotNull', [$user], 'Account does not exist');
        $passwordField->addRule('password', [$user != null ? $user->getPassword() : null]);

        // Validate form fields
        if (!$form->validate()) {
            $view = new View('login.phtml');

            $view->assign(['topnavBg'  => '#ffffff', 'pageTitle' => 'CV Generator | Log in']);
            $view->assign($form->getFieldValues());
            $view->assign($form->getErrors());
            $view->render();

            return;
        }

        $this->setSessionVariables($user->getId());
        $this->redirectTo('home');
    }

    public function processSignup() {
        // Create form
        $form = new Form('post');
        $form->addField('name')->addRule('required');
        $form->addField('lastname')->addRule('required');
        $form->addField('email')->addRule('email');
        $form->addField('password')->addRule('length', [5, 25], 'Password must have 5 - 25 characters');
        $form->addField('password_confirm')->addRule('equals', ['password'], 'Passwords must match');
        $form->addField(CSRF_TOKEN)->addRule('csrfToken', [$_SESSION[CSRF_TOKEN]]);

        // Check if user exists in database
        $entityManager = \App\App::getEntityManager();
        $usersRepo = $entityManager->getRepository(User::class);
        $user = $usersRepo->findOneBy(['email' => $form->getFieldValue('email')]);

        // Add rest of fields rules
        $form->getField('email')->addRule('paramsNull', [$user], 'User with this email address already exists');

        // If validation fails then render signup view with errors
        if(!$form->validate()) {
            $view = new View('signup.phtml');

            $view->assign(['topnavBg'  => '#ffffff', 'pageTitle' => 'CV Generator | Sign up']);
            $view->assign($form->getFieldValues());
            $view->assign($form->getErrors());
            $view->render();

            return;
        }

        // Create new user and redirect to home page
        $user = new User();
        $user->setEmail($form->getFieldValue('email'));
        $user->setName($form->getFieldValue('name'));
        $user->setLastname($form->getFieldValue('lastname'));
        $user->setPassword(password_hash($form->getFieldValue('password'), PASSWORD_DEFAULT));
        $user->setBirthDate(new \DateTime());

        try {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->setSessionVariables($user->getId());
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