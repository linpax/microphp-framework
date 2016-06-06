<?php /** MicroCompareValidator */

namespace Micro\Validator;

use Micro\Base\Exception;
use Micro\Form\IFormModel;

/**
 * CompareValidator class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Validator
 * @version 1.0
 * @since 1.0
 */
class CompareValidator extends BaseValidator
{
    /**
     * @inheritdoc
     * @throws Exception
     */
    public function validate(IFormModel $model)
    {
        if (empty($this->params['attribute']) && empty($this->params['value'])) {
            return false;
        }

        if (!$model->checkAttributeExists($this->params['attribute'])) {
            throw new Exception('Attribute `'.$this->params['attribute'].'` not found into '.get_class($model));
        }

        foreach ($this->elements AS $element) {
            if (!$model->checkAttributeExists($element)) {
                $this->errors[] = 'Parameter '.$element.' not defined in class '.get_class($model);

                return false;
            }

            $elementValue = $model->$element;
            if (!empty($this->params['value']) && ($this->params['value'] !== $elementValue)) {
                $this->errors[] = 'Parameter '.$element.' not equal '.$this->params['value'];

                return false;
            } elseif (array_key_exists('attribute',
                    $this->params) && ($model->{$this->params['attribute']} !== $elementValue)
            ) {
                $this->errors[] = 'Parameter '.$element.' not equal '.$model->{$this->params['attribute']};

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
        $value = $this->params['value'];
        if (!$value) {
            $attribute = $this->params['attribute'];
            $value = $model->$attribute;
        }

        $javaScript = 'if (this.value!="' . $value . '") { e.preventDefault(); this.focus(); alert(\'Value is not compatible\'); }';

        return $javaScript;
    }
}
