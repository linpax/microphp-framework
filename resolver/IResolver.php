<?php /** MicroInterfaceResolver */

namespace Micro\Resolver;

/**
 * Interface IResolver
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Resolver
 * @version 1.0
 * @since 1.0
 */
interface IResolver
{
    /**
     * Get instance application
     *
     * @access public
     *
     * @return mixed
     * @abstract
     */
    public function getApplication();

    /**
     * Get action from request
     *
     * @access public
     *
     * @return string
     */
    public function getAction();
}
