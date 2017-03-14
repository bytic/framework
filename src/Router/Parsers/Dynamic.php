<?php

namespace Nip\Router\Parsers;

/**
 * Class Dynamic
 * @package Nip\Router\Parsers
 */
class Dynamic extends AbstractParser
{
    /**
     * @var array
     */
    protected $uriParts = [];

    public function parseMap()
    {
        parent::parseMap();
        $this->parseMapForVariables();
    }

    protected function parseMapForVariables()
    {
        $parts = $this->getParts();
        foreach ($parts as &$part) {
            $variablesCount = substr_count($part, ":");
            if ($variablesCount >= 1) {
                if ($variablesCount == 1 && strpos($part, ':') === 0) {
                    $this->variables[] = str_replace(":", "", $part);
                } else {
                    $variables = $this->getVariableFromPart($part);
                    $variables = array_merge($this->variables, $variables);
                    $this->setVariables($variables);
                }
            }
        }
    }

    /**
     * @param $part
     * @return array
     */
    public function getVariableFromPart($part)
    {
        $len = strlen($part);
        $variables = [];
        $variable = false;
        $letters = array_merge(range('A', 'Z'), range('a', 'z'));
        for ($i = 0; $i < $len; $i++) {
            $char = $part[$i];
            if ($char == ':') {
                if ($variable) {
                    $variables[] = $variable;
                }
                $variable = '';
            } else {
                $isLetter = in_array($char, $letters);
                $isAllowed = in_array($char, ['_']);
                if (($isLetter || $isAllowed) && $variable !== false) {
                    $variable .= $char;
                } elseif ($variable !== false) {
                    $variables[] = $variable;
                    $variable = false;
                }
            }
        }
        if (!empty($variable)) {
            $variables[] = $variable;
        }

        return $variables;
    }

    /**
     * @param $uri
     * @return bool
     */
    public function match($uri)
    {
        $return = parent::match($uri);

        if ($return) {
//            if ($this->uri[strlen($this->uri) - 1] == '/') {
//                $this->uri = substr($this->uri, 0, -1);
//            }
            $this->parseUriParts($uri);
            if ($this->getVariableParts()) {
                if ($this->preMatch() === true) {
                    $this->parseParams();
                    if ($this->postMatch() == true) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param $uri
     */
    public function parseUriParts($uri)
    {
        $this->uriParts = explode("/", trim($uri, '/'));
    }

    /**
     * @return bool
     */
    public function getVariableParts()
    {
        foreach ($this->parts as $key => $part) {
            if (strpos($part, ':') !== false) {
                break;
            }
            if ($this->uriParts[$key] != $part) {
                return false;
            }
//            unset($this->uriParts[$key]);
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function preMatch()
    {
        $mapCount = count($this->getParts());
        $uriCount = count($this->getUriParts());
        if ($mapCount != $uriCount) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getUriParts()
    {
        return $this->uriParts;
    }

    /**
     * @param array $uriParts
     */
    public function setUriParts($uriParts)
    {
        $this->uriParts = $uriParts;
    }

    /**
     * @return bool
     */
    protected function parseParams()
    {
        $uriParts = $this->getUriParts();
        foreach ($this->parts as $key => $part) {
            if (strstr($part, ":") === false) {
                // part is static - no named params
                if (!isset($uriParts[$key]) || $uriParts[$key] != $part) {
                    // corresponding part in URI does not match
                    return false;
                }
            } else {
                $var = str_replace(":", "", $part);
                $this->setParam($var, isset($uriParts[$key]) ? $uriParts[$key] : null);
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function postMatch()
    {
        return true;
    }
}
