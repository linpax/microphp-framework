<?php /** DriverMicro */

namespace Micro\Db\Drivers;

use Micro\Base\Exception;

/**
 * Driver class file.
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
abstract class Driver implements IDriver
{
    /** @var \PDO|null $conn Connection to DB */
    protected $conn;


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
        try {
            $this->conn = new \PDO($dsn, $config['username'], $config['password'], $options);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        } catch (\PDOException $e) {
            if (!array_key_exists('ignoreFail', $config) || !$config['ignoreFail']) {
                throw new Exception('Connect to DB failed: ' . $e->getMessage());
            }
        }
    }

    /**
     * Get driver type of current connection
     *
     * @access public
     * @return string
     */
    public function getDriverType()
    {
        return $this->conn->getAttribute(\PDO::ATTR_DRIVER_NAME);
    }

    /**
     * Destructor for this class
     *
     * @access public
     * @return void
     */
    public function __destruct()
    {
        $this->conn = null;
    }

    /**
     * Table exists in db
     *
     * @access public
     *
     * @param string $table Table name
     *
     * @return bool
     */
    public function tableExists($table)
    {
        return in_array($table, $this->listTables(), false);
    }

    /**
     * Clear all data from table
     *
     * @access public
     *
     * @param string $name Table name
     *
     * @return int
     */
    public function clearTable($name)
    {
        return $this->conn->exec("TRUNCATE {$name};");
    }

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
    public function fieldExists($field, $table)
    {
        foreach ($this->listFields($table) AS $tbl) {
            if ($tbl['field'] === $field) {
                return true;
            }
        }

        return false;
    }

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
    public function rawQuery($query = '', array $params = [], $fetchType = \PDO::FETCH_ASSOC, $fetchClass = 'Model')
    {
        $sth = $this->conn->prepare($query);

        if ($fetchType === \PDO::FETCH_CLASS) {
            /** @noinspection PhpMethodParametersCountMismatchInspection */
            $sth->setFetchMode($fetchType, $fetchClass, ['new' => false]);
        } else {
            $sth->setFetchMode($fetchType);
        }

        foreach ($params AS $name => $value) {
            $sth->bindValue($name, $value);
        }

        $sth->execute();

        return $sth->fetchAll();
    }
}