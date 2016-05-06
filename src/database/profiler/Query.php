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
 * @version    SVN: $Id: Query.php 60 2009-04-28 14:50:04Z victor.stanciu $
 */

class Nip_DB_Profiler_Query extends Nip_Profile {
	public $columns = array('time',	'type',	'memory', 'query', 'affectedRows', 'info');

	public function __construct($query, $queryType) {
		$this->query = $query;

		parent::__construct($queryType);
	}
}