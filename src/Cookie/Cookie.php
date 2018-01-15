<?php

namespace Nip\Cookie;

class Cookie
{
    protected $name;
    protected $value;
    protected $expires;
    protected $expiresTimer;
    protected $domain;
    protected $path = '/';
    protected $secure;

    public function setName($name)
    {
        if ($name) {
            if (!preg_match("/[=,; \t\r\n\013\014]/", $name)) {
                $this->name = $name;
            }
        }

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setExpire($expires)
    {
        $this->expires = $expires;

        return $this;
    }

    public function getExpire()
    {
        return $this->expires;
    }

    public function setExpireTimer($expires)
    {
        $this->expiresTimer = $expires;

        return $this;
    }

    public function getExpireTimer()
    {
        return $this->expiresTimer;
    }

    public function setSecured($secured)
    {
        $this->secure = $secured;

        return $this;
    }

    public function isSecure()
    {
        return (bool) $this->secure;
    }

    public function isExpired()
    {
        if (is_int($this->expires) && $this->expires < time()) {
            return true;
        } else {
            return false;
        }
    }

    public function save()
    {
        $expire = $this->getExpire();
        if (!$expire) {
            $timer = $this->getExpireTimer() ? $this->getExpireTimer() : 3 * 60 * 60;
            $expire = time() + $timer;
        }
        $domain = ($this->getDomain() != 'localhost') ? $this->getDomain() : false;

        return setcookie(
                $this->getName(),
                $this->getValue(),
                $expire,
                $this->getPath(),
                $domain,
                $this->isSecure());
    }
}
