<?php

namespace Artemis\entities;

class Image{

    /** @var string */
    private $path;
    /** @var string */
    private $extension;
    /** @var string */
    private $basename;
    /** @var string */
    private $filename;

    public function __construct(string $filePath){
        $this->path = $filePath;

        $pathInfo = pathinfo($filePath);
        $this->extension = $pathInfo['extension'];
        $this->basename = $pathInfo['basename'];
        $this->filename = $pathInfo['filename'];
    }

    public function getPath() : string{
        return $this->path;
    }

    public function setPath(string $path) : void{
        $this->path = $path;
    }

    public function getExtension() : string{
        return $this->extension;
    }

    public function getBasename() : string{
        return $this->basename;
    }

    public function getFilename() : string{
        return $this->filename;
    }

}