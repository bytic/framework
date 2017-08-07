<?php

namespace Nip\Http\Response;

use Psr\Http\Message\ResponseInterface;

/**
 * Class ContainerAwareTrait
 * @package Nip\Container
 */
trait ResponseAwareTrait
{
    /**
     * @var Response|null
     */
    protected $response = null;

    /**
     * Get the container.
     *
     * @return Response
     */
    public function getResponse()
    {
        if ($this->response == null) {
            $this->initResponse();
        }

        return $this->response;
    }

    /**
     * Set a container.
     *
     * @param Response|ResponseInterface $response
     * @return $this
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;

        return $this;
    }

    public function initResponse()
    {
        $this->response = $this->newResponse();
    }

    /**
     * @return Response
     */
    public function newResponse()
    {
        return new Response();
    }
}
