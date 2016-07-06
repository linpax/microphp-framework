<?php /** PgsqlDriverMicro */

namespace Micro\Db\Drivers;

use Micro\Base\Exception;

/**
 * PostgreSQL Driver class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Db\Drivers
 * @version 1.0
 * @since 1.0
 */
class PgsqlDriver extends Driver
{
    /** @var string $tableSchema Table schema for postgres */
    protected $tableSchema = 'public';


    /**
     * Driver constructor.
     *
     * @access public
     *
     * @param string $dsn DSN connection string
     * @param array $config Configuration of connection
     * @param array $options Other options
     *
     * @result void
     * @throws Exception
     */
    public function __construct($dsn, array $config = [], array $options = [])
    {
        parent::__construct($dsn, $config, $options);

        if (!empty($config['schema'])) {
            $this->tableSchema = $config['schema'];
        }
    }

    /**
     * Set current database
     *
     * @access public
     *
     * @param string $dbName Database name
     *
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public function switchDatabase($dbName)
    {
        // TODO: Implement switchDatabase() method.
    }

    /**
     * Info of database
     *
     * @access public
     *
     * @param string $dbName Database name
     *
     * @return array
     */
    public function infoDatabase($dbName)
    {
        // TODO: Implement infoDatabase() method.
    }

    /**
     * List tables in db
     *
     * @access public
     * @return array
     */
    public function listTables()
    {
        return $this->conn->query(
            'SELECT table_name FROM information_schema.tables WHERE table_schema = \'' . $this->tableSchema . '\';'
        )->fetchAll(\PDO::FETCH_COLUMN, 0);
    }

    /**
     * List database names on this connection
     *
     * @access public
     * @return mixed
     */
    public function listDatabases()
    {
        return $this->conn->query(
            'SELECT datname FROM pg_database WHERE datistemplate = false and datname != \'postgres\';'
        )->fetchAll(\PDO::FETCH_COLUMN, 0);
    }

    /**
     * Create a new table
     *
     * @param string $name Table name
     * @param array $elements Table elements
     * @param string $params Table params
     *
     * @return int
     */
    public function createTable($name, array $elements = [], $params = '')
    {
        // TODO: Implement createTable() method.
    }

    /**
     * Remove table from database
     *
     * @access public
     *
     * @param string $name Table name
     *
     * @return mixed
     */
    public function removeTable($name)
    {
        // TODO: Implement removeTable() method.
    }

    /**
     * Get array fields into table
     *
     * @access public
     *
     * @param string $table Table name
     *
     * @return array
     */
    public function listFields($table)
    {
        $sth = $this->conn->query('SELECT * FROM information_schema.columns WHERE table_name =\'' . $table . '\'');
        $result = [];

        foreach ($sth->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $result[] = [
                'field' => $row['column_name'],
                'type' => $row['data_type'] . (($max = $row['character_maximum_length']) ? '(' . $max . ')' : ''),
                'null' => $row['is_nullable'],
                'default' => $row['column_default']
            ];
        }

        return $result;
    }

    /**
     * Get info of a field
     *
     * @access public
     *
     * @param string $field Field name
     * @param string $table Table name
     *
     * @return array|boolean
     */
    public function fieldInfo($field, $table)
    {
        // TODO: Implement fieldInfo() method.
    }

    /**
     * Insert row into table
     *
     * @access public
     *
     * @param string $table Table name
     * @param array $line Line or lines to added
     * @param bool $multi Is multi rows
     *
     * @return bool
     */
    public function insert($table, array $line = [], $multi = false)
    {
        $fields = '"' . implode('", "', array_keys($multi ? $line[0] : $line)) . '"';
        $values = ':' . implode(', :', array_keys($multi ? $line[0] : $line));
        $rows = $multi ? $line : [$line];
        $id = null;

        if ($rows) {
            $this->conn->beginTransaction();

            $dbh = null;
            foreach ($rows AS $row) {
                $res = $this->conn->prepare('INSERT INTO ' . $table . ' (' . $fields . ') VALUES (' . $values . ');');
                $dbh = $res->execute($row);
            }

            $id = $dbh ? $this->conn->lastInsertId() : false;
            $this->conn->commit();
        }

        return $id ?: false;
    }

    /**
     * Update row in table
     *
     * @access public
     *
     * @param string $table Table name
     * @param array $elements Elements to update
     * @param string $conditions Conditions for search
     *
     * @return bool
     */
    public function update($table, array $elements = [], $conditions = '')
    {
        // TODO: Implement update() method.
    }

    /**
     * Delete row from table
     *
     * @access public
     *
     * @param string $table Table name
     * @param string $conditions Conditions to search
     * @param array $params Params array
     *
     * @return bool
     */
    public function delete($table, $conditions, array $params = [])
    {
        // TODO: Implement delete() method.
    }

    /**
     * Count element in sub-query
     *
     * @access public
     *
     * @param string $query Query
     * @param string $table Table name
     *
     * @return integer|boolean
     */
    public function count($query = '', $table = '')
    {
        // TODO: Implement count() method.
    }

    /**
     * Exists element in the table by params
     *
     * @access public
     *
     * @param string $table Table name
     * @param array $params Params array
     *
     * @return bool
     */
    public function exists($table, array $params = [])
    {
        // TODO: Implement exists() method.
    }
}