<?php

class Nip_DB_Adapters_MySQL extends Nip_DB_Adapters_Abstract implements Nip_DB_Adapters_Interface
{

	protected $_connection;

	/**
	 * Connects to MySQL server
	 *
	 * @param string $host
	 * @param string $user
	 * @param string $password
	 * @param string $database
	 * @return resource
	 */
	public function connect($host = false, $user = false, $password = false, $database = false, $newLink = false)
	{
		$this->_connection = mysql_connect($host, $user, $password, $newLink);

		if ($this->_connection) {
			if (mysql_select_db($database, $this->_connection)) {
				$this->query("SET CHARACTER SET utf8");
				$this->query("SET NAMES utf8");
				return $this->_connection;
			} else {
				$message = 'Cannot select database ' . $database;
			}
		} else {
			$message = mysql_error();
		}

		if (!$this->_connection) {
			trigger_error($message, E_USER_WARNING);
		}

		return false;
	}

	/**
	 * Executes SQL query
	 *
	 * @param string $sql
	 * @return result ID
	 */
	public function query($sql)
	{
		return mysql_query($sql, $this->_connection);
	}

	public function lastInsertID()
	{
		return mysql_insert_id($this->_connection);
	}

	public function affectedRows()
	{
		return mysql_affected_rows($this->_connection);
	}

	public function info()
	{
		return mysql_info($this->_connection);
	}

	public function numRows($result)
	{
		return mysql_num_rows($result);
	}

	public function fetchArray($result)
	{
		return mysql_fetch_array($result);
	}

	public function fetchAssoc($result)
	{
		return mysql_fetch_assoc($result);
	}

	public function fetchObject($result)
	{
		return mysql_fetch_object($result);
	}

	public function result($result, $row, $field)
	{
		return mysql_result($result, $row, $field);
	}

	public function freeResults($result)
	{
		return mysql_free_result($result);
	}

	public function describeTable($table)
	{
		$return = array('fields' => array(), 'indexes' => array());

		$result = $this->execute('SHOW INDEX IN ' . $table);
		if (mysql_num_rows($result)) {
			while ($row = $this->fetchAssoc($result)) {
				if (!$return['indexes'][$row['Key_name']]) {
					$return['indexes'][$row['Key_name']] = array();
				}
				$return['indexes'][$row['Key_name']]['fields'][] = $row['Column_name'];
				$return['indexes'][$row['Key_name']]['unique'] = $row['Non_unique'] == '0';
				$return['indexes'][$row['Key_name']]['fulltext'] = $row['Index_type'] == 'FULLTEXT';
				$return['indexes'][$row['Key_name']]['type'] = $row['Index_type'];
			}
		}

		$result = $this->execute('DESCRIBE ' . $table);
		if (mysql_num_rows($result)) {
			while ($row = $this->fetchAssoc($result)) {
				$return['fields'][$row['Field']] = array(
					'field' => $row['Field'],
					'type' => $row['Type'],
					'primary' => ($return['indexes']['PRIMARY']['fields'][0] == $row['Field']),
					'default' => $row['Default'],
					'auto_increment' => ($row['Extra'] === 'auto_increment')
				);
			}
		}

		return $return;
	}

	public function getTables()
	{
		$return = array();
		
		$result = $this->execute("SHOW FULL TABLES");
		if ($this->numRows($result)) {
			while ($row = $this->fetchArray($result)) {
				$return[$row[0]] = array(
					"type" => $row[1] == "BASE TABLE" ? "table" : "view"
				);
			}
		}

		return $return;
	}

	public function quote($value)
	{
		$value = $this->cleanData($value);
		return is_numeric($value) ? $value : "'$value'";
	}

	public function cleanData($data)
	{
		return mysql_real_escape_string($data);
	}

	public function error()
	{
		return mysql_error();
	}

	public function disconnect()
	{
		mysql_close($this->_connection);
	}

}