<?php

namespace Nip\Session;

use Nip\Application;
use Nip\Cookie\Jar as CookieJar;

/**
 * Class SessionManager
 * @package Nip\Session
 */
class SessionManager
{
    protected $id;

    protected $_lifetime;

    /**
     * SessionManager constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->_lifetime = get_cfg_var("session.gc_maxlifetime");

        //		ini_set('session.save_handler', 'user');
        register_shutdown_function('session_write_close');
    }

    public function init()
    {
        $id = $this->checkRequestId();
        $this->start($id);
    }

    /**
     * Gets the session ID from REQUEST
     * @return int
     */
    public function checkRequestId()
    {
        if (isset($_REQUEST['session_id'])) {
            return $_REQUEST['session_id'];
        }

        return false;
    }

    /**
     * Starts the session, with optional session id
     * @param string|boolean $id
     */
    protected function start($id = false)
    {
        if ($id) {
            session_id($id);
        }
        session_start();
    }

    /**
     * Restarts the session, with new optional session id
     *
     * @param string|boolean $id
     */
    public function reinitialize($id = false)
    {
        session_write_close();
        $this->start($id);
    }

    /**
     * Public method to return the session id
     * @todo implement a verification method ( ex: adding another validation string in the sessionID )
     * @return string
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * @param int $lifetime
     * @return $this
     */
    public function setLifetime($lifetime)
    {
        if ($lifetime && is_numeric($lifetime)) {
            $this->_lifetime = $lifetime;
        }

        return $this;
    }

    /**
     * @param $domain
     */
    public function setRootDomain($domain)
    {
        if ($domain !== 'localhost') {
            ini_set('session.cookie_domain', '.' . $domain);
        }

        CookieJar::instance()->setDefaults(
            ['domain' => '.' . $domain]
        );
    }

    /**
     * @return bool
     */
    public function isAutoStart()
    {
        return ini_get('session.auto_start') && (strtolower(ini_get('session.auto_start')) == 'on');
    }
}
