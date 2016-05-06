<?php

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id: Dynamic.php 135 2009-05-27 16:48:23Z victor.stanciu $
 */
class Nip_Route_Default extends Nip_Route_Dynamic
{

    protected $_map = ':controller/:action';

    public function  assemble($params = array())
    {
        if (!$params['action']) {
            $params['action'] = '';
        }
        
        return parent::assemble($params);
    }
    public function match($uri) {
        $return = parent::match($uri);
        if ($return && !empty($this->_params['controller'])) {
            return $return;
        }
        return false;
    }

    protected function preMatch() {
        $mapCount = count($this->_parts);
        $uriCount = substr_count($this->_uri, '/') + 1;
        $difference = $mapCount - $uriCount;
        if ($difference == 0 || $difference == 1) {
            return true;
        }

        return false;
    }


}