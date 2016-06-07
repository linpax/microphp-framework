<?php /** MicroResponseInjector */

namespace Micro\Web;

use Micro\Base\Exception;
use Micro\Base\Injector;

/**
 * Class ResponseInjector
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Web
 * @version 1.0
 * @since 1.0
 */
class ResponseInjector extends Injector
{
    /**
     * @access public
     * @return IResponse
     * @throws Exception
     */
    public function build()
    {
        $response = $this->get('response');

        if (!($response instanceof IResponse)) {
            throw new Exception('Component `response` not configured');
        }

        return $response;
    }
}