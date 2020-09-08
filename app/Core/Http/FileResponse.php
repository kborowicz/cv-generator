<?php

namespace App\Core\Http;

class FileResponse extends Response {

    protected $file;

    protected $download;

    public function __construct($file, $download = false) {
        $this->file = $file;
        $this->download = $download;
    }

    public function send() {
        if(file_exists($this->file)) {
            $this->setHeader('Content-Type', mime_content_type($this->file));
            $this->setHeader('Content-Length', filesize($this->file));

            if($this->download) {
                $this->setHeader('Content-Disposition', 'attachment;filename="'
                    . basename($this->file) . '"');
            } else {
                $this->setHeader('Content-Disposition', 'inline;filename="' 
                    . basename($this->file) . '"');
            }

            $this->sendHeaders();
            readfile($this->file);
        } else {
            $this->setCode(self::HTTP_NOT_FOUND);
            $this->sendHeaders();
        }
    }

}