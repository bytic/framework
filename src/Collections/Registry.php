<?php

namespace Nip\Collections;

use ArrayAccess;

/**
 * Class Registry
 * @package Nip
 */
class Registry extends AbstractCollection implements ArrayAccess
{
    use ArrayAccessTrait;
}