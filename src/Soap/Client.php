<?php

namespace Nip\Soap;

/**
 * Class Client
 * @package Nip\Soap
 */
class Client extends \SoapClient
{

    /**
     * SoapClient constructor
     * @inheritdoc
     */
    public function __construct($wsdl, $options)
    {
        $url = parse_url($wsdl);
        if ($url['port']) {
            $this->_port = $url['port'];
        }

        return parent::__construct($wsdl, $options);
    }

    /**
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int $version
     * @return string
     */
    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        $parts = parse_url($location);
        if ($this->_port) {
            $parts['port'] = $this->_port;
        }
        $location = $this->buildLocation($parts);

        $return = parent::__doRequest($request, $location, $action, $version, $one_way);

        return $return;
    }

    /**
     * @param array $parts
     * @return string
     */
    public function buildLocation($parts = [])
    {
        $location = '';

        if (isset($parts['scheme'])) {
            $location .= $parts['scheme'].'://';
        }
        if (isset($parts['user']) || isset($parts['pass'])) {
            $location .= $parts['user'].':'.$parts['pass'].'@';
        }
        $location .= $parts['host'];
        if (isset($parts['port'])) {
            $location .= ':'.$parts['port'];
        }
        $location .= $parts['path'];
        if (isset($parts['query'])) {
            $location .= '?'.$parts['query'];
        }

        return $location;
    }
}
