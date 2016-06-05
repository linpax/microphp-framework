<?php /** MicroSessionInjector */

namespace Micro\Web;

use Micro\Base\Injector;

/**
 * Class SessionInjector
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
class SessionInjector extends Injector
{
    /**
     * @access public
     * @return ISession|bool
     */
    public function get()
    {
        return parent::get('session');
    }
}