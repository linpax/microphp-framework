<?php /** MicroAuthInjector */

namespace Micro\Auth;

use Micro\Base\Exception;
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
     * @return IAuth
     * @throws Exception
     */
    public function build()
    {
        $auth = parent::get('auth');

        if (!($auth instanceof IAuth)) {
            throw new Exception('Component `auth` not configured');
        }

        return $auth;
    }
}