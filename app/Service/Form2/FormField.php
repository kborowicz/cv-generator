<?php

namespace App\Service\Form2;

class FormField {

    protected string $name;

    protected array $rules = [];

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function getRules() {
        return $this->rules;
    }

    public function addRule($rule, $params = null, ?string $message = null) {
        if (!is_string($rule) && !is_callable($rule)) {
            throw new \InvalidArgumentException();
        }

        if (is_string($params)) {
            $message = $params;
            $params = [];
        }

        $this->rules[] = [
            'rule'    => $rule,
            'params'  => $params,
            'message' => $message,
        ];

        return $this;
    }

}