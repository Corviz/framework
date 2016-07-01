<?php

namespace Corviz\File;

/**
 * Represents a local file
 * @package Corviz\File
 */
class File
{

    /**
     * @var string
     */
    private $realPath;

    /**
     * Copy the current source to destination
     * @param string $destination
     * @param boolean $overwrite
     * @return boolean
     */
    public function copy(string $destination, $overwrite = false) : bool
    {
        $copied = false;

        if($this->isFile()){
            $exists = file_exists($destination);
            if(!$exists || ($exists && $overwrite)){
                $copied = copy($this->realPath, $destination);
            }
        }

        return $copied;
    }

    /**
     * Remove a file from the disk
     * @return boolean
     */
    public function delete() : boolean
    {
        $removed = null;

        if($this->isDirectory()){
            $removed = rmdir($this->realPath);
        }else{
            $removed = unlink($this->realPath);
        }

        return $removed;
    }

    /**
     * Checks if the current source exist
     * @return boolean
     */
    public function exists() : boolean
    {
        return $this->realPath && file_exists($this->realPath);
    }
    
    /**
     * Current source real path
     * @return string
     */
    public function getRealPath() : string
    {
        return $this->realPath;
    }

    /**
     * Gets the size of a file.
     * If the current source is not a file, returns -1
     * @return int
     */
    public function getSize() : int
    {
        $size = -1;

        if($this->isFile()){
            $size = filesize($this->realPath);
        }

        return $size;
    }

    /**
     * Determines if the source is a directory
     * @return boolean
     */
    public function isDirectory() : boolean
    {
        return is_dir($this->realPath);
    }

    /**
     * Determines if the source is a file
     * @return bool
     */
    public function isFile() : boolean
    {
        return is_file($this->realPath);
    }

    /**
     * Determines if the current item can be read
     * @return boolean
     */
    public function isReadable() : boolean
    {
        return is_readable($this->realPath);
    }

    /**
     * Determines if the current item can be written
     * @return boolean
     */
    public function isWriteable() : boolean
    {
        return is_writeable($this->realPath);
    }

    /**
     * Read the file contents, based on PHP file_get_contents function
     * This function will return a string containing the contents,
     * or FALSE on failure
     * @return boolean|string
     */
    public function read()
    {
        $contents = false;

        if($this->isFile() && $this->isReadable()){
            $contents = file_get_contents($this->realPath);
        }

        return $contents;
    }

    /**
     * Give the source a new name
     * @param string $newName
     * @return boolean
     */
    public function rename(string $newName) : boolean
    {
        $renamed = rename($this->realPath, $newName);

        if($renamed){
            $this->realPath = realpath($newName);
        }

        return $renamed;
    }

    /**
     *
     * @param $data
     * @return boolean
     */
    public function write($data) : boolean
    {
        $success = false;

        if($this->isFile() && $this->isWriteable()){
            $success = file_put_contents($this->realPath, $data) !== false;
        }

        return $success;
    }
    
    /**
     * @param string $path The source of an file
     */
    public function __construct(string $path)
    {
        $this->realPath = realpath($path) ?: '';
    }

}