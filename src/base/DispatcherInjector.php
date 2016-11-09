<?php /** MicroDispatcherInjector */

namespace Micro\Base;

/**
 * Class DispatcherInjector
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Base
 * @version 1.0
 * @since 1.0
 */
class DispatcherInjector extends Injector
{
    /**
     * @return IDispatcher
     * @throws Exception
     */
    public function build()
    {
        $dispatcher = $this->get('dispatcher');

        if (!($dispatcher instanceof IDispatcher)) {
            throw new Exception('Component `dispatcher` not configured');
        }

        return $dispatcher;
    }
}