<?php

use Nip\Container\Container;

/**
 * @return \Nip\Database\Connection
 */
function db()
{
    return Container::getInstance()->get('db.connection');
}