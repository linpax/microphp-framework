<?php /** MicroBaseCacheDriver */

namespace Micro\Cache\Driver;


/**
 * Abstract class Base Cache
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Cache\Driver
 * @version 1.0
 * @since 1.0
 * @abstract
 */
abstract class CacheDriver implements ICacheDriver
{
    /**
     * Constructor for caches
     *
     * @access public
     *
     * @param array $params Configuration params
     *
     * @result void
     */
    public function __construct(array $params = [])
    {
    }
}
