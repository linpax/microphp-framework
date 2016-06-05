<?php /** MicroDispatcherInjector */

namespace Micro\Base;

/**
 * Class DispatcherInjector
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
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
    public function get()
    {
        $dispatcher = parent::get('dispatcher');

        if (!($dispatcher instanceof IDispatcher)) {
            throw new Exception('Component `dispatcher` not configured');
        }

        return $dispatcher;
    }
}