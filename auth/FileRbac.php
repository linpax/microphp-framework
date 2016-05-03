<?php /** MicroFileRBAC */

namespace Micro\Auth;

use Micro\Base\IContainer;

/**
 * File RBAC class file.
 *
 * RBAC security with files.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/lugnsk/micro
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Auth
 * @version 1.0
 * @since 1.0
 */
class FileRbac extends Rbac
{
    /** @var array $roles RBAC role tree */
    private $roles = [];

    /**
     * Redefine constructor for RBAC
     *
     * @access public
     *
     * @param array $params configuration array
     *
     * @result void
     */
    public function __construct(IContainer $container)
    {
        parent::__construct($container);

        if (!empty($params['roles'])) {
            $this->roles = $this->tree($params['roles']);
        }
    }

    /**
     * Assign RBAC element into user
     *
     * @access public
     *
     * @param integer $userId user id
     * @param string $name element name
     *
     * @return bool
     */
    public function assign($userId, $name)
    {
        if ($this->searchRoleRecursive($this->roles, $name)) {
            $exists = $this->container->db->exists('rbac_user', ['user' => $userId, 'role' => $name]);
            if (!$exists) {
                return $this->container->db->insert('rbac_user', ['role' => $name, 'user' => $userId]);
            }
        }

        return false;
    }

    /**
     * Get raw roles
     *
     * @access public
     * @return mixed
     */
    public function rawRoles()
    {
        return $this->roles;
    }
}
