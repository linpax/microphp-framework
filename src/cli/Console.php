<?php /** MicroConsole */

namespace Micro\Cli;

use Micro\Base\Exception;
use Micro\Web\ResponseInjector;
use Psr\Http\Message\ResponseInterface;

/**
 * Console class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Cli
 * @version 1.0
 * @since 1.0
 * @abstract
 */
class Console
{
    /** @var string $command Parsed command */
    protected $command;


    /**
     * Constructor command
     *
     * @access public
     * @result void
     */
    public function __construct()
    {
    }

    /**
     * Run action of console command by name
     *
     * @access public
     *
     * @param string $name Command name
     *
     * @return ResponseInterface
     * @throws \RuntimeException|\InvalidArgumentException|Exception
     */
    public function action($name)
    {
        $command = '\\App\\Consoles\\'.ucfirst($name).'ConsoleCommand';
        $command = class_exists($command) ? $command : '\\Micro\\Cli\\Consoles\\'.ucfirst($name).'ConsoleCommand';

        if (!class_exists($command)) {
            throw new Exception('Command `'.$name.'` not found');
        }

        /** @var ConsoleCommand $command */
        $command = new $command();
        $command->execute();

        $response = (new ResponseInjector)->build();
        $response = $response->withHeader('status', (string)(int)$command->result);

        $stream = $response->getBody();
        $stream->write($command->message);

        return $response->withBody($stream);
    }
}
