<?php /** MicroTransportInjector */

namespace Micro\Mail\Transport;

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
     * @return ITransport|bool
     */
    public function get()
    {
        return parent::get('transport');
    }
}