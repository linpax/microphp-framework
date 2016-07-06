<?php /** MicroInterfaceConnection */

namespace Micro\Db;

/**
 * Interface for a connections to data bases
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
interface IConnection
{
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
    public function setDriver($dsn, array $config = [], array $options = []);

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
    public function __call($name, array $arguments = []);
}
