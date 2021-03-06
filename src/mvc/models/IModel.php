<?php /** MicroInterfaceModel */

namespace Micro\Mvc\Models;

use Micro\Form\IFormModel;

/**
 * IModel interface file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Mvc\Models
 * @version 1.0
 * @since 1.0
 * @interface
 *
 * @property string $tableName
 */
interface IModel extends IFormModel
{
    /**
     * Relations for model
     *
     * @access public
     * @return IRelations
     */
    public function relations();

    /**
     * Before create actions
     *
     * @access public
     * @return boolean
     */
    public function beforeCreate();

    /**
     * After create actions
     *
     * @access public
     * @return void
     */
    public function afterCreate();

    /**
     * Before save actions
     *
     * @access public
     * @return boolean
     */
    public function beforeSave();

    /**
     * After save actions
     *
     * @access public
     * @return void
     */
    public function afterSave();

    /**
     * Before update actions
     *
     * @access public
     * @return boolean
     */
    public function beforeUpdate();

    /**
     * After update actions
     *
     * @access public
     * @return boolean
     */
    public function afterUpdate();

    /**
     * Before delete actions
     *
     * @access public
     * @return boolean
     */
    public function beforeDelete();

    /**
     * After delete actions
     *
     * @access public
     * @return void
     */
    public function afterDelete();

    /**
     * Is a new record?
     *
     * @access public
     * @return boolean
     */
    public function isNewRecord();
}
