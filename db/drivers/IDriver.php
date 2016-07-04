<?php /** MicroInterfaceDriver */

namespace Micro\Db\Drivers;

use Micro\Base\Exception;

/**
 * Driver interface file.
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
interface IDriver
{
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
    public function __construct($dsn, array $config = [], array $options = []);

    /**
     * Get driver type of current connection
     *
     * @access public
     * @return string
     */
    public function getDriverType();

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
    public function switchDatabase($dbName);

    /**
     * Info of database
     *
     * @access public
     *
     * @param string $dbName Database name
     *
     * @return array
     */
    public function infoDatabase($dbName);

    /**
     * List database names on this connection
     *
     * @access public
     * @return mixed
     */
    public function listDatabases();

    /**
     * List tables in db
     *
     * @access public
     * @return array
     */
    public function listTables();

    /**
     * Table exists in db
     *
     * @access public
     *
     * @param string $table Table name
     *
     * @return bool
     */
    public function tableExists($table);

    /**
     * Create a new table
     *
     * @param string $name Table name
     * @param array $elements Table elements
     * @param string $params Table params
     *
     * @return int
     */
    public function createTable($name, array $elements = [], $params = '');

    /**
     * Remove table from database
     *
     * @access public
     *
     * @param string $name Table name
     *
     * @return mixed
     */
    public function removeTable($name);

    /**
     * Clear all data from table
     *
     * @access public
     *
     * @param string $name Table name
     *
     * @return int
     */
    public function clearTable($name);

    /**
     * Get array fields into table
     *
     * @access public
     *
     * @param string $table Table name
     *
     * @return array
     */
    public function listFields($table);

    /**
     * Field exists in table
     *
     * @access public
     *
     * @param string $field Field name
     * @param string $table Table name
     *
     * @return boolean
     */
    public function fieldExists($field, $table);

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
    public function fieldInfo($field, $table);

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
    public function insert($table, array $line = [], $multi = false);

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
    public function update($table, array $elements = [], $conditions = '');

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
    public function delete($table, $conditions, array $params = []);

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
    public function count($query = '', $table = '');

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
    public function exists($table, array $params = []);

    /**
     * Send RAW query to DB
     *
     * @access public
     *
     * @param string $query Raw query to db
     * @param array $params Params for query
     * @param int $fetchType Fetching type
     * @param string $fetchClass Fetching class
     *
     * @return \PDOStatement|array
     * @throws Exception
     */
    public function rawQuery($query = '', array $params = [], $fetchType = \PDO::FETCH_ASSOC, $fetchClass = 'Model');
}