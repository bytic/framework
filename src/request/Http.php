<?php

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id: Http.php 135 2009-05-27 16:48:23Z victor.stanciu $
 */

class Nip_Request_Http {

    /**
     * Retrieve a member of the $_SERVER superglobal
     *
     * If no $key is passed, returns the entire $_SERVER array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getServer($key = null, $default = null) {
        if (null === $key) {
            return $_SERVER;
        }

        return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
    }

    public function getFullURL()
    {
    	return ($this->isSSL() ? 'https://' : 'http://') . $this->getServerName() . $this->getServer('REQUEST_URI');
    }

    public function getScriptName()
    {
    	return isset($_SERVER['ORIG_SCRIPT_NAME']) ? $_SERVER['ORIG_SCRIPT_NAME'] : $_SERVER['SCRIPT_NAME'];
    }

    public function determineScriptNameByFilePath($filePath = false)
    {
        $dirPath = dirname($filePath).DS;
        $scriptName = DS.str_replace(ROOT_PATH, '', $dirPath);
    	return str_replace(DS, '/', $scriptName);
    }

    public function getServerName()
    {
    	return $this->getServer('SERVER_NAME');
    }

    public function getSubdomain()
    {
        $name = $this->getServerName();
        if ($name) {
            if (substr_count($name, '.') > 1) {
                $parts = explode('.', $name);
                return reset($parts);
            }
        }

    	return false;
    }

    public function getRootDomain()
    {
        $name = $this->getServerName();
        if ($name) {
            if (substr_count($name, '.') > 1) {
                $parts = explode('.', $name);
                array_shift($parts);
                return implode('.', $parts);
            }
            return $name;
        }

    	return false;
    }


    /**
     * Retrieve a member of the $_ENV superglobal
     *
     * If no $key is passed, returns the entire $_ENV array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getEnv($key = null, $default = null) {
        if (null === $key) {
            return $_ENV;
        }

        return (isset($_ENV[$key])) ? $_ENV[$key] : $default;
    }


    /**
     * Return the method by which the request was made
     *
     * @return string
     */
    public function getMethod() {
        return $this->getServer('REQUEST_METHOD');
    }


    /**
     * Was the request made by POST?
     *
     * @return boolean
     */
    public function isPost() {
        if ('POST' == $this->getMethod()) {
            return true;
        }

        return false;
    }


    /**
     * Was the request made by GET?
     *
     * @return boolean
     */
    public function isGet() {
        if ('GET' == $this->getMethod()) {
            return true;
        }

        return false;
    }


    public function isSSL()
    {
    	return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? true : false;
    }


    /**
     * Is the request a Javascript XMLHttpRequest?
     * Should work with Prototype/Script.aculo.us, possibly others.
     *
     * @return boolean
     */
    public function isXmlHttpRequest() {
        return ($this->getHeader('X_REQUESTED_WITH') == 'XMLHttpRequest');
    }


    /**
     * Return the value of the given HTTP header. Pass the header name as the
     * plain, HTTP-specified header name. Ex.: Ask for 'Accept' to get the
     * Accept header, 'Accept-Encoding' to get the Accept-Encoding header.
     *
     * @param string $header HTTP header name
     * @return string|false HTTP header value, or false if not found
     */
    public function getHeader($header) {
        if (empty($header)) {
            trigger_error('An HTTP header name is required', E_USER_ERROR);
        }

        // Try to get it from the $_SERVER array first
        $temp = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
        if (!empty($_SERVER[$temp])) {
            return $_SERVER[$temp];
        }

        // This seems to be the only way to get the Authorization header on
        // Apache
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (!empty($headers[$header])) {
                return $headers[$header];
            }
        }

        return false;
    }


    /**
     * Singleton
     *
     * @return Nip_Request_Http
     */
    public function instance() {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }
}