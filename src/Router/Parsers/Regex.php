<?php

namespace Nip\Router\Parsers;

/**
 * Class Regex
 * @package Nip\Router\Parsers
 */
class Regex extends AbstractParser
{
    protected $regex;

    public function parseMap()
    {
        parent::parseMap();
        $this->regex = false;
    }

    /**
     * @param $uri
     * @return bool
     */
    public function match($uri)
    {
        $return = parent::match($uri);

        if ($return) {
            $match = preg_match_all("`^" . $this->getRegex() . "$`i", $uri, $matches);

            if ($match > 0) {
                $variables = $this->getVariables();
                if (count($variables)) {
                    foreach ($variables as $key => $variable) {
                        $this->matches[$variable] = $matches[$key + 1][0];
                    }
                }
                return true;
            }
        }

        return false;
    }

    /**
     * @return mixed|string
     */
    public function getRegex()
    {
        if (!$this->regex) {
            $map = $this->map;
            foreach ($this->params as $key => $value) {
                if (stristr($map, ":" . $key) !== false) {
                    $map = str_replace(":" . $key, "(" . $value . ")", $map);
//                    unset($params[$key]);
                    $this->variables[] = $key;
                }
            }
            $this->regex = $map;
        }

        return $this->regex;
    }
}
