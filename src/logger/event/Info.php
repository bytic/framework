<?php

namespace Nip\Logger\Event;

use Nip\Logger\Manager;
use Nip\Logger\Event;

class Info extends Event {

    protected $_type = Manager::EVENT_INFO;

}