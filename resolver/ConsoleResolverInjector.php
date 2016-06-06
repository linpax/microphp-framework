<?php /** MicroConsoleResolverInjector */

namespace Micro\Resolver;

use Micro\Base\Exception;
use Micro\Base\Injector;

/**
 * Class ConsoleResolverInjector
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Resolver
 * @version 1.0
 * @since 1.0
 */
class ConsoleResolverInjector extends Injector
{
    /**
     * @return bool
     * @throws Exception
     */
    public function get()
    {
        $consoleResolver = parent::get('consoleResolver');

        if (!($consoleResolver instanceof IResolver)) {
            throw new Exception('Component `resolver` not configured');
        }

        return $consoleResolver;
    }
}