<?php

namespace Nip\Database\Adapters;

interface AdapterInterface
{

	function execute($sql);

	function lastInsertID();

	function affectedRows();

	function numRows($result);

	function fetchArray($result);

	function fetchAssoc($result);

	function fetchObject($result);

	function result($result, $row, $field);

	function freeResults($result);

	function describeTable($table);

	function cleanData($data);

	function error();

	function disconnect();
}