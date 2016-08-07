<?php /** MicroDbACL */

namespace Micro\Auth\Drivers;

use Micro\Db\Adapter;
use Micro\Mvc\Models\Query;

/**
 * Database ACL class file.
 *
 * ACL security logic with DB
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Auth\Drivers
 * @version 1.0
 * @since 1.0
 */
class DbAcl extends Acl
{
    /**
     * Constructor DB acl
     *
     * @access public
     *
     * @param Adapter $db
     * @param array $params config array
     *
     * @result void
     */
    public function __construct(Adapter $db, array $params = [])
    {
        parent::__construct($db, $params);

        /** @var array $tables */
        $tables = $this->db->listTables();

        if (empty($tables['acl_role'])) {
            $this->db->createTable('acl_role', [
                '`id` int(10) unsigned NOT NULL AUTO_INCREMENT',
                '`name` varchar(255) NOT NULL',
                'PRIMARY KEY (`id`)'
            ], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');
        }
        if (empty($tables['acl_perm'])) {
            $this->db->createTable('acl_perm', [
                '`id` int(10) unsigned NOT NULL AUTO_INCREMENT',
                '`name` varchar(255) NOT NULL',
                'PRIMARY KEY (`id`)'
            ], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');
        }
        if (empty($tables['acl_role_perm'])) {
            $this->db->createTable('acl_role_perm', [
                '`id` int(10) unsigned NOT NULL AUTO_INCREMENT',
                '`role` int(11) unsigned DEFAULT NOT NULL',
                '`perm` int(11) unsigned DEFAULT NOT NULL',
                'PRIMARY KEY (`id`)'
            ], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');
        }
    }

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
     * @throws \Micro\Base\Exception
     */
    public function check($userId, $permission, array $data = [])
    {
        $query = new Query($this->db);
        $query->select = '*';
        $query->table = '`acl_user` AS `au`';

        $query->addJoin('`acl_perm` AS  `ap`', '`ap`.`id` =  `au`.`perm`');
        $query->addJoin('`acl_role_perm` AS  `arp`', '`arp`.`role` =  `au`.`role`');
        $query->addJoin('`acl_perm` AS  `ap1`', '`ap1`.`id` =  `arp`.`perm`');

        $query->addWhere('`au`.`user`='.$userId);
        $query->addWhere('`ap`.`name`=:perm OR `ap1`.`name`=:perm');

        $query->limit = 1;

        $query->params = [':perm' => $permission];
        $query->single = true;

        return (bool)$query->run();
    }

    /**
     * Create new role
     *
     * @access public
     *
     * @param string $name role name
     *
     * @return void
     */
    public function createRole($name)
    {
        if (!$this->db->exists('acl_role', ['name' => $name])) {
            $this->db->insert('acl_role', ['name' => $name]);
        }
    }

    /**
     * Create new permission
     *
     * @access public
     *
     * @param string $name permission name
     *
     * @return void
     */
    public function createPermission($name)
    {
        if (!$this->db->exists('acl_role', ['name' => $name])) {
            $this->db->insert('acl_role', ['name' => $name]);
        }
    }

    /**
     * Delete permission by name
     *
     * @access public
     *
     * @param string $name permission name
     *
     * @return void
     */
    public function deletePermission($name)
    {
        $this->db->delete('acl_perm', ['name' => $name]);
    }

    /**
     * Delete role by name
     *
     * @access public
     *
     * @param string $name role name
     *
     * @return void
     * @throws \Micro\Base\Exception
     */
    public function deleteRole($name)
    {
        foreach ($this->rolePerms($name) AS $perm) {
            $this->db->delete('acl_role_perm', ['id' => $perm['perm']]);
        }

        $this->db->delete('acl_role', ['name' => $name]);
    }

    /**
     * Get role perms
     *
     * @access public
     *
     * @param string $role role name
     *
     * @return array
     * @throws \Micro\Base\Exception
     */
    protected function rolePerms($role)
    {
        $query = new Query($this->db);
        $query->select = '*';
        $query->table = 'acl_role_perm';
        $query->addWhere('role='.$role);
        $query->single = false;

        return $query->run();
    }

    /**
     * Assign role permission
     *
     * @access public
     *
     * @param string $role role name
     * @param string $permission permission name
     *
     * @return void
     */
    public function assignRolePermission($role, $permission)
    {
        $this->db->insert('acl_role_perm', ['role' => $role, 'perm' => $permission]);
    }

    /**
     * Revoke role permission
     *
     * @access public
     *
     * @param string $role role name
     * @param string $permission permission name
     *
     * @return void
     */
    public function revokeRolePermission($role, $permission)
    {
        $this->db->delete('acl_role_perm', ['role' => $role, 'perm' => $permission]);
    }

    /**
     * Grant privilege to user
     *
     * @access public
     *
     * @param integer $userId user ID
     * @param integer $privilege privilege ID
     * @param boolean $asRole as role?
     *
     * @return void
     */
    public function grantPrivilege($userId, $privilege = null, $asRole = true)
    {
        if ($asRole) {
            $this->db->insert('acl_user', ['user' => $userId, 'role' => $privilege]);
        } else {
            $this->db->insert('acl_user', ['user' => $userId, 'perm' => $privilege]);
        }
    }

    /**
     * Forbid privilege
     *
     * @access public
     *
     * @param integer $userId user ID
     * @param integer $privilege privilege ID
     * @param bool $asRole as role?
     */
    public function forbidPrivilege($userId, $privilege = null, $asRole = true)
    {
        if ($asRole) {
            $this->db->delete('acl_user', '`user`="'.$userId.'" AND `role`="'.$privilege.'"');
        } else {
            $this->db->delete('acl_user', '`user`="'.$userId.'" AND `perm`="'.$privilege.'"');
        }
    }
}
