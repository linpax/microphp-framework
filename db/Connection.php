<?php /** ConnectionMicro */

namespace Micro\Db;

use Micro\Db\Drivers\IDriver;

/**
 * Threads class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Db
 * @version 1.0
 * @since 1.0
 */
class Connection implements IConnection
{
    /** @var IDriver $driver */
    private $driver;


    /**
     * Make connection to database with PDO driver
     *
     * @access public
     *
     * @param string $dsn DSN connection string
     * @param array $config Configuration of connection
     * @param array $options Other options
     *
     * @result void
     */
    public function __construct($dsn, array $config = [], array $options = [])
    {
        $this->setDriver($dsn, $config, $options);
    }

    /**
     * Set active connection driver
     *
     * @access public
     *
     * @param string $dsn DSN connection string
     * @param array $config Configuration of connection
     * @param array $options Other options
     *
     * @return void
     */
    public function setDriver($dsn, array $config = [], array $options = [])
    {
        unset($this->driver);

        $class = '\Micro\Db\Drivers\\' . ucfirst(substr($dsn, 0, strpos($dsn, ':'))) . 'Connection';

        $this->driver = new $class($dsn, $config, $options);
    }

    /**
     * Send sql commands to driver
     *
     * @access public
     *
     * @param string $name Driver method name
     * @param array $arguments Method arguments
     *
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($name, array $arguments = [])
    {
        if (!method_exists($this->driver, $name)) {
            throw new \BadMethodCallException('Method `' . $name . '` not found in connection driver `' . get_class($this->driver) . '`');
        }

        return call_user_func_array([$this->driver, $name], $arguments);
    }
}
