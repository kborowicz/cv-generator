<?php

namespace App\Core\Http;

class JsonResponse extends Response {

    protected $data;

    protected $error;

    protected $success;

    public function __construct($data = null, $error = null, $success = null) {
        $this->data = $data;
        $this->error = $error;
        $this->success = $success;

        $this->headers = [
            'Content-Type'  => 'application/json',
            'Cache-Control' => '', //TODO jakos to dopracowac. problemem jest różny spójnik dla headerów
        ];
    }

    public function send() {
        $this->sendHeaders();

        echo json_encode([
            'data'    => $this->data,
            'error'   => $this->error,
            'success' => $this->success,
        ]);
    }

    /**
     * Get the value of data
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Set the value of data
     *
     * @return  self
     */
    public function setData($data) {
        $this->data = $data;

        return $this;
    }

    /**
     * Get the value of error
     */
    public function getError() {
        return $this->error;
    }

    /**
     * Set the value of error
     *
     * @return  self
     */
    public function setError($error) {
        $this->error = $error;

        return $this;
    }

    /**
     * Get the value of success
     */
    public function getSuccess() {
        return $this->success;
    }

    /**
     * Set the value of success
     *
     * @return  self
     */
    public function setSuccess($success) {
        $this->success = $success;

        return $this;
    }
}