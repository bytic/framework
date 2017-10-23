<?php

namespace Nip\FlashData;

/**
 * Class FlashMessages
 * @package Nip\FlashData
 */
class FlashMessages extends FlashData
{
    protected $sessionKey = 'flash-messages';

    /**
     * @param $var
     * @param $type
     * @param bool $value
     * @return $this
     */
    public function add($var, $type, $value = false)
    {
        if (!is_array($this->next[$var][$type])) {
            $this->next[$var][$type] = [$value];
        } else {
            if (is_array($value)) {
                $this->next[$var][$type] = [$this->next[$var][$type], $value];
            } else {
                $this->next[$var][$type][] = $value;
            }
        }

        $this->write();
        return $this;
    }
}
