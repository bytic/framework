<?php

class Nip_File_Upload extends Nip_File
{

    protected $_error;
    protected $_tmp_name;

    public function  __construct($data = array())
    {
        if ($data) {
            $this->_name = $data['name'];
            $this->_tmp_name = $data['tmp_name'];

            parent::__construct(TMP_PATH . $this->_tmp_name);
        }
    }

    public function valid()
    {
        $result = false;

        $max_upload = ini_get("post_max_size");
        $unit = strtoupper(substr($max_upload, -1));
        $multiplier = ($unit == 'M') ? 1048576 : (($unit == 'K') ? 1024 : (($unit == 'G') ? 1073741824 : 1));

        if ($max_upload && ((int) $_SERVER['CONTENT_LENGTH'] > $multiplier * (int) $max_upload)) {
            return self::ERROR_MAX_POST_SIZE;
        }

        if (!$this->getPath()) {
            return false;

        } else if (isset($this->error) && $this->error != 0) {
            return false;

        } else if (!isset($this->_tmp_name) || !@is_uploaded_file($this->_tmp_name)) {
            return false;

        } else if (!isset($this->_name)) {
            return false;
        }

        return true;
    }

    public function upload($path)
    {
        return $this->move($path);
    }

}