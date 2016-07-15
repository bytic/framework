<?php

use Nip\Database\Query\_Abstract;

class Nip_DB_Query_Delete extends _Abstract
{

	/**
	 * Joins together DELETE, FROM, WHERE, ORDER, and LIMIT parts of SQL query
	 * @return string
	 */
	public function assemble()
	{
		$where = $this->parseWhere();
		$order = $this->parseOrder();

		$query = 'DELETE FROM ' . $this->getManager()->protect($this->getTable());

		if (!empty($where)) {
			$query .= ' WHERE ' . $where;
		}

		if (!empty($order)) {
			$query .= ' ORDER BY ' . $order;
		}

		if (!empty($this->limit)) {
			$query .= ' LIMIT ' . $this->limit;
		}

		return $query;
	}

}