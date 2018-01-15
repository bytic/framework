<?php

function flash_get($name)
{
    return Nip_Flash::instance()->get($name);
}

function flash_add($name, $value)
{
    Nip_Flash::instance()->add($name, $value);
}

function flash_success($name, $message)
{
    Nip_Flash_Messages::instance()->add($name, 'success', $message);
}

function flash_error($name, $message)
{
    Nip_Flash_Messages::instance()->add($name, 'error', $message);
}

function flash_info($name, $message)
{
    Nip_Flash_Messages::instance()->add($name, 'info', $message);
}
