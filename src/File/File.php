<?php

class Nip_File_Exception extends Exception
{
}

class Nip_File extends Nip_Object
{
    protected $_path;
    protected $_name;
    protected $_extension;

    public function __construct($path = false)
    {
        if ($path) {
            $this->setPath($path);
        }
    }

    public function move($target)
    {
        $dir = dirname($target);
        if (!is_dir($target)) {
            mkdir($dir, 0755, true);
        }

        if (rename($this->getPath(), $target)) {
            $this->setPath($target);
        } else {
            throw new Nip_File_Exception("Cannot move $this->_path file to $target");
        }

        return $this;
    }

    public function copy($target)
    {
        $dir = dirname($target);
        if (!is_dir($target)) {
            mkdir($dir, 0755, true);
        }

        if (copy($this->getPath(), $target)) {
            $this->setPath($target);
        } else {
            throw new Nip_File_Exception("Cannot copy $this->_path file to $target");
        }

        return $this;
    }

    /**
     * @param string $target
     *
     * @return Nip_Process
     */
    public function unzip($target)
    {
        if (!is_dir($target)) {
            mkdir($target, 0755, true);
        }

        $process = new Nip_Process("unzip {$this->_path}  -d $target");
        $process->run();

        return $process;
    }

    public function download($filename = false, $contentType = false)
    {
        if (!$filename) {
            $filename = $this->getName();
        }
        if (!$contentType) {
            $contentType = 'application/force-download';
        }

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header("Content-Type: $contentType");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Length: {$this->getSize()}");
        header('Content-Transfer-Encoding: binary');

        readfile($this->getPath());
        exit();
    }

    public function delete()
    {
        unlink($this->getPath());
    }

    public function setPath($path)
    {
        $this->_name = basename($path);
        $this->_extension = pathinfo($path, PATHINFO_EXTENSION);
        $this->_path = $path;

        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getExtension()
    {
        return $this->_extension;
    }

    public function getSize()
    {
        return filesize($this->getPath());
    }

    public function getTime()
    {
        return filemtime($this->getPath());
    }

    public function getPath()
    {
        return $this->_path;
    }

    public function getMimeType()
    {
        if (function_exists('mime_content_type')) {
            return mime_content_type($this->getPath());
        } elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $this->getPath());
            finfo_close($finfo);

            return $mimetype;
        }

        return 'unknown';
    }
}
