<?php /** MicroQueue */

namespace Micro\Queue;

use Micro\Base\Exception;

/**
 * Queue class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Queue
 * @version 1.0
 * @since 1.0
 */
class Queue
{
    /** @var array $servers Configuration servers */
    protected $servers = [];
    /** @var array $routes Configuration routes */
    protected $routes = [];
    /** @var array $brokers Started servers */
    protected $brokers = [];


    /**
     * Initialize Queue manager
     *
     * @access public
     *
     * @param array $servers
     * @param array $routes
     *
     * @result void
     */
    public function __construct(array $servers = [], array $routes = [])
    {
        $this->servers = $servers;
        $this->routes = $routes;
    }

    /**
     * Send message into service on selected server
     *
     * @access public
     *
     * @param string $route
     * @param array $data
     * @param string $type
     * @param int $retry
     *
     * @return mixed
     * @throws Exception
     */
    public function send($route, array $data = [], $type = 'sync', $retry = 5)
    {
        $broker = $this->getBroker($route, $type, $retry);

        switch ($type) {
            case 'sync':
            case 'async':
            case 'stream':
                break;

            default:
                throw new Exception('Service type `'.$type.'` wrong name.');
        }

        return $broker->{$type}($route, $data);
    }

    /**
     * @param string $uri
     * @param string $type
     * @param string $retry
     *
     * @return \Micro\queue\IQueue
     * @throws Exception
     */
    private function getBroker($uri, $type, $retry)
    {
        $servers = $this->getServersFromRoute($this->getRoute($uri), $type);
        $server = null;

        for ($counter = 0; $counter < $retry; $counter++) {
            $random = mt_rand(0, count($servers) - 1);

            if (!array_key_exists($servers[$random], $this->brokers)) {
                $cls = $this->servers[$servers[$random]];
                $this->brokers[$servers[$random]] = new $cls['class']($cls);
            }
            /** @noinspection PhpUndefinedMethodInspection */
            if ($this->brokers[$servers[$random]]->test()) {
                $server = $servers[$random];
            }
        }
        if (!$server) {
            throw new Exception('Message not send, random servers is down into `'.$uri.'`');
        }

        return $this->brokers[$server];
    }

    /**
     * Get servers list from routing rule
     *
     * @access protected
     *
     * @param array $route Routing rule
     * @param string $type Sending type
     *
     * @return array
     * @throws Exception
     */
    protected function getServersFromRoute(array $route, $type = '*')
    {
        $servers = [];

        foreach ($route AS $key => $val) {
            if (is_string($val)) {
                $route['*'] = [$val];
                unset($route[$key]);
            }
        }
        if (array_key_exists($type, $route)) {
            $servers += $route[$type];
        }
        if (array_key_exists('*', $route)) {
            $servers += $route['*'];
        }
        if (!$servers) {
            throw new Exception('Type `'.$type.'` not found into route');
        }

        return $servers;
    }

    /**
     * Get rules from route by pattern
     *
     * @access protected
     *
     * @param string $uri URI for match
     *
     * @return array Rules for URI
     * @throws Exception
     */
    protected function getRoute($uri)
    {
        $keys = array_keys($this->routes);

        foreach (range(0, count($keys) - 1) AS $i) {
            if (preg_match('/'.$keys[$i].'/', $uri)) {
                if (!is_array($this->routes[$keys[$i]])) {
                    $this->routes[$keys[$i]] = ['*' => $this->routes[$keys[$i]]];
                }

                return $this->routes[$keys[$i]];
            }
        }
        throw new Exception('Route `'.$uri.'` not found');
    }
}
