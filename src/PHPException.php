<?php

class Nip_PHPException extends \Exception
{
    public function log()
    {
        trigger_error($this->getMessage(), $this->getCode());
    }
}
