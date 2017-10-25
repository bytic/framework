<?php

namespace Nip\Database\Adapters;

/**
 * Class MySQLi
 * @package Nip\Database\Adapters
 */
class MySQLi extends AbstractAdapter implements AdapterInterface
{
    protected $connection;

    /**
     * Connects to MySQL server
     *
     * @param string|boolean $host
     * @param string|boolean $user
     * @param string|boolean $password
     * @param string|boolean $database
     * @param bool $newLink
     *
     * @return resource
     */
    public function connect($host = false, $user = false, $password = false, $database = false, $newLink = false)
    {
        $this->connection = mysqli_connect($host, $user, $password, $newLink);

        if ($this->connection) {
            if ($this->selectDatabase($database)) {
                $this->query("SET CHARACTER SET utf8");
                $this->query("SET NAMES utf8");
                return $this->connection;
            } else {
                $message = 'Cannot select database ' . $database;
            }
        } else {
            $message = mysqli_error($this->connection);
        }

        if (!$this->connection) {
            trigger_error($message, E_USER_WARNING);
        }
    }


    public function selectDatabase($database)
    {
        return mysqli_select_db($this->connection, $database);
    }

    /**
     * @param $sql
     * @return bool|\mysqli_result
     */
    public function query($sql)
    {
        return mysqli_query($this->connection, $sql);
    }

    /**
     * @return int|string
     */
    public function lastInsertID()
    {
        return mysqli_insert_id($this->connection);
    }

    /**
     * @return int
     */
    public function affectedRows()
    {
        return mysqli_affected_rows($this->connection);
    }

    /**
     * @return string
     */
    public function info()
    {
        return mysqli_info($this->connection);
    }

    /**
     * @param $result
     * @return null|object
     */
    public function fetchObject($result)
    {
        return mysqli_fetch_object($result);
    }

    /**
     * @param $result
     * @param $row
     * @param $field
     * @return mixed
     */
    public function result($result, $row, $field)
    {
        return mysqli_result($result, $row, $field);
    }

    /**
     * @param $result
     */
    public function freeResults($result)
    {
        return mysqli_free_result($result);
    }

    public function describeTable($table)
    {
        $return = ['fields' => [], 'indexes' => []];

        $result = $this->execute('SHOW INDEX IN ' . $table);
        if (mysqli_num_rows($result)) {
            while ($row = $this->fetchAssoc($result)) {
                if (!isset($return['indexes'][$row['Key_name']])) {
                    $return['indexes'][$row['Key_name']] = [];
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
                $return['fields'][$row['Field']] = [
                    'field' => $row['Field'],
                    'type' => $row['Type'],
                    'primary' => (
                        isset($return['indexes']['PRIMARY']['fields'][0])
                        && $return['indexes']['PRIMARY']['fields'][0] == $row['Field']
                    ),
                    'default' => $row['Default'],
                    'auto_increment' => ($row['Extra'] === 'auto_increment')
                ];
            }
        }

        return $return;
    }

    /**
     * @param $result
     * @return array|null
     */
    public function fetchAssoc($result)
    {
        return mysqli_fetch_assoc($result);
    }

    /**
     * @return array
     */
    public function getTables()
    {
        $return = [];

        $result = $this->execute("SHOW FULL TABLES");
        if ($this->numRows($result)) {
            while ($row = $this->fetchArray($result)) {
                $return[$row[0]] = [
                    "type" => $row[1] == "BASE TABLE" ? "table" : "view"
                ];
            }
        }

        return $return;
    }

    /**
     * @param $result
     * @return int
     */
    public function numRows($result)
    {
        return mysqli_num_rows($result);
    }

    /**
     * @param $result
     * @return array|null
     */
    public function fetchArray($result)
    {
        return mysqli_fetch_array($result);
    }

    /**
     * @param $value
     * @return int|string
     */
    public function quote($value)
    {
        $value = $this->cleanData($value);
        return is_numeric($value) ? $value : "'$value'";
    }

    /**
     * @param $data
     * @return string
     */
    public function cleanData($data)
    {
        return mysqli_real_escape_string($this->connection, $data);
    }

    /**
     * @return string
     */
    public function error()
    {
        return mysqli_error($this->connection);
    }

    public function disconnect()
    {
        mysqli_close($this->connection);
    }
}
