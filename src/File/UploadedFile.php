<?php

namespace Corviz\File;

use Exception;

/**
 * Represents an file received via Php's HTTP post
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
     * UploadedFile constructor.
     * @param string $path
     * @param string $originalName
     * @throws Exception
     */
    public function __construct(string $path, string $originalName)
    {

        parent::__construct($path);
        $this->originalName = $originalName;

        if(!$this->isFile() || !is_uploaded_file($this->getRealPath())){
            throw new Exception("The file '$path' was not uploaded");
        }

    }

}