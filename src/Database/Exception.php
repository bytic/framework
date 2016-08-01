<?php

namespace Nip\Database;

class Exception extends \Exception
{

    public function __construct($message, $code = E_USER_ERROR)
    {
        parent::__construct($message, $code);
    }

    public function log()
    {
    }

}