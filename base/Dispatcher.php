<?php /** MicroDispatcher */

namespace Micro\Base;

use Micro\Web\IResponse;

/**
 * Dispatcher class file.
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
class Dispatcher implements IDispatcher
{
    /** @var array $listeners Listeners objects on events */
    protected $listeners = [];


    /**
     * Add listener on event
     *
     * @access public
     *
     * @param string $listener listener name
     * @param array $event ['Object', 'method'] or callable
     * @param int|null $prior priority
     *
     * @return bool
     */
    public function addListener($listener, $event, $prior = null)
    {
        if (!is_callable($event)) {
            return false;
        }

        if (!array_key_exists($listener, $this->listeners)) {
            $this->listeners[$listener] = [];
        }

        if (!$prior) {
            $this->listeners[$listener][] = $event;
        } else {
            array_splice($this->listeners[$listener], $prior, 0, $event);
        }

        return true;
    }

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
    public function signal($listener, array $params = [])
    {
        $result = null;

        if (array_key_exists($listener, $this->listeners) && 0 !== count($this->listeners[$listener])) {
            /** @noinspection ForeachSourceInspection */
            foreach ($this->listeners[$listener] as $listen) {
                $result = call_user_func($listen, $params);

                if ($result instanceof IResponse) {
                    return $result;
                }
            }
        }

        return $result;
    }
}
