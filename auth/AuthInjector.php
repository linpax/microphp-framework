<?php /** MicroAuthInjector */

namespace Micro\Auth;

use Micro\Base\Injector;

/**
 * Class AuthInjector
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Auth
 * @version 1.0
 * @since 1.0
 */
class AuthInjector extends Injector
{
    /**
     * @access public
     * @return IAuth|bool
     */
    public function get()
    {
        return parent::get('auth');
    }
}