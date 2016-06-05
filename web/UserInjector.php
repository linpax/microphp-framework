<?php /** MicroUserInjector */

namespace Micro\Web;

use Micro\Base\Injector;

/**
 * Class UserInjector
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
class UserInjector extends Injector
{
    /**
     * @access public
     * @return IUser|bool
     */
    public function get()
    {
        return parent::get('user');
    }
}