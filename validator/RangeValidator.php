<?php /** MicroRangeValidator */

namespace Micro\Validator;

use Micro\Form\IFormModel;

/**
 * RangeValidator class file.
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
class RangeValidator extends BaseValidator
{
    /**
     * @inheritdoc
     */
    public function validate(IFormModel $model)
    {
        if (empty($this->params['min'])) {
            $this->errors[] = 'Minimal value not declared to Range validator';
        }
        if (empty($this->params['max'])) {
            $this->errors[] = 'Maximal value not declared to Range validator';
        }
        $step = (!empty($this->params['step'])) ? $this->params['step'] : 1;

        $rang = range($this->params['min'], $this->params['max'], $step);

        foreach ($this->elements AS $element) {
            if (!$model->checkAttributeExists($element)) {
                $this->errors[] = 'Parameter '.$element.' not defined in class '.get_class($model);

                return false;
            }
            if (!in_array($model->$element, $rang, true)) {
                $this->errors[] = 'Parameter '.$element.' not find in rage '.
                    $this->params['min'].'..'.$this->params['max'];

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
        return 'if (this.value < ' . $this->params['min'] . ' || this.value > ' . $this->params['max'] . ') {' .
            ' e.preventDefault(); this.focus(); alert(\'Value not find in range\'); }';
    }
}
