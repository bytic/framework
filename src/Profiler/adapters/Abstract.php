<?php

namespace Nip\Profiler\Adapters;

use Nip\Profiler\Profile;

abstract class AbstractAdapter
{

    abstract public function write(Profile $profile);
}