<?php

namespace Corviz\Http\RequestParser;

use Corviz\File\UploadedFile;
use Corviz\Http\Request;

/**
 * Provided 'multipart/form-data' parser.
 */
class MultipartFormDataParser extends ContentTypeParser
{
    /**
     * @return mixed
     */
    protected function initialize()
    {
        $this->supports('multipart/form-data');
    }

    /**
     * Convert a raw body string to array format.
     *
     * @return array
     */
    public function getData() : array
    {
        $data = [];

        if ($this->getRequest()->getMethod() == Request::METHOD_POST) {
            $data = $_POST ?: [];
        }

        return $data;
    }

    /**
     * Gets an array of uploaded files,
     * from the request.
     *
     * @return array
     */
    public function getFiles() : array
    {
        $files = [];

        if (!empty($_FILES)) {
            foreach ($_FILES as $inputName => $phpFile) {
                if (!is_array($phpFile['name'])) {

                    // When this was a single file
                    $files[$inputName] = $this->handleUniqueFile($phpFile);
                } else {

                    //When the input has a 'multiple' attribute
                    $files[$inputName] = $this->handleMultipleFile($phpFile);
                }
            }
        }

        return $files;
    }

    /**
     * Handle a file input, when it IS NOT 'multiple'.
     *
     * @param array $file An element from $_FILES superglobal
     *
     * @return UploadedFile|null
     */
    protected function handleUniqueFile($file)
    {
        $uploadedFile = null;

        //The current file was uploaded successfully?
        if (!$file['error']) {
            $uploadedFile = new UploadedFile(
                $file['tmp_name'], $file['name']
            );
        }

        return $uploadedFile;
    }

    /**
     * Handle a file input, when it IS 'multiple'.
     *
     * @param array $file An element from $_FILES superglobal
     *
     * @return array
     */
    protected function handleMultipleFile($file) : array
    {
        $fileBag = [];

        foreach ($file['name'] as $idx => $name) {
            $uploadedFile = null;

            //The current file was uploaded successfully?
            if (!$file[$idx]['error']) {
                $uploadedFile = new UploadedFile(
                    $file[$idx]['tmp_name'], $file[$idx]['name']
                );
            }

            $fileBag[$idx] = $uploadedFile;
        }

        return $fileBag;
    }
}
