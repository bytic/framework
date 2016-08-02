<?php

namespace Nip\Router\Parser;

class Literal extends AbstractParser
{

    public function match($uri)
    {
        $return = parent::match($uri);

        if ($return) {
            return $this->_map == $uri;
        }
    }


    public function assemble($params = array())
    {
        $params = $this->stripEmptyParams($params);
        return $this->_map . ($params ? '?' . http_build_query($params) : '');
    }

}
