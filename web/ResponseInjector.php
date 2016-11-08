<?php /** MicroResponseInjector */

namespace Micro\Web;

use Micro\Base\Exception;
use Micro\Base\Injector;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseInjector
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Web
 * @version 1.0
 * @since 1.0
 */
class ResponseInjector extends Injector
{
    /**
     * @access public
     * @return ResponseInterface
     * @throws Exception
     */
    public function build()
    {
        $response = $this->get('response');

        if (!($response instanceof ResponseInterface)) {
            throw new Exception('Component `response` not configured');
        }

        return $response;
    }
}