<?php

namespace Nip\Database\Query;

class Replace extends Insert
{

	public function assemble()
	{
		$query = "REPLACE INTO " . $this->protect($this->getTable()) . $this->parseCols() . $this->parseValues();
		return $query;
	}

}