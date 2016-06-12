<?php /** MicroInterfaceRouter */

namespace Micro\Web;

/**
 * Interface IRouter
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
interface IRouter
{
    /**
     * Parsing uri
     *
     * @access public
     *
     * @param string $uri current check URI
     * @param string $method current Request method
     *
     * @return string
     */
    public function parse($uri, $method = 'GET');
}
