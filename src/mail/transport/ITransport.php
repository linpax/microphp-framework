<?php /** MicroMailTransport */

namespace Micro\Mail\Transport;

use Micro\Mail\IMessage;

/**
 * Interface ITransport
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Mail\Transport
 * @version 1.0
 * @since 1.0
 */
interface ITransport
{
    /**
     * Send message
     *
     * @access public
     *
     * @param IMessage $message Message to send
     *
     * @return bool
     */
    public function send(IMessage $message);
}
