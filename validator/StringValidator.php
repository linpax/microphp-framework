<?php /** MicroStringValidator */

namespace Micro\Validator;

use Micro\Form\IFormModel;

/**
 * StringValidator class file.
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
class StringValidator extends BaseValidator
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
            $elementLength = strlen($model->$element);
            if (!empty($this->params['min'])) {
                $this->params['min'] = filter_var($this->params['min'], FILTER_VALIDATE_INT);
                if ($this->params['min'] > $elementLength) {
                    $this->errors[] = $element.' error: minimal characters not valid.';

                    return false;
                }
            }
            if (!empty($this->params['max'])) {
                $this->params['max'] = filter_var($this->params['max'], FILTER_VALIDATE_INT);
                if ($this->params['max'] < $elementLength) {
                    $this->errors[] = $element.' error: maximal characters not valid.';

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function client(IFormModel $model)
    {
        $javaScript = '';

        if (!empty($this->params['min'])) {
            $javaScript .= ' if (this.value.length < '.$this->params['min'].') { e.preventDefault(); this.focus();'.
                ' alert(\'Value lowest, minimum '.$this->params['min'].' symbols\'); }';
        }
        if (!empty($this->params['max'])) {
            $javaScript .= ' if (this.value.length > '.$this->params['max'].') { e.preventDefault(); this.focus();'.
                ' alert(\'Value highest, maximum '.$this->params['max'].' symbols\'); }';
        }

        return $javaScript;
    }
}
