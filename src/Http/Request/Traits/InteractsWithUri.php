<?php

namespace Nip\Http\Request\Traits;

/**
 * Class InteractsWithUri
 * @package Nip\Http\Request\Traits
 */
trait InteractsWithUri
{

    /**
     * Get the root URL for the application.
     *
     * @return string
     */
    public function root()
    {
        return rtrim($this->getSchemeAndHttpHost() . $this->getBaseUrl(), '/');
    }

    /**
     * Get the full URL for the request.
     *
     * @return string
     */
    public function fullUrl()
    {
        $query = $this->getQueryString();
        $question = $this->getBaseUrl() . $this->getPathInfo() == '/' ? '/?' : '?';
        return $query ? $this->url() . $question . $query : $this->url();
    }

    /**
     * Get the URL (no query string) for the request.
     *
     * @return string
     */
    public function url()
    {
        return rtrim(preg_replace('/\?.*/', '', $this->getUri()), '/');
    }

    /**
     * @return array|mixed|string
     */
    protected function prepareRequestUri()
    {
        if ((int) $this->server->get('REDIRECT_STATUS', '200') >= 400 && $this->server->has('REDIRECT_URL')) {
            $requestUri = $this->server->get('REDIRECT_URL');
            $schemeAndHttpHost = $this->getSchemeAndHttpHost();
            if (strpos($requestUri, $schemeAndHttpHost) === 0) {
                $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
            }
            $this->server->set('REQUEST_URI', $requestUri);
            return $requestUri;
        }

        return parent::prepareRequestUri();
    }
}
