<?php /** MicroInterfaceFilter */

namespace Micro\Filter;

/**
 * Interface IFilter
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Filter
 * @version 1.0
 * @since 1.0
 *
 * @property array|string $result
 */
interface IFilter
{
    /**
     * PreFilter
     *
     * @access public
     *
     * @param array $params checked items and other params
     *
     * @return boolean
     */
    public function pre(array $params);

    /**
     * PostFilter
     *
     * @access public
     *
     * @param array $params checked items and other params
     *
     * @return mixed
     */
    public function post(array $params);
}
