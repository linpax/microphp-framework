<?php /** MicroKernelInjector */

namespace Micro\Base;

/**
 * Class KernelInjector
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
class KernelInjector extends Injector
{
    /**
     * @access public
     * @return KernelInterface
     * @throws Exception
     */
    public function build()
    {
        $kernel = $this->get('kernel');

        if (!($kernel instanceof KernelInterface)) {
            throw new Exception('Component `kernel` not configured');
        }

        return $kernel;
    }
}