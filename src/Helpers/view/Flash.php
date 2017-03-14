<?php

namespace Nip\Helpers\View;

/**
 * Class Flash
 * @package Nip\Helpers\View
 */
class Flash extends AbstractHelper
{

    /**
     * @param $key
     * @return bool
     */
    public function hasKey($key)
    {
        return app('flash.messages')->has($key);
    }

    /**
     * @param $key
     * @return string
     */
    public function render($key)
    {
        $return = '';

        $data = $this->getData($key);

        if (is_array($data)) {
            foreach ($data as $type => $message) {
                $return .= $this->getView()->Messages()->$type($message);
            }
        }

        return $return;
    }

    /**
     * @param $key
     * @return mixed|object
     */
    public function getData($key)
    {
        $this->data = app('flash.messages')->get($key);
        return $this->data;
    }
}
