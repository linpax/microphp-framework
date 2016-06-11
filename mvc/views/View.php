<?php /** MicroView */

namespace Micro\Mvc\Views;

use Micro\Base\Exception;
use Micro\Mvc\Module;
use Micro\Mvc\Widget;
use Micro\Web\Html\Html;

/**
 * Class View
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Mvc/Views
 * @version 1.0
 * @since 1.0
 */
abstract class View implements IView
{
    /** @var array $styleScripts */
    public $styleScripts = [];
    /** @var bool $asWidget */
    public $asWidget = false;
    /** @var array $params */
    public $params = [];
    /** @var array $stack */
    public $stack = [];
    /** @var Module $module */
    public $module;


    /**
     * @access public
     * @result void
     */
    public function __construct()
    {
    }

    /**
     * Add parameter into view
     *
     * @access public
     *
     * @param string $name parameter name
     * @param mixed $value parameter value
     *
     * @return void
     */
    public function addParameter($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * Widget
     *
     * @access public
     *
     * @param string $name widget name
     * @param array $options options array
     * @param bool $capture capture output
     *
     * @return string
     * @throws Exception
     */
    public function widget($name, array $options = [], $capture = false)
    {
        if (!class_exists($name)) {
            throw new Exception('Widget '.$name.' not found.');
        }

        /** @var \Micro\mvc\Widget $widget widget */
        $widget = new $name($options);
        $widget->init();

        if ($capture) {
            ob_start();
            $widget->run();
            $result = ob_get_clean();
        } else {
            $result = $widget->run();
        }

        if ($result instanceof PhpView) {
            $result->asWidget = true;
            $result->path = get_class($widget);

            $result = $result->render();
        }

        unset($widget);

        if ($capture) {
            return $result;
        }

        echo $result;

        return '';
    }

    /**
     * Begin widget
     *
     * @access public
     *
     * @param string $name widget name
     * @param array $options options array
     *
     * @return mixed
     * @throws Exception
     */
    public function beginWidget($name, array $options = [])
    {
        if (!class_exists($name)) {
            throw new Exception('Widget `'.$name.'` not found.');
        }

        if (!empty($GLOBALS['widgetStack'][$name])) {
            throw new Exception('This widget `'.$name.'` already started!');
        }

        $GLOBALS['widgetStack'][$name] = new $name($options);

        /** @noinspection PhpUndefinedMethodInspection */

        return $GLOBALS['widgetStack'][$name]->init();
    }

    /**
     * Ending widget
     *
     * @access public
     *
     * @param string $name widget name
     *
     * @throws Exception
     */
    public function endWidget($name = '')
    {
        if (!$name && $GLOBALS['widgetStack']) {
            /** @var Widget $widget */
            $widget = array_pop($GLOBALS['widgetStack']);
            $v = $widget->run();

            if ($v instanceof PhpView) {
                $v->asWidget = true;
                $v->path = get_class($widget);

                $v = $v->render();
            }

            unset($widget);
            echo $v;
        }

        if (empty($GLOBALS['widgetStack'][$name]) && !class_exists($name)) {
            throw new Exception('Widget `'.$name.'` not started.');
        }

        /** @var \Micro\mvc\Widget $widget widget */
        $widget = $GLOBALS['widgetStack'][$name];
        unset($GLOBALS['widgetStack'][$name]);

        $v = $widget->run();

        if ($v instanceof PhpView) {
            $v->asWidget = true;
            $v->path = get_class($widget);

            $v = $v->render();
        }

        unset($widget);
        echo $v;
    }

    /**
     * Register JS script
     *
     * @access public
     *
     * @param string $source file name
     * @param bool $isHead is head block
     *
     * @return void
     */
    public function registerScript($source, $isHead = true)
    {
        $this->styleScripts[] = [
            'isHead' => $isHead,
            'body' => Html::script($source)
        ];
    }

    /**
     * Register JS file
     *
     * @access public
     *
     * @param string $source file name
     * @param bool $isHead is head block
     *
     * @return void
     */
    public function registerScriptFile($source, $isHead = true)
    {
        $this->styleScripts[] = [
            'isHead' => $isHead,
            'body' => Html::scriptFile($source)
        ];
    }

    /**
     * Register CSS code
     *
     * @access public
     *
     * @param string $source file name
     * @param bool $isHead is head block
     *
     * @return void
     */
    public function registerCss($source, $isHead = true)
    {
        $this->styleScripts[] = [
            'isHead' => $isHead,
            'body' => Html::css($source)
        ];
    }

    /**
     * Register CSS file
     *
     * @access public
     *
     * @param string $source file name
     * @param bool $isHead is head block
     *
     * @return void
     */
    public function registerCssFile($source, $isHead = true)
    {
        $this->styleScripts[] = [
            'isHead' => $isHead,
            'body' => Html::cssFile($source)
        ];
    }

    /**
     * Insert styles and scripts into cache
     *
     * @access protected
     *
     * @param string $cache cache of generated page
     *
     * @return string
     */
    protected function insertStyleScripts($cache)
    {
        $heads = '';
        $ends = '';
        $result = '';

        foreach ($this->styleScripts AS $element) {
            if ($element['isHead']) {
                $heads .= $element['body'];
            } else {
                $ends .= $element['body'];
            }
        }

        $positionHead = strpos($cache, Html::closeTag('head'));
        $positionBody = strpos($cache, Html::closeTag('body'), $positionHead);

        $result .= substr($cache, 0, $positionHead);
        $result .= $heads;
        $result .= substr($cache, $positionHead, $positionBody);
        $result .= $ends;
        $result .= substr($cache, $positionHead + $positionBody);

        return $result;
    }
}
