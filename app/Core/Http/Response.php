<?php

namespace App\Core\Http;

abstract class Response {

    public const HTTP_OK = 200;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;
    public const HTTP_SERVICE_UNAVAILBLE = 503;

    protected int $code = self::HTTP_OK;

    protected array $headers = [];

    protected function sendHeaders() {
        if(!headers_sent()) {
            header_remove();
            http_response_code($this->code);
    
            foreach ($this->headers as $header => $value) {
                header($header . ':' . $value);
            }
        }
    }

    public function setCode($code) {
        $this->code = $code;
        return $this;
    }

    public function getCode() {
        return $this->code;
    }

    public function setHeader($header, $value) {
        $this->headers[$header] = $value;
        return $this;
    }

    public function setHeaders(array $headers) {
        $this->headers = $headers;
        return $this;
    }

    public function getHeaders() {
        return $this->headers;
    }

    abstract public function send();

}