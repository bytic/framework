<?php

namespace Nip\Router\Parser;

class Regex extends AbstractParser
{
    protected $_regex;
    protected $_variables = [];

    public function parseMap()
    {
        parent::parseMap();
        $this->_regex = false;
    }

    public function match($uri)
    {
        $return = parent::match($uri);

        if ($return) {
            $match = preg_match_all("`^" . $this->getRegex() . "$`i", $uri, $matches);

            if ($match > 0) {
                if ($this->_variables) {
                    foreach ($this->_variables as $key => $variable) {
                        $this->_matches[$variable] = $matches[$key + 1][0];
                    }
                }
                return true;
            }
        }

        return false;
    }

    public function getRegex()
    {
        if (!$this->_regex) {
            $map = $this->_map;
            foreach ($this->_params as $key => $value) {
                if (stristr($map, ":" . $key) !== false) {
                    $map = str_replace(":" . $key, "(" . $value . ")", $map);
                    unset($params[$key]);
                    $this->_variables[] = $key;
                }
            }
            $this->_regex = $map;
        }

        return $this->_regex;
    }
}