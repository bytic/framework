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

class Nip_DB_Profiler extends Nip_Profiler {

    public $filterTypes = null;    
    

	public function start($queryText = false) {
		if (!$this->checkEnabled()) {
			return;
		}

		// make sure we have a query type
		switch (strtolower(substr($queryText, 0, 6))) {
			case 'insert':
				$queryType = 'INSERT';
				break;

			case 'update':
				$queryType = 'UPDATE';
				break;

			case 'delete':
				$queryType = 'DELETE';
				break;

			case 'select':
				$queryType = 'SELECT';
				break;

			default:
				$queryType = 'QUERY';
				break;
		}

		$this->profiles[] = new Nip_DB_Profiler_Query($queryText, $queryType);

		$profileID = $this->lastProcessID();
		$this->addRunningProces($profileID);
		return $profileID;
	}
    

	public function end($profileID = false) {
		if (!$this->checkEnabled()) {
			return;
		}

		if ($profileID == false) {
			$profileID = $this->getLastRunningProces();
		}

		$profile = $this->endProfile($profileID);
		if (is_object($profile)) {
			$this->secondsFilter($profile);
			$this->typeFilter($profile);
		}
		return;
	}


	public function typeFilter($profile){
		if (is_array($this->filterTypes) && in_array($profile->type, $this->filterTypes)) {
			$this->deleteProfile($profileID);
			return;
		}
	}


	public function setFilterQueryType($queryTypes = null){
		$this->filterTypes = $queryTypes;

		return $this;
	}


	/**
	 * Singleton
	 *
	 * @return Nip_DB_Profiler
	 */
	public static function instance() {
		static $instance;
		if (!($instance instanceof self)) {
			$instance = new self();
		}
		return $instance;
	}
}