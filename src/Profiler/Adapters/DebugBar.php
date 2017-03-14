<?php

namespace Nip\Profiler\Adapters;

use DebugBar\DataCollector\DataCollectorInterface;
use Nip\DebugBar\DataCollector\QueryCollector;
use Nip\Profiler\Profile;

/**
 * Class DebugBar
 * @package Nip\Profiler\Adapters
 */
class DebugBar extends AbstractAdapter
{

    /**
     * @var QueryCollector|DataCollectorInterface
     */
    protected $collector = null;

    /**
     * @param Profile $profile
     */
    public function write(Profile $profile)
    {
        $this->getCollector()->addQuery($profile);
    }


    /**
     * @return QueryCollector|DataCollectorInterface
     */
    public function getCollector()
    {
        return $this->collector;
    }

    /**
     * @param QueryCollector|DataCollectorInterface $collector
     */
    public function setCollector(QueryCollector $collector)
    {
        $this->collector = $collector;
    }
}
