<?php /** MicroConsoleResolver */

namespace Micro\Resolver;

use Micro\Base\Injector;
use Micro\Cli\Console;

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
     */
    public function getAction()
    {
        return (new Injector)->get('request')->getOption('a', 'action') ?: 'default';
    }
}
