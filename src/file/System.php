<?php

use Nip\Filesystem\Exception\IOException;

class Nip_File_System
{

    protected $_uploadErrors = array(
        0 => "There is no error, the file uploaded with success",
        1 => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
        2 => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
        3 => "The uploaded file was only partially uploaded",
        4 => "No file was uploaded",
        6 => "Missing a temporary folder",
    );

    /**
     * Singleton
     *
     * @return self
     */
    public static function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Returns error message on upload, if any
     *
     * @param string $file
     * @param array $extensions
     * @return mixed
     */
    public function getUploadError($file, $extensions = array())
    {
        $messages = array(
            'max_post' => 'POST exceeded maximum allowed size.',
            'no_upload' => 'No upload found in \$_FILES',
            'bad_upload' => 'Upload failed is_uploaded_file test.',
            'no_name' => 'File has no name.',
            'bad_extension' => 'Invalid file extension',
        );

        $errorCode = $this->getUploadErrorNo($file, $extensions);
        if (is_int($errorCode)) {
            $translateSlug = 'general.errors.upload.code-'.$errorCode;

            return app('translator')->hasTranslation($translateSlug) ? __($translateSlug) : $this->_uploadErrors[$errorCode];
        } elseif (is_string($errorCode)) {
            $translateSlug = 'general.errors.upload.'.$errorCode;

            return app('translator')->hasTranslation($translateSlug) ? __($translateSlug) : $messages[$errorCode];
        }

        return false;
    }

    /**
     * Returns error message on upload, if any
     *
     * @param string $file
     * @param array $extensions
     * @return mixed
     */
    public function getUploadErrorNo($file, $extensions = array())
    {
        $result = false;

        $maxUpload = ini_get("post_max_size");
        $unit = strtoupper(substr($maxUpload, -1));
        $multiplier = $unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1));

        if ($maxUpload && ((int)$_SERVER['CONTENT_LENGTH'] > $multiplier * (int)$maxUpload)) {
            $result = "max_post";
        }

        if (!isset($file)) {
            $result = "no_upload";
        } else {
            if (isset($file["error"]) && $file["error"] != 0) {
                $result = $file["error"];
            } else {
                if (!isset($file["tmp_name"]) || !@is_uploaded_file($file["tmp_name"])) {
                    $result = "bad_upload";
                } else {
                    if (!isset($file['name'])) {
                        $result = "no_name";
                    }
                }
            }
        }

        if ($extensions && !in_array($this->getExtension($file['name']), $extensions)) {
            $result = "bad_extension";
        }

        return $result;
    }

    /**
     * Get file extension
     *
     * @param string $str
     * @return string
     */
    public function getExtension($str)
    {
        return strtolower(pathinfo($str, PATHINFO_EXTENSION));
    }

    /**
     * Gets list of all files within a directory
     *
     * @param string $dir
     * @param boolean $recursive
     * @return array
     */
    public function scanDirectory($dir, $recursive = false, $fullPaths = false)
    {
        $result = array();

        if (is_dir($dir)) {
            if ($recursive) {
                $iterator = new RecursiveDirectoryIterator($dir);
                foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file) {
                    if ($file->isFile()) {
                        $result[] = ($fullPaths ? $file->getPath().DIRECTORY_SEPARATOR : '').$file->getFilename();
                    }
                }
            } else {
                $iterator = new DirectoryIterator($dir);
                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $result[] = $file->getFilename();
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Recursively create a directory and set it's permissions
     *
     * @param string $dir
     * @param int $mode
     * @return boolean
     */
    public function createDirectory($dir, $mode = 0777)
    {
        return is_dir($dir) ? true : mkdir($dir, $mode, true);
    }

    /**
     * Builds array-tree of directory, with files as final nodes
     *
     * @param string $dir
     * @param array $tree
     * @return array
     */
    public function directoryTree($dir, $tree = array())
    {
        $dir = realpath($dir);
        $d = dir($dir);

        while (false != ($entry = $d->read())) {
            $complete = $d->path."/".$entry;
            if (!in_array($entry, array(".", "..", ".svn"))) {
                if (is_dir($complete)) {
                    $tree[$entry] = $this->directoryTree($complete, $tree[$dir][$entry]);
                } else {
                    $tree[] = $entry;
                }
            }
        }

        $d->close();

        return $tree;
    }

    /**
     * Recursively empties a directory
     * @param string $dir
     */
    public function emptyDirectory($dir)
    {
        $dir = rtrim($dir, "/");

        $files = scandir($dir);

        array_shift($files);
        array_shift($files);

        foreach ($files as $file) {
            $file = $dir.'/'.$file;
            if (is_dir($file)) {
                $this->removeDirectory($file);
            } else {
                unlink($file);
            }
        }

        return $this;
    }

    /**
     * Recursively removes a directory
     * @param string $dir
     */
    public function removeDirectory($dir)
    {
        $dir = rtrim($dir, "/");

        if (is_dir($dir)) {
            $files = scandir($dir);

            foreach ($files as $file) {
                if (!in_array($file, array(".", ".."))) {
                    $file = $dir.DIRECTORY_SEPARATOR.$file;
                    if (is_dir($file)) {
                        $this->removeDirectory($file);
                    } else {
                        unlink($file);
                    }
                }
            }

            rmdir($dir);
        }
    }

    public function deleteFile($path)
    {
        if (file_exists($path)) {
            unlink($path);
        }

        return $this;
    }

    public function copyDirectory($source, $destination)
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        $process = new Nip_Process("cp -R -f $source/* $destination");

        return $process->run();
    }

    public function formatSize($bytes)
    {
        if (!$bytes) {
            return "0 kb";
        }

        $s = array('b', 'kb', 'MB', 'GB', 'TB', 'PB');
        $e = floor(log($bytes) / log(1024));

        return sprintf('%.2f '.$s[$e], ($bytes / pow(1024, floor($e))));
    }

    public function chmod($file, $mode)
    {
        if (true !== @chmod($file, $mode)) {
            throw new IOException(sprintf('Failed to chmod file "%s".', $file), 0, null, $file);
        }
    }

}
