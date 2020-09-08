<?php

namespace App\Core\Http;

class ZipFileResponse extends Response {

    private $zip;

    private $paths;

    private $ignoreStructure;

    /**
     * Undocumented function
     *
     * @param string $filename name of the zip file
     * @param array $paths paths of directories or files to zip
     * @param boolean $ignoreStructure
     */
    public function __construct(string $filename, $paths = [], bool $ignoreStructure = false) {
        $this->filename = $filename;
        $this->paths = is_array($paths) ? $paths : [$paths];
        $this->ignoreStructure = $ignoreStructure;
    }

    public function addPath($path) {
        $this->paths[] = $path;

        return $this;
    }

    private function pack(string $path, $relativeDir = '') {
        $path = rtrim($path, '/');

        if (is_dir($path)) {
            $subPaths = array_diff(scandir($path), ['.', '..']);

            if (!$this->ignoreStructure) {
                $relativeDir .= basename($path) . '/';
            }

            foreach ($subPaths as $subPath) {
                $this->pack($path . '/' . $subPath, $relativeDir);
            }
        } else if (is_file($path)) {
            $this->zip->addFile($path, $relativeDir . basename($path));
        } else {
            throw new \Exception('Path is neither a directory nor file');
        }
    }

    private function createZip() {
        $this->tmp = tempnam(TMP_DIR, 'zip');
        $this->zip = new \ZipArchive();

        try {
            if ($this->zip->open($this->tmp, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                throw new \Exception("Cannot create zip file");
            }

            foreach ($this->paths as $path) {
                $this->pack($path);
            }
        } catch (\Exception $e) {
            unset($this->tmp);
            throw $e;
        }

        $this->zip->close();
    }

    public function send() {
        ignore_user_abort(true);
        set_time_limit(0);

        $headers = [
            'Content-Length'      => filesize($this->tmp),
            'Content-Type'        => 'application/zip',
            'Content-Disposition' => 'attachment; filename=' . $this->filename . '.zip',
            'Pragma'              => 'no-cache',
            'Expires'             => 0,
        ];

        $this->createZip();
        $this->setHeaders($headers);
        $this->sendHeaders();

        if (file_exists($this->tmp)) {
            readfile($this->tmp);
            unlink($this->tmp);
        }
    }

}