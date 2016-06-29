<?php

namespace Corviz\File;

use \Exception;
use  \Corviz\File\File;

/**
 * Represents an file received via Php's HTTP post
 * @package Corviz\File
 */
class UploadedFile extends File
{

    /**
     * @var string
     */
    private $originalName;

    /**
     * @return string
     */
    public function getOriginalName() : string
    {
        return $this->originalName;
    }

    /**
     * @param string $originalName
     */
    public function setOriginalName(string $originalName)
    {
        $this->originalName = $originalName;
    }

    /**
     * UploadedFile constructor.
     * @param string $path
     * @throws Exception
     */
    public function __construct($path)
    {

        parent::__construct($path);

        if(!$this->isFile() || !is_uploaded_file($this->getRealPath())){
            throw new Exception("The file '$path' was not uploaded");
        }

    }

}