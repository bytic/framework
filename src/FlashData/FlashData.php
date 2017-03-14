<?php

namespace Nip\FlashData;

/**
 * Nip Framework
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @version    SVN: $Id: Flash.php 14 2009-04-13 11:24:22Z victor.stanciu $
 */

class FlashData
{

    protected $previous = [];
    protected $next = [];

    protected $session_var = 'flash-data';

    /**
     * FlashData constructor.
     */
    public function __construct()
    {
        $this->read();
    }

    public function read()
    {
        if (isset($_SESSION[$this->session_var])) {
            $data = $_SESSION[$this->session_var];
            if (is_array($data)) {
                $this->previous = $data;
            }
            unset($_SESSION[$this->session_var]);
        }
    }

    public function has($var)
    {
        return isset($this->previous[trim($var)]) ? true : false;
    }

    public function get($var)
    {
        return isset($this->previous[trim($var)]) ? $this->previous[trim($var)] : null;
    }

    public function add($var, $value)
    {
        $this->next[trim($var)] = $value;
        $this->write();
    }

    protected function write()
    {
        $_SESSION[$this->session_var] = $this->next;
    }

    public function remove($var)
    {
        unset($this->next[trim($var)]);
        $this->write();
    }

    protected function clear()
    {
        $this->next = [];
    }
}
