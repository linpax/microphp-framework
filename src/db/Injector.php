<?php /** MicroConnectionInjector */

namespace Micro\Db;

use Micro\Base\Exception;
use Micro\Db\Drivers\IDriver;

/**
 * Class ConnectionInjector
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
class Injector extends \Micro\Base\Injector
{
    /**
     * Get DB driver
     *
     * @access public
     * @return IDriver
     * @throws Exception
     */
    public function getDriver()
    {
        return $this->build()->getDriver();
    }

    /**
     * @access public
     * @return Adapter
     * @throws Exception
     */
    public function build()
    {
        $connection = $this->get('connection');

        if (!($connection instanceof Adapter)) {
            throw new Exception('Component `connection` not configured');
        }

        return $connection;
    }
}