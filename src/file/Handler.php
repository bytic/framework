<?php

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id: Handler.php 70 2009-04-29 11:47:12Z victor.stanciu $
 */
class Nip_File_Handler
{
    const MODE_READ = "r";
    const MODE_WRITE = "w";
    const MODE_APPEND = "a";


    public $name;
    public $path;
    public $url;
    public $data;
    public $extension;
    public $permissions = 0777;
    protected $handle = null;

    public function __construct($data = false)
    {
        if ($data) {
            foreach ($data as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) == 'set') {
            $name = str_replace("set", "", $name);
            $name{0} = strtolower($name{0});

            $this->$name = $arguments[0];
            return $this;
        } else {
            trigger_error("Method [$name] not defined", E_USER_ERROR);
        }
    }

    public function upload($upload)
    {
        move_uploaded_file($upload["tmp_name"], $this->path);
    }

    public function delete()
    {
        return Nip_File_System::instance()->deleteFile($this->path);
    }

    public function gzip()
    {
        if (function_exists("gzencode")) {
            $this->data = gzencode($this->data, 9, FORCE_GZIP);
        } else {
            return false;
        }
        return $this;
    }

    public function write($data = false, $mode = self::MODE_APPEND)
    {
        if ($data) {
            $this->setData($data);
        }

        $this->open($mode);
        fwrite($this->handle, $this->data);
        $this->close($mode);

        chmod($this->path, $this->permissions);
    }

    public function rewrite($data = false)
    {
        return $this->write($data, self::MODE_WRITE);
    }

    public function open($mode = self::MODE_READ)
    {
        $this->handle = fopen($this->path, $mode);
        return $this;
    }

    public function close()
    {
        if ($this->handle) {
            fclose($this->handle);
            $this->handle = null;
        } else {
            trigger_error("Attempting to close an unopened file", E_USER_WARNING);
        }

        return $this;
    }

}