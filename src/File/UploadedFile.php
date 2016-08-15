<?php

namespace Corviz\File;

use Exception;

/**
 * Represents an file received via Php's HTTP request.
 */
class UploadedFile extends File
{
    /**
     * @var string
     */
    private $originalName;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * Returns the MIME content type for the file
     * informed by HTTP request.
     *
     * @return string
     */
    public function getMimeType() : string
    {
        return $this->mimeType;
    }

    /**
     * Get the original name file,
     * informed by HTTP request;
     *
     * @return string
     */
    public function getOriginalName() : string
    {
        return $this->originalName;
    }

    /**
     * UploadedFile constructor.
     * The original file name and mime-type are
     * received from the request itself.
     *
     * @param string $path
     * @param string $originalName
     * @param string $mimeType
     *
     * @throws Exception
     */
    public function __construct(string $path, string $originalName, string $mimeType)
    {
        parent::__construct($path);
        $this->originalName = $originalName;

        if (!$this->isFile() || !is_uploaded_file($this->getRealPath())) {
            throw new Exception("The file '$path' was not uploaded");
        }
    }
}
