<?php /** MicroSessionInjector */

namespace Micro\Web;

use Micro\Base\Exception;
use Micro\Base\Injector;

/**
 * Class SessionInjector
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Web
 * @version 1.0
 * @since 1.0
 */
class SessionInjector extends Injector
{
    /**
     * @access public
     * @return ISession
     * @throws Exception
     */
    public function build()
    {
        $session = $this->get('session');

        if (!($session instanceof ISession)) {
            throw new Exception('Component `session` not configured');
        }

        return $session;
    }
}