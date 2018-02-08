<?php

namespace Nip\Utility\Traits;

use Nip\Request;

/**
 * Class HasRequest.
 */
trait HasRequestTrait
{
    /**
     * @var Request
     */
    protected $_request;

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function setRequest($request)
    {
        $this->_request = $request;

        return $this;
    }
}
