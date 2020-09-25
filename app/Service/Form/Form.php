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
        } else {
            throw new \InvalidArgumentException("Invalid form method: '$formMethod'");
        }

        $this->errorMessages = [
            'required' => 'This field is required',
            'equals'   => 'Fields doest not match',
            'length'   => 'Invalid field length',
            'email'    => 'Invalid email address',
            'password' => 'Incorrect password',
        ];
    }

    public function getField($name) : FormField {
        return $this->fields[$name] ?? null;
    }

    public function addField($name) : FormField {
        if (array_key_exists($name, $this->fields)) {
            throw new \InvalidArgumentException("Field '$name' already exists");
        }

        $field = new FormField($name);
        $this->fields[$name] = $field;

        return $field;
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

    public function validate() : bool {
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
                        break;
                    }
                } else {
                    $ruleName = $rule['rule'];
                    $callback = 'validate' . ucfirst($ruleName);

                    if (method_exists($this, $callback)) {
                        if (!$this->{$callback}($value, $ruleParams, $fieldsValues)) {
                            $this->errors[$name] = $ruleMessage ?? $this->errorMessages[$ruleName] ?? $this->defaultErrorMessage;
                            break;
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

    public function getErrors(string $errorSuffix = 'Error') : array{
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

    public function stopOnFirstError(bool $stopOnFirstError) : Form {
        $this->stopOnFirstError = $stopOnFirstError;

        return $this;
    }

    public function setDefaultErrorMessage(string $errorMessage) : Form {
        $this->defaultErrorMessage = $errorMessage;

        return $this;
    }

    public function setErrorMessage(string $rule, string $errorMessage) : Form {
        $this->defaultErrorMessages[$rule] = $errorMessage;

        return $this;
    }

    public function setErrorMesages(array $errorMessages) : Form {
        $this->errorMessages = $errorMessages;

        return $this;
    }

    /*  Validate methods */

    protected function validateRequired($value) : bool {
        return !empty($value);
    }

    protected function validateEquals($value, $params, $fields) : bool {
        return $value === $fields[$params[0]];
    }

    protected function validateLength($value, $params) : bool  {
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

    protected function validateEmail($value) : bool {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validateCsrfToken($value, $params) : bool {
        return !empty($value) && $value == $params[0];
    }

    protected function validatePassword($value, $params) : bool  {
        return !empty($value) && password_verify($value, $params[0]);
    }

    protected function validateParamsNotNull($value, $params) : bool  {
        foreach ($params as $params) {
            if ($params == null) {
                return false;
            }
        }

        return true;
    }

    protected function validateParamsNull($value, $params) : bool  {
        foreach ($params as $params) {
            if ($params != null) {
                return false;
            }
        }

        return true;
    }

}

BFS(initialState) {
    open = [ initialState ];
    closed = [];

    while open != [] {
        X = shift(open);
        
        if(X == {goal state}) {
            return 'sucess';
        }

        closed.push(X);
        children = X.getChildren();

        open.push(children);
        open.removeDuplicates();
        open.sortAscending(); // Sort by heurestic value ascending
    }
}