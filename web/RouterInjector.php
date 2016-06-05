<?php /** MicroRouterInjector */

namespace Micro\Web;

use Micro\Base\Injector;

/**
 * Class RouterInjector
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Web
 * @version 1.0
 * @since 1.0
 */
class RouterInjector extends Injector
{
    /**
     * @access public
     * @return IRouter|bool
     */
    public function get()
    {
        return parent::get('router');
    }
}