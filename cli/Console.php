<?php /** MicroConsole */

namespace Micro\Cli;

use Micro\Base\IContainer;

/**
 * Console class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/lugnsk/micro
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Cli
 * @version 1.0
 * @since 1.0
 * @abstract
 */
class Console
{
    /** @var IContainer $container */
    protected $container;
    /** @var string $command Parsed command */
    protected $command;

    /**
     * Constructor command
     *
     * @access public
     *
     * @param IContainer $container
     *
     * @result void
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * Run action of console command by name
     *
     * @access public
     *
     * @param string $name Command name
     *
     * @return bool|ConsoleCommand|string
     */
    public function action($name)
    {
        $command = '\\App\\Consoles\\'.ucfirst($name).'ConsoleCommand';
        $command = class_exists($command) ? $command : '\\Micro\\Cli\\Consoles\\'.ucfirst($name).'ConsoleCommand';

        if (!class_exists($command)) {
            return false;
        }

        /** @var ConsoleCommand $command */
        $command = new $command(['container' => $this->container]);
        $command->execute();

        return $command;
    }
}
