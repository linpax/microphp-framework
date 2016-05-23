<?php /** MicroAction */

namespace Micro\Mvc;

/**
 * Class Action
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Mvc
 * @version 1.0
 * @since 1.0
 * @abstract
 */
abstract class Action
{
    /**
     * @access public
     * @result void
     */
    public function __construct()
    {
    }

    /**
     * Running action
     *
     * @access public
     *
     * @return mixed
     */
    abstract public function run();
}
