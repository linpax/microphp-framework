<?php /** ConnectionMicro */

namespace Micro\Db;

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
abstract class Connection implements IConnection
{
    private $driver;


    /**
     * Make connection to database with PDO driver
     *
     * @access public
     * @param string $dsn DSN connection string
     * @param array $config
     * @param array $options
     */
    public function __construct($dsn, $config = [], array $options = [])
    {
    }

    /**
     * @param $dsn
     * @param array $config
     * @param array $options
     */
    public function setDriver($dsn, $config = [], array $options = [])
    {
        unset($this->driver);

        $class = '\Micro\Db\Drivers\\' . ucfirst(substr($dsn, 0, strpos($dsn, ':'))) . 'Connection';

        $this->driver = new $class($dsn, $config, $options);
    }

    /**
     * is triggered when invoking inaccessible methods in an object context.
     *
     * @access public
     *
     * @param $name string
     * @param $arguments array
     *
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        if (!method_exists($this->driver, $name)) {
            throw new \BadMethodCallException('Method `` not found in connection driver ``');
        }

        return call_user_func_array([$this->driver, $name], $arguments);
    }
}
