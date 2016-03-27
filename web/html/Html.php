<?php /** MicroHtml */

namespace Micro\Web\Html;

/**
 * Html class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/lugnsk/micro
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Web\Html
 * @version 1.0
 * @since 1.0
 */
class Html
{
    use TagTrait;
    use HeadTagTrait;
    use TableTagTrait;
    use Html5TagTrait;
    use FieldTagTrait;


    /**
     * Render begin form tag
     *
     * @access public
     *
     * @param  string $action path to URL action
     * @param  string $method method of request
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function beginForm($action, $method = 'POST', array $attributes = [])
    {
        return static::openTag('form', array_merge($attributes, [
            'action' => $action,
            'method' => $method
        ]));
    }

    /**
     * Render end form tag
     *
     * @access public
     * @return string
     * @static
     */
    public static function endForm()
    {
        return static::closeTag('form');
    }


    /**
     * Render image button tag
     *
     * @access public
     *
     * @param  string $name image name
     * @param  string $file image file path
     * @param  array $attributesButton attributes for button
     * @param  array $attributesImage attributes for image
     *
     * @return string
     * @static
     */
    public static function imageButton($name, $file, array $attributesButton = [], array $attributesImage = [])
    {
        return static::button(static::image($name, $file, $attributesImage), $attributesButton);
    }

    /**
     * Render button tag
     *
     * @access public
     *
     * @param  string $text text for button
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function button($text, array $attributes = [])
    {
        return static::openTag('button', $attributes) . $text . static::closeTag('button');
    }

    /**
     * Render textArea tag
     *
     * @access public
     *
     * @param  string $name textArea name
     * @param  string $text textArea text
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function textArea($name, $text, array $attributes = [])
    {
        return static::openTag('textarea', array_merge($attributes, [
            'id' => $name,
            'name' => $name
        ])) . $text . static::closeTag('textarea');
    }

    /**
     * Render legend tag
     *
     * @access public
     *
     * @param  string $text legend text
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function legend($text, array $attributes = [])
    {
        return static::openTag('legend', $attributes) . $text . static::closeTag('legend');
    }

    /**
     * Render label tag
     *
     * @access public
     *
     * @param string $name label name
     * @param string $elemId element ID
     * @param array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function label($name, $elemId = '', array $attributes = [])
    {
        return static::openTag('label', array_merge($attributes, [
            'for' => $elemId
        ])) . $name . static::closeTag('label');
    }

    /**
     * Render dropDownList (select tag)
     *
     * @access public
     *
     * @param string $name dropDown name
     * @param array $options format array(value, text, attributes) OR array(label, options, attributes)
     * @param array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function dropDownList($name, array $options = [], array $attributes = [])
    {
        return static::listBox($name, $options, array_merge($attributes, [
            'id' => $name,
            'size' => 1
        ]));
    }

    /**
     * Render listBox (select tag)
     *
     * @access public
     *
     * @param string $name listBox name
     * @param array $options format array(value, text, attributes) OR array(label, options, attributes)
     * @param array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function listBox($name, array $options = [], array $attributes = [])
    {
        $selected = empty($attributes['selected']) ? null : $attributes['selected'];
        unset($attributes['selected']);

        $attributes['name'] = $name;
        $opts = '';
        foreach ($options AS $option) {
            if (!empty($option['label'])) {
                $opts .= static::optGroup($option['label'], $option['options'], $option['attributes']);
            } else {
                $attr = empty($option['attributes']) ? [] : $option['attributes'];
                unset($option['attributes']);

                if (!empty($option['value']) && (string)$option['value'] === (string)$selected) {
                    $attr['selected'] = 'selected';
                }

                $text = empty($option['text']) ? '' : $option['text'];
                unset($option['text']);

                $opts .= static::option(!empty($option['value']) ? $option['value'] : '', $text, $attr);
            }
        }

        $attributes['name'] .= array_key_exists('multiple', $attributes) ? '[]' : '';

        return static::openTag('select', $attributes) . $opts . static::closeTag('select');
    }

    /**
     * Render optGroup tag
     *
     * @access public
     *
     * @param string $label label for options group
     * @param array $options format array(value, text, attributes) OR array(label, options, attributes)
     * @param array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function optGroup($label, array $options = [], array $attributes = [])
    {
        $opts = '';
        foreach ($options AS $option) {
            if (!empty($option['label'])) {
                $opts .= static::optGroup($option['label'], $option['options'], $option['attributes']);
            } else {
                $opts .= static::option($option['value'], $option['text'], $option['attributes']);
            }
        }

        return static::openTag('optgroup', array_merge($attributes, [
            'label' => $label
        ])) . $opts . static::closeTag('optgroup');
    }

    /**
     * Render option tag
     *
     * @access public
     *
     * @param string $value option value
     * @param string $text label for option
     * @param array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function option($value, $text, array $attributes = [])
    {
        return static::openTag('option', array_merge($attributes, [
            'value' =>$value
        ])) . $text . static::closeTag('option');
    }

    /**
     * Converting array to options
     *
     * @param array $arr Input array
     *
     * @return array|null Output array
     */
    public static function arrayToOptions(array $arr = [])
    {
        $result = [];

        foreach ($arr AS $n => $m) {
            $result[] = ['value' => $n, 'text' => $m];
        }

        return $result;
    }

    /**
     * Render reset button tag
     *
     * @access public
     *
     * @param  string $label text for label on button
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function resetButton($label = 'Reset', array $attributes = [])
    {
        return static::tag('input', array_merge($attributes, [
            'type' => 'reset',
            'value' => $label
        ]));
    }

    /**
     * Render submit button tag
     *
     * @access public
     *
     * @param  string $label text for label on button
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function submitButton($label = 'Submit', array $attributes = [])
    {
        return static::tag('input', array_merge($attributes, [
            'type' => 'submit',
            'value' => $label
        ]));
    }
}
