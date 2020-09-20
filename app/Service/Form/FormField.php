<?php

namespace App\Service\Form;

class FormField {

    private string $name;

    private array $constraints = [];

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    public function getRequired() {
        return $this->required;
    }

    public function setRequired($required) {
        $this->required = $required;

        return $this;
    }

    public function getConstraints() {
        return $this->constraints;
    }

    public function setWithConstraint(callable $constraint) {
        $this->constraints[] = $constraint;

        return $this;
    }

    public function setWithConstraints(array $constraints) {
        foreach ($constraints as $constraint) {
            $this->constraints[] = $constraint;
        }

        return $this;
    }

    public function setNotEmpty(string $errorMessage = 'Field cannot be empty') {
        $this->constraints[] = function ($isEmpty, $name, $value) use ($errorMessage) {
            if ($isEmpty) {
                return $errorMessage;
            }
        };

        return $this;
    }

    public function setWithValidEmail(string $errorMessage= 'Invalid email adress') {
        $this->constraints[] = function ($isEmpty, $name, $value) use ($errorMessage) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return $errorMessage;
            }
        };

        return $this;
    }

    public function setWithValidDate(string $errorMessage = ' Invalid date format') {
        $this->constraints[] = function ($isEmpty, $name, $value) use ($errorMessage) {
        };
    }

    public function setEqualTo($val, string $errorMessage = 'Fields does not match') {
        $this->constraints[] = function($isEmpty, $name, $value) use ($val, $errorMessage) {
            if($value !== $val) {
                return $errorMessage;
            }
        };

        return $this;
    }

}