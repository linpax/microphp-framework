<?php /** MicroInterfaceAuth */

namespace Micro\Auth;

/**
 * Interface IAuth
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Auth
 * @version 1.0
 * @since 1.0
 */
interface IAuth
{
    /**
     * Check user access to permission
     *
     * @access public
     *
     * @param integer $userId user id
     * @param string $permission checked permission
     * @param array $data for compatible, not used!
     *
     * @return bool
     */
    public function check($userId, $permission, array $data = []);
}
