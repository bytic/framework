<?php

namespace Nip\DebugBar;

class DebugBarServiceProvider
{


    protected $debugBar = null;


    /**
     * @return StandardDebugBar
     */
    public function getDebugBar()
    {
        if ($this->debugBar == null) {
            $this->initDebugBar();
        }

        return $this->debugBar;
    }

    /**
     * @param null $debugBar
     */
    public function setDebugBar($debugBar)
    {
        $this->debugBar = $debugBar;
    }

    public function initDebugBar()
    {
        $this->setDebugBar($this->newDebugBar());
    }

    /**
     * @return StandardDebugBar
     */
    public function newDebugBar()
    {
        $debugBar = new StandardDebugBar();

        return $debugBar;
    }
}