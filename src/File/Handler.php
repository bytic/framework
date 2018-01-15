<?php

/**
 * Nip Framework.
 *
 * @category   Nip
 *
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * @version    SVN: $Id: Handler.php 70 2009-04-29 11:47:12Z victor.stanciu $
 */
class Nip_File_Handler
{
    const MODE_READ = 'r';
    const MODE_WRITE = 'w';
    const MODE_APPEND = 'a';

    /**
     * @var string
     */
    public $name;

    /**
     * @var null|string
     */
    public $path = null;

    /**
     * @var null|string
     */
    public $url = null;

    public $data;
    public $extension;
    public $permissions = 0777;
    protected $handle = null;

    /**
     * Nip_File_Handler constructor.
     *
     * @param bool $data
     */
    public function __construct($data = false)
    {
        if ($data) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return $this
     */
    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) == 'set') {
            $name = str_replace('set', '', $name);
            $name[0] = strtolower($name[0]);

            $this->$name = $arguments[0];

            return $this;
        } else {
            trigger_error("Method [$name] not defined", E_USER_ERROR);
        }
    }

    /**
     * @param $upload
     */
    public function upload($upload)
    {
        move_uploaded_file($upload['tmp_name'], $this->path);
    }

    /**
     * @return $this
     */
    public function delete()
    {
        return Nip_File_System::instance()->deleteFile($this->path);
    }

    /**
     * @return $this|false
     */
    public function gzip()
    {
        if (function_exists('gzencode')) {
            $this->data = gzencode($this->data, 9, FORCE_GZIP);
        } else {
            return false;
        }

        return $this;
    }

    /**
     * @param bool   $data
     * @param string $mode
     */
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

    /**
     * @param bool $data
     */
    public function rewrite($data = false)
    {
        return $this->write($data, self::MODE_WRITE);
    }

    /**
     * @param string $mode
     *
     * @return $this
     */
    public function open($mode = self::MODE_READ)
    {
        $this->handle = fopen($this->path, $mode);

        return $this;
    }

    /**
     * @return $this
     */
    public function close()
    {
        if ($this->handle) {
            fclose($this->handle);
            $this->handle = null;
        } else {
            trigger_error('Attempting to close an unopened file', E_USER_WARNING);
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUrl()
    {
        if ($this->url === null) {
            $this->initUrl();
        }

        return $this->url;
    }

    public function initUrl()
    {
        $this->url = '';
    }

    /**
     * @return null|string
     */
    public function getPath()
    {
        if ($this->path === null) {
            $this->initPath();
        }

        return $this->path;
    }

    public function initPath()
    {
        $this->path = '';
    }
}
