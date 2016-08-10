<?php

namespace Nip\Router\Parser;

class Dynamic extends AbstractParser
{

    protected $_variables = array();

    public function parseMap()
    {
        parent::parseMap();
        foreach ($this->_parts as &$part) {
            $variablesCount = substr_count($part, ":");
            if ($variablesCount >= 1) {
                if ($variablesCount == 1 && strpos($part, ':') === 0) {
                    $this->_variables[] = str_replace(":", "", $part);
                } else {
                    $variables = $this->getVariableFromPart($part);
                    $this->_variables = array_merge($this->_variables, $variables);
                }
            }
        }
    }

    public function getVariableFromPart($part)
    {
        $len = strlen($part);
        $variables = array();
        $variable = false;
        $letters = array_merge(range('A', 'Z'), range('a', 'z'));
        for ($i=0; $i < $len; $i++) {
            $char = $part[$i];
            if ($char == ':') {
                if ($variable) {
                    $variables[] = $variable;
                }
                $variable = '';
            } else {
                $isLetter = in_array($char, $letters);
                $isAllowed = in_array($char, array('_'));
                if (($isLetter || $isAllowed) && $variable !== false) {
                    $variable .= $char;
                } elseif ($variable !== false) {
                    $variables[] = $variable;
                    $variable = false;
                }
            }
        }
        if (!empty ($variable)) {
            $variables[] = $variable;
        }
        return $variables;
    }

    public function match($uri)
    {
        $return = parent::match($uri);

        if ($return) {
            if ($this->_uri[strlen($this->_uri) - 1] == '/') {
                $this->_uri = substr($this->_uri, 0, -1);
            }


            if ($this->getVariableParts($uri)) {
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

    protected function preMatch()
    {
        if (count($this->_parts) != (substr_count($this->_uri, '/') + 1)) {
            return false;
        }

        return true;
    }

    protected function postMatch()
    {
        return true;
    }

    public function getVariableParts($url)
    {
        $this->_uriParts = explode("/", $url);

        foreach ($this->_parts as $key => $part) {
            if (strpos($part, ':') !== false) {
                break;
            }
            if ($this->_uriParts[$key] != $part) {
                return false;
            }
            unset($this->_uriParts[$key]);
        }

        return true;
    }

    protected function parseParams()
    {
        $uriParts = explode("/", $this->_uri);
        foreach ($this->_parts as $key => $part) {
            if (strstr($part, ":") === false) {
                // part is static - no named params
                if ($uriParts[$key] != $part) {
                    // corresponding part in URI does not match
                    return false;
                }
            } else {
                $var = str_replace(":", "", $part);
                $this->_params[$var] = $uriParts[$key];
            }
        }
    }

    public function getVariables()
    {
        return $this->_variables;
    }
}