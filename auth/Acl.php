<?php /** MicroACL */

namespace Micro\Auth;

use Micro\Base\Injector;

/**
 * Abstract ACL class file.
 *
 * Base logic for a ACL security
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Auth
 * @version 1.0
 * @since 1.0
 * @abstract
 */
abstract class Acl implements IAuth
{
    /** @var string $groupTable name of group table */
    protected $groupTable;


    /**
     * Base constructor for ACL, make acl_user table if exists
     *
     * @access public
     *
     * @param array $params config array
     *
     * @result void
     */
    public function __construct(array $params = [])
    {
        if (!empty($params['groupTable'])) {
            $this->groupTable = $params['groupTable'];
        }

        if (!(new Injector)->get('db')->tableExists('acl_user')) {
            (new Injector)->get('db')->createTable('acl_user', [
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
