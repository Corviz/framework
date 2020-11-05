<?php

namespace Corviz\File;

/**
 * Represents a local file.
 */
class File
{
    /**
     * @var string
     */
    private $realPath;

    /**
     * Copy the current source to destination.
     *
     * @param string $destination
     * @param bool   $overwrite
     *
     * @return bool
     */
    public function copy(string $destination, $overwrite = false): bool
    {
        $copied = false;

        if ($this->isFile()) {
            $exists = file_exists($destination);
            if (!$exists || ($exists && $overwrite)) {
                $copied = copy($this->realPath, $destination);
            }
        }

        return $copied;
    }

    /**
     * Remove a file from the disk.
     *
     * @return bool
     */
    public function delete(): bool
    {
        $removed = null;

        if ($this->isDirectory()) {
            $removed = rmdir($this->realPath);
        } else {
            $removed = unlink($this->realPath);
        }

        return $removed;
    }

    /**
     * Checks if the current source exist.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return $this->realPath && file_exists($this->realPath);
    }

    /**
     * Returns the MIME content type for the file
     * based on 'mime_content_type' function.
     * Please, visit http://php.net/manual/en/function.mime-content-type.php
     * for more information.
     *
     * @return string
     */
    public function getMimeType(): string
    {
        return mime_content_type($this->realPath);
    }

    /**
     * Current source real path.
     *
     * @return string
     */
    public function getRealPath(): string
    {
        return $this->realPath;
    }

    /**
     * Gets the size of a file.
     * If the current source is not a file, returns -1.
     *
     * @return int
     */
    public function getSize(): int
    {
        $size = -1;

        if ($this->isFile()) {
            $size = filesize($this->realPath);
        }

        return $size;
    }

    /**
     * Determines if the source is a directory.
     *
     * @return bool
     */
    public function isDirectory(): bool
    {
        return is_dir($this->realPath);
    }

    /**
     * Determines if the source is a file.
     *
     * @return bool
     */
    public function isFile(): bool
    {
        return is_file($this->realPath);
    }

    /**
     * Determines if the current item can be read.
     *
     * @return bool
     */
    public function isReadable(): bool
    {
        return is_readable($this->realPath);
    }

    /**
     * Determines if the current item can be written.
     *
     * @return bool
     */
    public function isWriteable(): bool
    {
        return is_writable($this->realPath);
    }

    /**
     * Read the file contents, based on PHP file_get_contents function
     * This function will return a string containing the contents,
     * or FALSE on failure.
     *
     * @return bool|string
     */
    public function read()
    {
        $contents = false;

        if ($this->isFile() && $this->isReadable()) {
            $contents = file_get_contents($this->realPath);
        }

        return $contents;
    }

    /**
     * Give the source a new name.
     *
     * @param string $newName
     *
     * @return bool
     */
    public function rename(string $newName): bool
    {
        $renamed = rename($this->realPath, $newName);

        if ($renamed) {
            $this->realPath = realpath($newName);
        }

        return $renamed;
    }

    /**
     * @param $data
     *
     * @return bool
     */
    public function write($data): bool
    {
        $success = false;

        if ($this->isFile() && $this->isWriteable()) {
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
