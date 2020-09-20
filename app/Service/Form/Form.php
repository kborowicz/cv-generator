<?php

namespace App\Service\Form;

class Form {

    private array $data;

    private array $fields = [];

    private array $errors = [];

    public function __construct(string $method) {
        $method = strtoupper($method);

        if($method == 'GET') {
            $this->data = $_GET;
        } else if($method == 'POST') {
            $this->data = $_POST;
        } else {
            throw new \Exception("Unknown method: '$method'");
        }
    }

    public function addField(string $name) {
        $field = new FormField($name);
        $this->fields[] = $field;

        return $field;
    }

    public function getValueOf(string $fieldName) {
        return $this->data[$fieldName] ?? null;
    }

    public function getFieldValues() : array {
        $values = [];

        foreach ($this->fields as $field) {
            $name = $field->getName();
            $values[$name] = $this->data[$name] ?? null;
        }

        return $values;
    }

    public function validate(string $errorSuffix = 'Error') {
        foreach($this->fields as $field) {
            $name = $field->getName();
            $isEmpty = empty($this->data[$name]);
            $value = $isEmpty ? null : $this->data[$name];

            foreach($field->getConstraints() as $constraint) {
                if($errorMessage = $constraint($isEmpty, $name, $value)) {
                    $this->errors[$name . $errorSuffix] = $errorMessage;
                    break;
                }
            }
        }

        return count($this->errors) == 0;
    }

    public function hasErrors() : bool {
        return count($this->errors) > 0;
    }

    public function getErrors() : array {
        return $this->errors;
    }

}