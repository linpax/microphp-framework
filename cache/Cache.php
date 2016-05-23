<?php /** MicroCache */

namespace Micro\Cache;

use Micro\Base\Exception;

/**
 * Cache class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Cache
 * @version 1.0
 * @since 1.0
 */
class Cache
{
    /** @var array $drivers Supported drivers */
    protected static $drivers = [
        'apc' => '\\Micro\\Cache\\Driver\\ApcDriver',
        'array' => '\\Micro\\Cache\\Driver\\ArrayDriver',
        'db' => '\\Micro\\Cache\\Driver\\DbDriver',
        'file' => '\\Micro\\Cache\\Driver\\FileDriver',
        'memcache' => '\\Micro\\Cache\\Driver\\MemcachedDriver',
        'memcached' => '\\Micro\\Cache\\Driver\\MemcachedDriver',
        'redis' => '\\Micro\\Cache\\Driver\\RedisDriver',
        'wincache' => '\\Micro\\Cache\\Driver\\WincacheDriver',
        'xcache' => '\\Micro\\Cache\\Driver\\XcacheDriver'
    ];

    /** @var array $servers Activated servers */
    protected $servers = [];


    /**
     * Constructor is a initialize Caches
     *
     * @access public
     *
     * @param array $servers
     *
     * @result void
     * @throws Exception
     */
    public function __construct(array $servers)
    {
        foreach ($servers AS $key => $server) {
            if (array_key_exists($server['driver'], array_keys(static::$drivers))) {
                $this->servers[$key] = new static::$drivers[$server['driver']] ($server);
            } else {
                throw new Exception('Cache driver `'.$server['driver'].'` not found');
            }
        }
    }

    /**
     * Get cache server by name
     *
     * @access public
     *
     * @param string $driver server name
     *
     * @return mixed
     * @throws Exception
     */
    public function get($driver = null)
    {
        if (!$driver) {
            return $this->servers[0];
        }

        if (in_array($driver, $this->servers, true)) {
            return $this->servers[$driver];
        } else {
            throw new Exception('Cache `'.$driver.'` not found.');
        }
    }
}
