<?php /** MicroConsoleCommand */

namespace Micro\Cli;

use Micro\Base\Command;
use Micro\Web\IOutput;

/**
 * Class ConsoleCommand
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
abstract class ConsoleCommand extends Command implements IOutput
{
    /**
     * Send data into console
     *
     * @access public
     * @return void
     */
    public function send()
    {
        echo $this->message;
    }
}
