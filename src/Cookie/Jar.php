<?php

namespace Nip\Cookie;

class Jar {

    protected $_defaults;

    public static $instance;

    public function  __construct() {
        $this->initDefaults();
    }

    /**
     * @return Cookie
     */
    public function newCookie() {
        $cookie = new Cookie();
        $defaults = $this->getDefaults();
        $cookie->setPath($defaults['path']);
        $cookie->setDomain($defaults['domain']);
        $cookie->setExpireTimer($defaults['expireTimer']);
        return $cookie;
    }

    public function initDefaults() {
        $this->_defaults = array(
            'path'   => '/',
            'domain' => $_SERVER['SERVER_NAME'],
            'expireTimer' => 6 * 60 * 60,
        );
    }

    public function setDefaults($defaults) {
        foreach ($defaults as $name => $value) {
            $this->setDefault($name, $value);
        }
    }

    public function setDefault($name, $value = NULL) {
        if ($value !== NULL) {
            $this->_defaults[$name] = $value;
        }
    }

    public function getDefaults() {
        return $this->_defaults;
    }

    /**
     * Singleton
     *
     * @return self
     */
    public static function instance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}