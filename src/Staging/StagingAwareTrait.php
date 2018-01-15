<?php

namespace Nip\Staging;

/**
 * Class StagingAwareTrait.
 */
trait StagingAwareTrait
{
    /**
     * @var Staging|null
     */
    protected $staging = null;

    /**
     * Get the container.
     *
     * @return Staging
     */
    public function getStaging()
    {
        if ($this->staging == null) {
            $this->initStaging();
        }

        return $this->staging;
    }

    /**
     * Set a container.
     *
     * @param Staging $staging
     *
     * @return $this
     */
    public function setStaging($staging)
    {
        $this->staging = $staging;

        return $this;
    }

    public function initStaging()
    {
        $this->setStaging($this->newStaging());
    }

    /**
     * @return Staging
     */
    public function newStaging()
    {
        return app()->get('staging');
    }
}
