<?php /** MicroXcacheDriver */

namespace Micro\Cache\Driver;

use Micro\Base\Exception;
use Micro\Base\IContainer;

/**
 * Class XcacheDriver
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Cache\Driver
 * @version 1.0
 * @since 1.0
 */
class XcacheDriver extends BaseCacheDriver
{
    /**
     * Constructor
     *
     * @access public
     *
     * @param IContainer $container
     * @param array $config config array
     *
     * @result void
     * @throws Exception
     */
    public function __construct(IContainer $container, array $config = [])
    {
        parent::__construct($container, $config);

        if (!$this->check()) {
            throw new Exception('Extension XCache not installed');
        }
    }

    /**
     * @inheritdoc
     */
    public function check()
    {
        return extension_loaded('xcache') ? true : false;
    }

    /**
     * @inheritdoc
     */
    public function delete($name)
    {
        return xcache_unset($name);
    }

    /**
     * @inheritdoc
     */
    public function clean()
    {
        /** @noinspection PhpUndefinedConstantInspection */
        for ($i = 0, $cnt = xcache_count(XC_TYPE_VAR); $i < $cnt; $i++) {
            /** @noinspection PhpUndefinedConstantInspection */
            if (xcache_clear_cache(XC_TYPE_VAR, $i) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function info()
    {
        /** @noinspection PhpUndefinedConstantInspection */
        return xcache_count(XC_TYPE_VAR);
    }

    /**
     * @inheritdoc
     */
    public function getMeta($id)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function increment($name, $offset = 1)
    {
        $val = $this->get($name) + $offset;

        return $this->set($name, $val);
    }

    /**
     * @inheritdoc
     */
    public function get($name)
    {
        return xcache_isset($name) ? xcache_get($name) : false;
    }

    /**
     * @inheritdoc
     */
    public function set($name, $value, $duration = 0)
    {
        return xcache_set($name, $value, $duration);
    }

    /**
     * @inheritdoc
     */
    public function decrement($name, $offset = 1)
    {
        $val = $this->get($name) - $offset;

        return $this->set($name, $val);
    }
} 
