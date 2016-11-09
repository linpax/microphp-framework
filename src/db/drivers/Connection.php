<?php /** ConnectionMicro */

namespace Micro\Db\Drivers;

use Micro\Base\Exception;
use Micro\Db\Adapter;

/**
 * Threads class file.
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
class Connection implements Adapter
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
     * @throws Exception
     */
    public function __construct($dsn, array $config = [], array $options = [])
    {
        $this->setDriver($dsn, $config, $options);
    }

    /**
     * Get DB driver
     *
     * @access public
     * @return IDriver
     */
    public function getDriver()
    {
        return $this->driver;
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
     * @throws Exception
     */
    public function setDriver($dsn, array $config = [], array $options = [])
    {
        $class = '\Micro\Db\Drivers\\'.ucfirst(substr($dsn, 0, strpos($dsn, ':'))).'Driver';

        if (!class_exists($class)) {
            throw new Exception('DB driver `'.$class.'` not supported');
        }

        unset($this->driver);

        $this->driver = new $class($dsn, $config, $options);
    }
}
