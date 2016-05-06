<?php

class Nip_DB_Query_Replace extends Nip_DB_Query_Insert
{

	public function assemble()
	{
		$query = "REPLACE INTO " . $this->protect($this->getTable()) . $this->parseCols() . $this->parseValues();
		return $query;
	}

}