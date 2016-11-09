<?php /** MicroFormWidget */

namespace Micro\Widget;

use Micro\Form\Form;
use Micro\Mvc\Widget;
use Micro\Web\Html\Html;

/**
 * FormWidget class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Widget
 * @version 1.0
 * @since 1.0
 */
class FormWidget extends Widget
{
    /** @var string $action action url */
    public $action = '';
    /** @var string $method send form method */
    public $method = 'GET';
    /** @var string $type type of form */
    public $type = 'text/plain';
    /** @var string $client client js code */
    public $client = '';
    /** @var array $attributes attributes for form element */
    public $attributes = [];


    /**
     * Initialize widget
     *
     * @access public
     *
     * @return Form
     */
    public function init()
    {
        $this->attributes['type'] = $this->type;
        echo Html::beginForm($this->action, $this->method, $this->attributes);

        return new Form;
    }

    /**
     * Running widget
     *
     * @access public
     *
     * @return void
     */
    public function run()
    {
        $result = Html::endForm();
        if ($this->client) {
            $result .= Html::script($this->client);
        }
        echo $result;
    }
}
