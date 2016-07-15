<?php

use Nip\Database\Query\_Abstract;

class Nip_DB_Query_Truncate extends _Abstract
{

	public function assemble()
	{
		return 'TRUNCATE TABLE ' . $this->getTable();
	}

}