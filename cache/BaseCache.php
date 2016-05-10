<?php /** MicroBaseCache */

namespace Micro\Cache;

use Micro\Base\IContainer;

/**
 * Abstract class Base Cache
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Cache
 * @version 1.0
 * @since 1.0
 * @abstract
 */
abstract class BaseCache implements ICache
{
    /** @var IContainer $container */
    protected $container;


    /**
     * Constructor for caches
     *
     * @access public
     *
     * @param IContainer $container
     * @param array $params Configuration params
     *
     * @result void
     */
    public function __construct(IContainer $container, array $params = [])
    {
        $this->container = $container;
    }
}
