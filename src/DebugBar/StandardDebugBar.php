<?php

namespace Nip\DebugBar;

use DebugBar\DataCollector\ExceptionsCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\TimeDataCollector;
use Nip\DebugBar\DataCollector\QueryCollector;
use Nip\DebugBar\DataCollector\RouteCollector;

//use DebugBar\DataCollector\ConfigCollector;

class StandardDebugBar extends DebugBar
{
    public function doBoot()
    {
        $this->addCollector(new PhpInfoCollector());
        $this->addCollector(new MessagesCollector());
        $this->addCollector(new RequestDataCollector());
        $this->addCollector(new TimeDataCollector());
        $this->addCollector(new MemoryCollector());
        $this->addCollector(new ExceptionsCollector());
        $this->addCollector(new QueryCollector());
        $this->addCollector(new RouteCollector());
    }

    public function initDatabaseAdapter($adapter)
    {
        $profiler = $adapter->newProfiler()->setEnabled(true);
        $writer = $profiler->newWriter('DebugBar');

        /* @var ProfilerDebugBar $writer */
        $writer->setCollector($this->getCollector('queries'));
        $profiler->addWriter($writer);
        $adapter->setProfiler($profiler);
    }
}
