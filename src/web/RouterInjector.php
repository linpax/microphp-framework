<?php /** MicroRouterInjector */

namespace Micro\Web;

use Micro\Base\Exception;
use Micro\Base\Injector;

/**
 * Class RouterInjector
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
class RouterInjector extends Injector
{
    /**
     * @access public
     * @return IRouter
     * @throws Exception
     */
    public function build()
    {
        $router = $this->get('router');

        if (!($router instanceof IRouter)) {
            throw new Exception('Component `router` not configured');
        }

        return $router;
    }
}