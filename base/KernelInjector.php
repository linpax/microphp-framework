<?php /** MicroKernelInjector */

namespace Micro\Base;

use Micro\Micro;

/**
 * Class KernelInjector
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
class KernelInjector extends Injector
{
    /**
     * @access public
     * @return Micro|bool
     */
    public function get()
    {
        return parent::get('kernel');
    }
}