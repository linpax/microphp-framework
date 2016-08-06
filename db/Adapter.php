<?php /** MicroInterfaceConnection */

namespace Micro\Db;

use Micro\Db\Drivers\IDriver;

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
interface Adapter
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
     * Get DB driver
     *
     * @access public
     * @return IDriver
     */
    public function getDriver();
}
