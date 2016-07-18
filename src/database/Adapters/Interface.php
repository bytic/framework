<?php
interface Nip_DB_Adapters_Interface
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