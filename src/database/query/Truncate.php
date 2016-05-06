<?php

class Nip_DB_Query_Truncate extends Nip_DB_Query_Abstract
{

	public function assemble()
	{
		return 'TRUNCATE TABLE ' . $this->getTable();
	}

}