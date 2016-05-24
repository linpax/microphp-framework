<?php /** MicroMigration */

namespace Micro\Mvc\Models;


/**
 * Migration class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Mvc\Models
 * @version 1.0
 * @since 1.0
 * @abstract
 */
abstract class Migration
{
    /**
     * Constructor for model
     *
     * @access public
     * @result void
     */
    public function __construct()
    {
    }

    /**
     * Upgrade DB
     *
     * @access public
     * @return void
     * @abstract
     */
    abstract public function up();

    /**
     * Downgrade DB
     *
     * @access public
     * @return void
     * @abstract
     */
    abstract public function down();
}
