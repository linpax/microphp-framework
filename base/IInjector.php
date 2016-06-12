<?php /** MicroInterfaceInjector */

namespace Micro\Base;


/**
 * Class Injector
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
interface IInjector
{
    /**
     * Add requirement to injector
     *
     * @access public
     * @param string $name
     * @param mixed $component
     * @return void
     */
    public function addRequirement($name, $component);

    /**
     * Build injector
     *
     * @access public
     * @return IDispatcher
     * @throws Exception
     */
    public function build();
}