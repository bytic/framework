<?php

/**
 * From Symfony Symfony\Component\HttpFoundation\ServerBag class
 */

namespace Nip\Request;

class ServerBag extends ParameterBag
{
    protected $_maxFileSize = null;

    /**
     * Gets the HTTP headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        $headers = [];
        $contentHeaders = array('CONTENT_LENGTH' => true, 'CONTENT_MD5' => true, 'CONTENT_TYPE' => true);
        foreach ($this->parameters as $key => $value) {
            if (0 === strpos($key, 'HTTP_')) {
                $headers[substr($key, 5)] = $value;
            } // CONTENT_* are not prefixed with HTTP_
            elseif (isset($contentHeaders[$key])) {
                $headers[$key] = $value;
            }
        }
        if (isset($this->parameters['PHP_AUTH_USER'])) {
            $headers['PHP_AUTH_USER'] = $this->parameters['PHP_AUTH_USER'];
            $headers['PHP_AUTH_PW'] = isset($this->parameters['PHP_AUTH_PW']) ? $this->parameters['PHP_AUTH_PW'] : '';
        } else {
            /*
             * php-cgi under Apache does not pass HTTP Basic user/pass to PHP by default
             * For this workaround to work, add these lines to your .htaccess file:
             * RewriteCond %{HTTP:Authorization} ^(.+)$
             * RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
             *
             * A sample .htaccess file:
             * RewriteEngine On
             * RewriteCond %{HTTP:Authorization} ^(.+)$
             * RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
             * RewriteCond %{REQUEST_FILENAME} !-f
             * RewriteRule ^(.*)$ app.php [QSA,L]
             */
            $authorizationHeader = null;
            if (isset($this->parameters['HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $this->parameters['HTTP_AUTHORIZATION'];
            } elseif (isset($this->parameters['REDIRECT_HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $this->parameters['REDIRECT_HTTP_AUTHORIZATION'];
            }
            if (null !== $authorizationHeader) {
                if (0 === stripos($authorizationHeader, 'basic ')) {
                    // Decode AUTHORIZATION header into PHP_AUTH_USER and PHP_AUTH_PW when authorization header is basic
                    $exploded = explode(':', base64_decode(substr($authorizationHeader, 6)), 2);
                    if (count($exploded) == 2) {
                        list($headers['PHP_AUTH_USER'], $headers['PHP_AUTH_PW']) = $exploded;
                    }
                } elseif (empty($this->parameters['PHP_AUTH_DIGEST']) && (0 === stripos($authorizationHeader, 'digest '))) {
                    // In some circumstances PHP_AUTH_DIGEST needs to be set
                    $headers['PHP_AUTH_DIGEST'] = $authorizationHeader;
                    $this->parameters['PHP_AUTH_DIGEST'] = $authorizationHeader;
                } elseif (0 === stripos($authorizationHeader, 'bearer ')) {
                    /*
                     * XXX: Since there is no PHP_AUTH_BEARER in PHP predefined variables,
                     *      I'll just set $headers['AUTHORIZATION'] here.
                     *      http://php.net/manual/en/reserved.variables.server.php
                     */
                    $headers['AUTHORIZATION'] = $authorizationHeader;
                }
            }
        }
        if (isset($headers['AUTHORIZATION'])) {
            return $headers;
        }
        // PHP_AUTH_USER/PHP_AUTH_PW
        if (isset($headers['PHP_AUTH_USER'])) {
            $headers['AUTHORIZATION'] = 'Basic ' . base64_encode($headers['PHP_AUTH_USER'] . ':' . $headers['PHP_AUTH_PW']);
        } elseif (isset($headers['PHP_AUTH_DIGEST'])) {
            $headers['AUTHORIZATION'] = $headers['PHP_AUTH_DIGEST'];
        }
        return $headers;
    }

    /**
     * @return integer
     */
    public function getMaxFileSize()
    {
        if ($this->_maxFileSize == null) {
            $ini = $this->convertIniToInteger(trim(ini_get('post_max_size')));
            $max = $this->convertIniToInteger(trim(ini_get('upload_max_filesize')));
            $min = min($ini, $max);
            $this->_maxFileSize = $min;
        }
        return $this->_maxFileSize;
    }

    /**
     * Converts a ini setting to a integer value
     *
     * @param  string $setting
     * @return integer
     */
    protected function convertIniToInteger($setting)
    {
        if (!is_numeric($setting)) {
            $type = strtoupper(substr($setting, -1));
            $setting = (integer) substr($setting, 0, -1);
            switch ($type) {
                case 'K' :
                    $setting *= 1024;
                    break;
                case 'M' :
                    $setting *= 1024 * 1024;
                    break;
                case 'G' :
                    $setting *= 1024 * 1024 * 1024;
                    break;
                default :
                    break;
            }
        }
        return (integer) $setting;
    }
}