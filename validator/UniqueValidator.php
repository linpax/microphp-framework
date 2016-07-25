<?php /** MicroUniqueValidator */

namespace Micro\Validator;

use Micro\Db\ConnectionInjector;
use Micro\Form\IFormModel;
use Micro\Mvc\Models\Query;

/**
 * UniqueValidator class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Validator
 * @version 1.0
 * @since 1.0
 */
class UniqueValidator extends BaseValidator
{
    /**
     * @inheritdoc
     */
    public function validate(IFormModel $model)
    {
        foreach ($this->elements AS $element) {
            if (!$model->checkAttributeExists($element)) {
                $this->errors[] = 'Parameter '.$element.' not defined in class '.get_class($model);

                return false;
            }
            $elementValue = $model->$element;

            $query = new Query((new ConnectionInjector)->getDriver());
            $query->select = $this->params['attribute'];
            $query->table = $this->params['table'];
            $query->addWhere($this->params['attribute'].'="'.$elementValue.'"');
            $query->limit = 1;
            $query->single = true;

            if ($query->run()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function client(IFormModel $model)
    {
        return '';
    }
}
