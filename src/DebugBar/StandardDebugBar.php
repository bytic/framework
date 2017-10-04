<?php

namespace Nip\DebugBar;

use DebugBar\DataCollector\ExceptionsCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\TimeDataCollector;
use Monolog\Logger as Monolog;
use Nip\Database\Connections\Connection;
use Nip\Database\DatabaseManager;
use Nip\DebugBar\DataCollector\QueryCollector;
use Nip\DebugBar\DataCollector\RouteCollector;
use Nip\Profiler\Adapters\DebugBar as ProfilerDebugBar;

/**
 * Class StandardDebugBar
 * @package Nip\DebugBar
 */
class StandardDebugBar extends DebugBar
{
    public function doBoot()
    {
        $this->addCollector(new PhpInfoCollector());
        $this->addCollector(new MessagesCollector());
        $this->addCollector(new RequestDataCollector());
        $this->addCollector(new TimeDataCollector());
        $this->addCollector(new MemoryCollector());
        $this->addCollector(new RouteCollector());

        if (app()->has('db')) {
            $this->addQueryCollector();
        }

        if (app()->has(Monolog::class)) {
            $monolog = app(Monolog::class);
            $this->addMonolog($monolog);
        } else {
            $this->addCollector(new ExceptionsCollector());
        }
    }

    public function addQueryCollector()
    {
        $this->addCollector(new QueryCollector());

        $databaseManager = app('db');
        $databaseManager->connection();

        $this->populateQueryCollector();
    }

    public function populateQueryCollector()
    {
        /** @var DatabaseManager $databaseManager */
        $databaseManager = app('db');
        $connections = $databaseManager->getConnections();

        foreach ($connections as $connection) {
            $this->initDatabaseConnection($connection);
        }
    }

    /**
     * @param Connection $connection
     */
    public function initDatabaseConnection($connection)
    {
        $profiler = $connection->getAdapter()->newProfiler()->setEnabled(true);
        $writer = $profiler->newWriter('DebugBar');

        /** @var ProfilerDebugBar $writer */
        $writer->setCollector($this->getCollector('queries'));
        $profiler->addWriter($writer);
        $connection->getAdapter()->setProfiler($profiler);
    }
}
