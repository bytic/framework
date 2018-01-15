<?php

namespace Nip;

use Nip\Database\Connection;
use Nip\Database\Result;

class Session
{
    protected $id;
    protected $_lifetime;
    protected $db;
    protected $table = 'session';

    public function __construct()
    {
        $this->db = db();
        $this->_lifetime = get_cfg_var('session.gc_maxlifetime');

        //		ini_set('session.save_handler', 'user');
        register_shutdown_function('session_write_close');
    }

    public function init()
    {
        $id = $this->checkRequestId();
        //		$this->setHandlers();
        $this->start($id);
    }

    /**
     * Gets the session ID from REQUEST.
     *
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
     * Starts the session, with optional session id.
     *
     * @param string|bool $id
     */
    protected function start($id = false)
    {
        if ($id) {
            session_id($id);
        }
        session_start();
    }

    /**
     * Restarts the session, with new optional session id.
     *
     * @param string|bool $id
     */
    public function reinitialize($id = false)
    {
        session_write_close();
        $this->setHandlers()->start($id);
    }

    /**
     * Overrides default session handling functions.
     *
     * @return Session
     */
    protected function setHandlers()
    {
        session_set_save_handler(
            [$this, 'open'],
            [$this, 'close'],
            [$this, 'read'],
            [$this, 'write'],
            [$this, 'destroy'],
            [$this, 'gc']
        );

        return $this;
    }

    public function open()
    {
        return true;
    }

    public function close()
    {
        return true;
    }

    /**
     * Public method to return the session id.
     *
     * @todo implement a verification method ( ex: adding another validation string in the sessionID )
     *
     * @return int
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * Fetches session entry from database.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function read($id)
    {
        /* @var $result Result */
        $query = $this->db->newQuery('select');
        $query->from($this->table)
            ->where('id = ?', $id)
            ->where('expires > ?', time())
            ->limit(1);
        $result = $this->db->execute($query);

        $return = false;
        if ($result->numRows()) {
            $row = $result->fetchResult();
            $return = $this->decodeData($row['data']);
        }

        return $return;
    }

    /**
     * Decodes data to be used.
     *
     * @param string $data
     *
     * @return mixed
     */
    protected function decodeData($data)
    {
        return base64_decode($data);
    }

    /**
     * Stores the surrent session in the database.
     *
     * @param string $id
     * @param mixed  $data
     *
     * @return int
     */
    public function write($id, $data)
    {
        $replace = [];
        $replace['id'] = $id;
        $replace['data'] = $this->encodeData($data);
        $replace['expires'] = time() + $this->_lifetime;

        $query = $this->db->newQuery('replace');
        $query->table($this->table)
            ->data($replace);
        $result = $this->db->execute($query);

        return $this->db->affectedRows();
    }

    /**
     * Encodes data to be stored.
     *
     * @param mixed $data
     *
     * @return string
     */
    protected function encodeData($data)
    {
        return base64_encode($data);
    }

    /**
     * Destroys current session and deletes it's entry from the database.
     *
     * @param string $id
     *
     * @return int
     */
    public function destroy($id)
    {
        $query = $this->db->newQuery('delete');
        $query->table($this->table);
        $query->where('id = ?', $id);
        $query->limit(1);
        $query->execute();

        return $this->db->affectedRows();
    }

    /**
     * Garbage control. Called by PHP to remove expired entries from the database.
     *
     * @return int
     */
    public function gc()
    {
        $query = $this->db->newQuery('delete');
        $query->table($this->table);
        $query->where('expires <= ?', time());
        $query->execute();

        return $this->db->affectedRows();
    }

    /**
     * @param Connection $db
     *
     * @return $this
     */
    public function setDB($db)
    {
        $this->db = $db;

        return $this;
    }

    /**
     * @param int $lifetime
     *
     * @return $this
     */
    public function setLifetime($lifetime)
    {
        if ($lifetime && is_numeric($lifetime)) {
            $this->_lifetime = $lifetime;
        }

        return $this;
    }

    public function setRootDomain($domain)
    {
        if ($domain !== 'localhost') {
            ini_set('session.cookie_domain', '.'.$domain);
        }

        Cookie\Jar::instance()->setDefaults(
            ['domain' => '.'.$domain]
        );
    }

    public function isAutoStart()
    {
        return ini_get('session.auto_start') && (strtolower(ini_get('session.auto_start')) == 'on');
    }
}
