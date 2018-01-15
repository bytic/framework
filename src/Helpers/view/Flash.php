<?php

namespace Nip\Helpers\View;

use Nip_Flash_Messages;

class Flash extends AbstractHelper
{
    public function hasKey($key)
    {
        return Nip_Flash_Messages::instance()->has($key);
    }

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

    public function getData($key)
    {
        $this->data = Nip_Flash_Messages::instance()->get($key);

        return $this->data;
    }
}
