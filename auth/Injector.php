<?php /** MicroAuthInjector */

namespace Micro\Auth;

use Micro\Base\Exception;

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
class Injector extends \Micro\Base\Injector
{
    /**
     * @access public
     * @return Adapter
     * @throws Exception
     */
    public function build()
    {
        $auth = $this->get('auth');

        if (!($auth instanceof Adapter)) {
            throw new Exception('Component `auth` not configured');
        }

        return $auth;
    }
}