<?php /** MicroDbRBAC */

namespace Micro\Auth;

use Micro\Db\IConnection;
use Micro\Mvc\Models\Query;

/**
 * Database RBAC class file.
 *
 * RBAC security logic with DB
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Auth
 * @version 1.0
 * @since 1.0
 */
class DbRbac extends Rbac
{
    /**
     * Constructor file RBAC
     *
     * @public
     *
     * @param IConnection $connection
     *
     * @result void
     */
    public function __construct(IConnection $connection)
    {
        parent::__construct($connection);

        if (!$this->db->tableExists('rbac_role')) {
            $this->db->createTable('rbac_role', [
                '`name` varchar(127) NOT NULL',
                '`type` int(11) NOT NULL DEFAULT \'0\'',
                '`based` varchar(127)',
                '`data` text',
                'UNIQUE KEY `name` (`name`)'
            ], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');
        }
    }

    /**
     * Assign RBAC element into user
     *
     * @access public
     *
     * @param integer $userId user ID
     * @param string $name assign element name
     *
     * @return bool
     */
    public function assign($userId, $name)
    {
        if ($this->db->exists('rbac_role',
                ['name' => $name]) && $this->db->exists('user',
                ['id' => $userId])
        ) {
            return $this->db->insert('rbac_user', ['role' => $name, 'user' => $userId]);
        }

        return false;
    }

    /**
     * Check privileges to operation
     *
     * @access public
     *
     * @param integer $userId user id
     * @param string $action checked action
     * @param array $data action params
     *
     * @return boolean
     * @throws \Micro\Base\Exception
     */
    public function check($userId, $action, array $data = [])
    {
        return $this->db->exists('rbac_role', ['name' => $action]) ? parent::check($userId, $action, $data) : false;
    }

    /**
     * Add new element into RBAC rules
     *
     * @access public
     *
     * @param string $name element name
     * @param int $type element type
     * @param string $based based element name
     * @param string $data element params
     *
     * @return bool
     */
    public function create($name, $type = self::TYPE_ROLE, $based = null, $data = null)
    {
        if ($this->db->exists('rbac_role', ['name' => $name])) {
            return false;
        }

        if (null !== $based && !$this->db->exists('rbac_role', ['name' => $based])) {
            return false;
        }

        switch ($type) {
            case Rbac::TYPE_ROLE:
            case Rbac::TYPE_OPERATION:
            case Rbac::TYPE_PERMISSION:
                break;
            default:
                return false;
                break;
        }

        return $this->db->insert('rbac_role', ['name' => $name, 'type' => $type, 'based' => $based, 'data' => $data]);
    }

    /**
     * Delete element from RBAC rules
     *
     * @access public
     *
     * @param string $name element name
     *
     * @result void
     * @throws \Micro\Base\Exception
     */
    public function delete($name)
    {
        $tree = $this->searchRoleRecursive($this->tree($this->rawRoles()), $name);
        if ($tree) {
            $this->recursiveDelete($tree);
        }
    }

    /**
     * Get raw roles
     *
     * @access public
     *
     * @param int $pdo PHPDataObject fetch key
     *
     * @return mixed
     * @throws \Micro\Base\Exception
     */
    public function rawRoles($pdo = \PDO::FETCH_ASSOC)
    {
        $query = new Query($this->db);
        $query->table = 'rbac_role';
        $query->order = '`type` ASC';
        $query->single = false;

        return $query->run($pdo);
    }

    /**
     * Recursive delete roles from array
     *
     * @access public
     *
     * @param array $tree elements tree
     *
     * @return void
     */
    public function recursiveDelete(&$tree)
    {
        foreach ($tree AS $key => $element) {
            $this->db->delete('rbac_user', 'role=:name', ['name' => $element['name']]);
            $this->db->delete('rbac_role', 'name=:name', ['name' => $element['name']]);

            if (!empty($tree['childs'])) {
                $this->recursiveDelete($element['childs']);
            }
            unset($tree[$key]);
        }
    }
}
