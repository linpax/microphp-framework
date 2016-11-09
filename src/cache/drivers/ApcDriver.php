<?php /** MicroApcDriver */

namespace Micro\Cache\Drivers;

use Micro\Base\Exception;

/**
 * Class ApcDriver
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Cache\Driver
 * @version 1.0
 * @since 1.0
 */
class ApcDriver extends CacheDriver
{
    /**
     * Constructor
     *
     * @access public
     *
     * @param array $config config array
     *
     * @result void
     * @throws Exception
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        if (!$this->check()) {
            throw new Exception('APC cache not installed');
        }
    }

    /**
     * @inheritdoc
     */
    public function check()
    {
        if (extension_loaded('apc') && ini_get('apc.enabled')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function get($name)
    {
        $values = apc_fetch($name);

        return is_array($values) ? $values : [];
    }

    /**
     * @inheritdoc
     */
    public function set($name, $value, $duration = 300, $new = false)
    {
        if ($new === true) {
            return apc_add($name, $value, $duration);
        } else {
            return apc_store($name, $value, $duration);
        }
    }

    /**
     * @inheritdoc
     */
    public function delete($name)
    {
        return apc_delete($name);
    }

    /**
     * @inheritdoc
     */
    public function clean()
    {
        if (extension_loaded('apc')) {
            return apc_clear_cache();
        } else {
            return apc_clear_cache('user');
        }
    }

    /**
     * @inheritdoc
     */
    public function info($type = null)
    {
        return apc_cache_info($type);
    }

    /**
     * @inheritdoc
     */
    public function getMeta($id)
    {
        $success = false;

        $stored = apc_fetch($id, $success);
        if ($success === false || count($stored) !== 3) {
            return false;
        }

        list($data, $time, $ttl) = $stored;

        return ['expire' => $time + $ttl, 'mtime' => $time, 'data' => unserialize($data)];
    }

    /**
     * @inheritdoc
     */
    public function increment($name, $offset = 1)
    {
        return apc_inc($name, $offset);
    }

    /**
     * @inheritdoc
     */
    public function decrement($name, $offset = 1)
    {
        return apc_dec($name, $offset);
    }
} 
