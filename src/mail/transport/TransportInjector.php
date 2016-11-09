<?php /** MicroTransportInjector */

namespace Micro\Mail\Transport;

use Micro\Base\Exception;
use Micro\Base\InjectorInterface;

/**
 * Class TransportInjector
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
class TransportInjector extends InjectorInterface
{
    /**
     * @access public
     * @return ITransport
     * @throws Exception
     */
    public function build()
    {
        $transport = $this->get('transport');

        if (!($transport instanceof ITransport)) {
            throw new Exception('Component `transport` not configured');
        }

        return $transport;
    }
}