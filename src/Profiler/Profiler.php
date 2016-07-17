<?php

/**
 * Nip Framework
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @version    SVN: $Id: Profiler.php 60 2009-04-28 14:50:04Z victor.stanciu $
 */
class Nip_Profiler
{

    protected $adapter = null;

    public $profiles = array();
    public $runningProfiles = array();

    public $enabled = false;

    public $filterElapsedSecs = null;


    public function __construct()
    {
    }


    public function output($adapter = false)
    {
        if (!$this->checkEnabled()) {
            return;
        }

        if (!$adapter) {
            $adapter = 'Console';
        }

        $adapter = 'Nip_Profiler_Adapters_' . ucfirst($adapter);
        $this->adapter = new $adapter;

        if ($this->enabled === true) {
            $this->adapter->setProfiles($this->getProfiles());
            $this->adapter->output(get_class($this));
        }
    }


    public function setEnabled($enabled = false)
    {
        $this->enabled = (boolean)$enabled;
        return $this;
    }


    public function checkEnabled()
    {
        return $this->enabled;
    }


    public function clear()
    {
        $this->profiles = array();

        return $this;
    }


    public function start($profileID = false)
    {
        if (!$this->checkEnabled()) {
            return;
        }

        if ($profileID == false) {
            $profilesCount = count($this->getProfiles(null, true));
            $profileID = 'profile' . $profilesCount;
        }

        $this->profiles[$profileID] = new Nip_Profile($profileID);
        $this->addRunningProces($profileID);
    }


    public function end($profileID = false)
    {
        if (!$this->checkEnabled()) {
            return;
        }

        if ($profileID == false) {
            $profileID = $this->getLastRunningProces();
        }

        $profile = $this->endProfile($profileID);
        if (is_object($profile)) {
            $this->secondsFilter($profile);
        }
        return;
    }


    public function endProfile($profileID)
    {

        $profile = $this->getProfile($profileID);
        // Ensure that the query profile has not already ended
        if ($profile->hasEnded()) {
            trigger_error("Profile with profiler handle '$profileID' has already ended.", E_USER_ERROR);
        }

        $profile->end();

        $key = array_search($profileID, $this->runningProfiles);
        if (is_numeric($key)) {
            unset($this->runningProfiles[$key]);
        }

        return $profile;
    }


    public function lastProcessID()
    {
        end($this->profiles);
        return key($this->profiles);
    }


    public function addRunningProces($profileID)
    {
        $this->runningProfiles[] = $profileID;
    }


    public function getLastRunningProces()
    {
        return array_pop($this->runningProfiles);
    }


    public function secondsFilter($profile)
    {
        if (null !== $this->filterElapsedSecs && $profile->getElapsedSecs() < $this->filterElapsedSecs) {
            $this->deleteProfile($profileID);
            return;
        }
    }


    public function setFilterElapsedSecs($minimumSeconds = null)
    {
        if (null === $minimumSeconds) {
            $this->filterElapsedSecs = null;
        } else {
            $this->filterElapsedSecs = (integer)$minimumSeconds;
        }

        return $this;
    }


    public function getProfile($profileID)
    {
        if (!array_key_exists($profileID, $this->profiles)) {
            trigger_error("Profile handle '$profileID' not found in profiler log.", E_USER_ERROR);
        }

        $profile = $this->profiles[$profileID];
        $profile->profileID = $profileID;
        return $profile;
    }


    public function deleteProfile($profile)
    {
        if (!array_key_exists($profile->profileID, $this->profiles)) {
            trigger_error("Query handle '{$profile->profileID}' not found in profiler log.", E_USER_ERROR);
        }
        unset($this->profiles[$profile->profileID]);
        return;
    }


    public function getProfiles($type = null, $showUnfinished = false)
    {

        $profiles = array();
        foreach ($this->profiles as $key => $profile) {
            if ($type === null) {
                $condition = true;
            } else {
                $condition = ($profile->type && $type);
            }

            if (($profile->hasEnded() || $showUnfinished) && $condition) {
                $profiles[$key] = $profile;
            }
        }

        if (empty($profiles)) {
            $profiles = false;
        }

        return $profiles;
    }


    /**
     * Singleton
     *
     * @return Nip_Profiler
     */
    public static function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }
}