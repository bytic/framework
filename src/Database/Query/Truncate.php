<?php

namespace Nip\Database\Query;

class Truncate extends AbstractQuery
{

	public function assemble()
	{
		return 'TRUNCATE TABLE ' . $this->getTable();
	}

}