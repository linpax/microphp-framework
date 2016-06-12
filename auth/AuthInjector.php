<?php /** MicroAuthInjector */

namespace Micro\Auth;

use Micro\Base\Exception;
use Micro\Base\Injector;

/**
 * Class AuthInjector
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
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
        $auth = $this->get('auth');

        if (!($auth instanceof IAuth)) {
            throw new Exception('Component `auth` not configured');
        }

        return $auth;
    }
}