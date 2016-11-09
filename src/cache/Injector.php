<?php /** MicroCacheInjector */

namespace Micro\Cache;

use Micro\Base\Exception;
use Micro\Cache\Drivers\Cache;

/**
 * Class CacheInjector
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Cache
 * @version 1.0
 * @since 1.0
 */
class Injector extends \Micro\Base\Injector
{
    /**
     * @return Cache
     * @throws Exception
     */
    public function build()
    {
        $cache = $this->get('cache');

        if (!($cache instanceof Cache)) {
            throw new Exception('Component `cache` not configured');
        }

        return $cache;
    }
}