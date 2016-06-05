<?php /** MicroConnectionInjector */

namespace Micro\Db;

use Micro\Base\Injector;

/**
 * Class ConnectionInjector
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Db
 * @version 1.0
 * @since 1.0
 */
class ConnectionInjector extends Injector
{
    /**
     * @access public
     * @return IConnection|bool
     */
    public function get()
    {
        return parent::get('connection');
    }
}