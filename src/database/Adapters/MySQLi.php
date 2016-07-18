<?php

use \Nip\Database\Adapters\AbstractAdapter;

class Nip_DB_Adapters_MySQLi extends AbstractAdapter implements Nip_DB_Adapters_Interface
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
		$this->_connection = mysqli_connect($host, $user, $password, $newLink);
        
		if ($this->_connection) {
			if ($this->selectDatabase($database)) {
				$this->query("SET CHARACTER SET utf8");
				$this->query("SET NAMES utf8");
				return $this->_connection;
			} else {
				$message = 'Cannot select database ' . $database;
			}
		} else {
			$message = mysqli_error();
		}

		if (!$this->_connection) {
			trigger_error($message, E_USER_WARNING);
		}

		return false;
	}


    public function selectDatabase($database)
    {
        return mysqli_select_db($this->_connection, $database);
    }

	/**
	 * Executes SQL query
	 *
	 * @param string $sql
	 * @return result ID
	 */
	public function query($sql)
	{
		return mysqli_query($this->_connection, $sql);
	}

	public function lastInsertID()
	{
		return mysqli_insert_id($this->_connection);
	}

	public function affectedRows()
	{
		return mysqli_affected_rows($this->_connection);
	}

	public function info()
	{
		return mysqli_info($this->_connection);
	}

	public function numRows($result)
	{
		return mysqli_num_rows($result);
	}

	public function fetchArray($result)
	{
		return mysqli_fetch_array($result);
	}

	public function fetchAssoc($result)
	{
		return mysqli_fetch_assoc($result);
	}

	public function fetchObject($result)
	{
		return mysqli_fetch_object($result);
	}

	public function result($result, $row, $field)
	{
		return mysqli_result($result, $row, $field);
	}

	public function freeResults($result)
	{
		return mysqli_free_result($result);
	}

	public function describeTable($table)
	{
		$return = array('fields' => array(), 'indexes' => array());

		$result = $this->execute('SHOW INDEX IN ' . $table);
		if (mysqli_num_rows($result)) {
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
		if (mysqli_num_rows($result)) {
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
		return mysqli_real_escape_string($this->_connection, $data);
	}

	public function error()
	{
		return mysqli_error($this->_connection);
	}

	public function disconnect()
	{
		mysqli_close($this->_connection);
	}

}