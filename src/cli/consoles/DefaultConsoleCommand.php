<?php /** MicroDefaultConsoleCommand */

namespace Micro\Cli\Consoles;

use Micro\Cli\ConsoleCommand;

/**
 * Class DefaultConsoleCommand
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
class DefaultConsoleCommand extends ConsoleCommand
{
    /** @var mixed $data */
    public $data = 'Hello, world!';


    /**
     * @return void
     */
    public function execute()
    {
        $this->result = true;
        $this->message = $this->data;
    }
}
