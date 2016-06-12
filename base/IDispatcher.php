<?php /** MicroInterfaceDispatcher */

namespace Micro\Base;

/**
 * Interface IDispatcher
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
interface IDispatcher
{
    /**
     * Add listener on event
     *
     * @access public
     *
     * @param string $listener listener name
     * @param mixed $event ['Object', 'method'] or callable
     * @param int|null $prior priority
     *
     * @return bool
     */
    public function addListener($listener, $event, $prior = null);

    /**
     * Send signal to run event
     *
     * @access public
     *
     * @param string $listener listener name
     * @param array $params Signal parameters
     *
     * @return mixed
     */
    public function signal($listener, array $params = []);
}
