<?php

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id: Static.php 187 2009-06-22 18:49:32Z victorstanciu $
 */

class Nip_Route_Static extends Nip_Route_Abstract {

    public function match($uri) {
        return $this->_map == $uri;
    }


    public function assemble($params = array()) {
        $params = $this->stripEmptyParams($params);
        return $this->_map . ($params ? '?' . http_build_query($params) : '');
    }

}
