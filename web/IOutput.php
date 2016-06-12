<?php /** MicroInterfaceOutput */

namespace Micro\Web;

/**
 * Interface IOutput
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Web
 * @version 1.0
 * @since 1.0
 * @interface
 */
interface IOutput
{
    /**
     * Send data to browser|console
     *
     * @access public
     * @return void
     */
    public function send();
}
