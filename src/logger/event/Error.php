<?php

namespace Nip\Logger\Event;

use Nip\Logger\Manager;
use Nip\Logger\Event;

class Error extends Event {

    protected $_type = Manager::EVENT_ERROR;

}