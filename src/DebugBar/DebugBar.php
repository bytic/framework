<?php

namespace Nip\DebugBar;

use DebugBar\DebugBar as DebugBarGeneric;
use DebugBar\Bridge\MonologCollector;

use Nip\DebugBar\Formatter\MonologFormatter;
use Monolog\Logger as MonologLogger;

abstract class DebugBar extends DebugBarGeneric
{

    /**
     * True when booted.
     *
     * @var bool
     */
    protected $booted = false;
    /**
     * True when enabled, false disabled an null for still unknown
     *
     * @var bool
     */
    protected $enabled = null;


    /**
     * Enable the DebugBar and boot, if not already booted.
     */
    public function enable()
    {
        $this->enabled = true;

        if (!$this->booted) {
            $this->boot();
        }
    }

    /**
     * Disable the DebugBar
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * Check if the DebugBar is enabled
     * @return boolean
     */
    public function isEnabled()
    {
        if ($this->enabled === null) {
            $this->enabled = true;
        }
        return $this->enabled;
    }

    public function boot()
    {
        if ($this->booted) {
            return;
        }

        $this->doBoot();
    }

    public function doBoot()
    {
    }

    public function addMonolog(MonologLogger $monolog)
    {
        $colector = new MonologCollector($monolog);
        $colector->setFormatter(new MonologFormatter());
        $this->addCollector($colector);
    }

    /**
     * Injects the web debug toolbar
     */
    public function injectDebugBar()
    {
        $renderer = $this->getJavascriptRenderer();
        $renderedContent = $renderer->renderHead() . $renderer->render();
        echo $renderedContent;
    }
}