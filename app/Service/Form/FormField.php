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

    public function getConstraints() {
        return $this->constraints;
    }

    public function addConstraint(callable $constraint) {
        $this->constraints[] = $constraint;

        return $this;
    }

    public function notEmpty(string $errorMessage = 'This field is required') {
        $this->constraints[] = function ($value) use ($errorMessage) {
            if (empty($value)) { return $errorMessage; }
        };

        return $this;
    }

    public function equalTo($toValue, string $errorMessage = 'Fields does not match') {
        $this->constraints[] = function($value) use ($toValue, $errorMessage) {
            if($value !== $toValue) { return $errorMessage; }
        };

        return $this;
    }

    public function filter($filter, string $errorMessage) {
        $this->constraints[] = function ($value) use ($filter, $errorMessage) {
            if (!filter_var($value, $filter)) { return $errorMessage; }
        };

        return $this;
    }

    public function validEmail(string $errorMessage= 'Invalid email adress') {
        return $this->filter(FILTER_VALIDATE_EMAIL, $errorMessage);
    }

    public function validDate(string $format = 'd.m.Y', string $errorMessage = ' Invalid date format') {
        $this->constraints[] = function ($value) use ($format, $errorMessage) {
            $date = \DateTime::createFromFormat($format, $value);
            if(!$date || !$date->format($format) === $value) {
                return $errorMessage;
            }
        };

        return $this;
    }

}