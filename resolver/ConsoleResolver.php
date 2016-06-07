<?php /** MicroConsoleResolver */

namespace Micro\Resolver;

use Micro\Base\Exception;
use Micro\Cli\Console;
use Micro\Web\RequestInjector;

/**
 * CLI Resolver class file.
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
class ConsoleResolver extends Resolver
{
    /**
     * Get instance application
     *
     * @access public
     *
     * @return Console
     */
    public function getApplication()
    {
        return new Console();
    }

    /**
     * Get action from request
     *
     * @access public
     *
     * @return string
     * @throws Exception
     */
    public function getAction()
    {
        return (new RequestInjector)->build()->getOption('a', 'action') ?: 'default';
    }
}
