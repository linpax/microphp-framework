<?php /** MicroACL */

namespace Micro\Auth\Drivers;

use Micro\Auth\Adapter;
use Micro\Db\Drivers\IDriver;
use Micro\Db\IConnection;

/**
 * Abstract ACL class file.
 *
 * Base logic for a ACL security
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Auth\Drivers
 * @version 1.0
 * @since 1.0
 * @abstract
 */
abstract class Acl implements Adapter
{
    /** @var IDriver $db */
    protected $db;
    /** @var string $groupTable name of group table */
    protected $groupTable;


    /**
     * Base constructor for ACL, make acl_user table if exists
     *
     * @access public
     *
     * @param IConnection $db
     * @param array $params config array
     *
     * @result void
     */
    public function __construct(IConnection $db, array $params = [])
    {
        $this->db = $db->getDriver();

        if (!empty($params['groupTable'])) {
            $this->groupTable = $params['groupTable'];
        }

        if (!$this->db->tableExists('acl_user')) {
            $this->db->createTable('acl_user', [
                '`id` int(10) unsigned NOT NULL AUTO_INCREMENT',
                '`user` int(11) unsigned NOT NULL',
                '`role` int(11) unsigned DEFAULT NULL',
                '`perm` int(11) unsigned DEFAULT NULL',
                'PRIMARY KEY (`id`)'
            ], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');
        }
    }

    /**
     * Get permissions in role
     *
     * @access protected
     *
     * @param string $role role name
     *
     * @return array
     * @abstract
     */
    abstract protected function rolePerms($role);
}
