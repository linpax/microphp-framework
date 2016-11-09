<?php /** MicroInterfaceCommand */

namespace Micro\Base;

/**
 * Interface Command
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
interface ICommand
{
    /**
     * Execute command
     *
     * @access public
     * @return void
     */
    public function execute();
}
