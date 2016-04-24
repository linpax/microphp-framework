<?php /** MicroCache */

namespace Micro\Cache;

use Micro\Base\Exception;
use Micro\Base\IContainer;

/**
 * Cache class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/lugnsk/micro
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
        'array' => '\\Micro\\Cache\\ArrayCache',
        'apc' => '\\Micro\\Cache\\ApcCache',
        'file' => '\\Micro\\Cache\\FileCache',
        'memcache' => '\\Micro\\Cache\\MemcacheCache',
        'memcached' => '\\Micro\\Cache\\MemcacheCache',
        'redis' => '\\Micro\\Cache\\RedisCache',
        'wincache' => '\\Micro\\Cache\\WincacheCache',
        'xcache' => '\\Micro\\Cache\\XcacheCache'
    ];
    /** @var array $servers Activated servers */
    protected $servers = [];
    /** @var IContainer $container Config container */
    protected $container;


    /**
     * Constructor is a initialize Caches
     *
     * @access public
     *
     * @param IContainer $container
     * @param array $servers
     *
     * @result void
     * @throws Exception
     */
    public function __construct(IContainer $container, array $servers)
    {
        $this->container = $container;

        foreach ($servers AS $key => $server) {
            if (array_key_exists($server['driver'], array_keys(self::$drivers))) {
                $this->servers[$key] = new self::$drivers[$server['driver']] (
                    array_merge($server, ['container' => $this->container])
                );
            } else {
                throw new Exception('Cache driver `' . $server['driver'] . '` not found');
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
            throw new Exception('Cache `' . $driver . '` not found.');
        }
    }
}
