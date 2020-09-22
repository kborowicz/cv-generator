<?php

namespace App\Service\Form;

class Form {

    protected string $defaultErrorMessage = 'Invalid field value';

    protected array $errorMessages = [];

    protected array $errors = [];

    protected array $fields = [];

    protected array $data;

    protected bool $stopOnFirstError = false;

    public function __construct(string $formMethod) {
        $formMethod = strtoupper($formMethod);

        if ($formMethod == 'POST') {
            $this->data = $_POST;
        } else if ($formMethod == 'GET') {
            $this->data = $_GET;
        }

        $this->errorMessages = [
            'required'  => 'This field is required',
            'equals'    => 'Fields doest not match',
            'length'    => 'Invalid field length',
            'email'     => 'Invalid email address',
            'csrfToken' => '',
        ];
    }

    public function getFieldValue(string $fieldName) {
        return $this->data[$fieldName];
    }

    public function getFieldValues(): array{
        $values = [];

        foreach ($this->fields as $field) {
            $name = $field->getName();
            $values[$name] = $this->data[$name] ?? null;
        }

        return $values;
    }

    public function getField($name) {
        return $this->fields[$name] ?? null;
    }

    public function addField($name) {
        if (array_key_exists($name, $this->fields)) {
            throw new \InvalidArgumentException("Field '$name' already exists");
        }

        $field = new FormField($name);
        $this->fields[$name] = $field;

        return $field;
    }

    public function validate() {
        $fieldsValues = $this->getFieldValues();
        $errorCount = 0;

        foreach ($this->fields as $field) {
            $name = $field->getName();
            $value = $fieldsValues[$name];

            foreach ($field->getRules() as $rule) {
                $ruleParams = $rule['params'];
                $ruleMessage = $rule['message'];

                if (is_callable($rule['rule'])) {
                    $callback = $rule['rule'];

                    if (!$callback($value, $ruleParams, $fieldsValues)) {
                        $this->errors[$name] = $ruleMessage ?? $this->defaultErrorMessage;
                    }
                } else {
                    $ruleName = $rule['rule'];
                    $callback = 'validate' . ucfirst($ruleName);

                    if (method_exists($this, $callback)) {
                        if (!$this->{$callback}($value, $ruleParams, $fieldsValues)) {
                            $this->errors[$name] = $ruleMessage ?? $this->errorMessages[$ruleName] ?? $this->defaultErrorMessage;
                        }
                    } else {
                        throw new \Exception("Unknown rule '$ruleName'");
                    }
                }

                $errorCount = count($this->errors);

                if ($errorCount > 0 && $this->stopOnFirstError) {
                    return false;
                }
            }
        }

        return $errorCount == 0;
    }

    public function getErrors(string $errorSuffix = 'Error'): array{
        if (empty($errorSuffix)) {
            return $this->errors;
        }

        $errors = [];

        foreach ($this->errors as $field => $error) {
            $errors[$field . $errorSuffix] = $error;
        }

        return $errors;
    }

    public function hasErrors() {
        return count($this->errors) > 0;
    }

    public function setStopOnFirstError(bool $stopOnFirstError) {
        $this->stopOnFirstError = $stopOnFirstError;
    }

    public function setDefaultErrorMessage(string $errorMessage) {
        $this->defaultErrorMessage = $errorMessage;
    }

    public function setErrorMessage(string $rule, string $errorMessage) {
        $this->defaultErrorMessages[$rule] = $errorMessage;
    }

    public function setErrorMesages(array $errorMessages) {
        $this->errorMessages = $errorMessages;
    }

    /*  Validate methods */

    protected function validateRequired($value) {
        return !empty($value);
    }

    protected function validateEquals($value, $params, $fields) {
        return $value === $fields[$params[0]];
    }

    protected function validateLength($value, $params) {
        if (!is_string($value)) {
            return false;
        }

        $valueLength = strlen($value);

        if (isset($params[1])) {
            return $valueLength >= $params[0] && $valueLength <= $params[1];
        } else {
            return $valueLength >= $params[0];
        }
    }

    protected function validateEmail($value) {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validateCsrfToken($value, $params) {
        return !empty($value) && $value == $params[0];
    }

}