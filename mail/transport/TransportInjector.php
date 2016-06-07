<?php /** MicroTransportInjector */

namespace Micro\Mail\Transport;

use Micro\Base\Exception;
use Micro\Base\Injector;

/**
 * Class TransportInjector
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Mail\Transport
 * @version 1.0
 * @since 1.0
 */
class TransportInjector extends Injector
{
    /**
     * @access public
     * @return ITransport
     * @throws Exception
     */
    public function build()
    {
        $transport = parent::get('transport');

        if (!($transport instanceof ITransport)) {
            throw new Exception('Component `transport` not configured');
        }

        return $transport;
    }
}