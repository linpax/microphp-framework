<?php /** MicroInterfaceInjector */

namespace Micro\Base;


/**
 * Class Injector
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
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
     * Check injector or config
     *
     * @access public
     * @param string $name
     * @return bool
     */
    public function check($name);
}