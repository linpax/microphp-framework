<?php /** MicroFieldTagTrait */

namespace Micro\Web\Html;


/**
 * FieldTagTrait trait file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Web\Html
 * @version 1.0
 * @since 1.0
 */
trait FieldTagTrait
{
    use TagTrait;


    /**
     * Render input button tag
     *
     * @access public
     *
     * @param  string $name button name
     * @param  string $value button value
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function buttonField($name, $value = null, array $attributes = [])
    {
        return static::field('button', $name, $value, $attributes);
    }

    /**
     * Base field tag
     *
     * @access private
     *
     * @param  string $type type of element
     * @param  string $name name of element
     * @param  string $value value of element
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    private static function field($type, $name, $value = null, array $attributes = [])
    {
        $attributes['id'] = !empty($attributes['id']) ? $attributes['id'] : $name;
        $attributes['type'] = $type;
        $attributes['name'] = $name;
        $attributes['value'] = $value;

        return static::tag('input', $attributes);
    }

    /**
     * Render input file tag
     *
     * @access public
     *
     * @param  string $name file name
     * @param  string $value file value
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function fileField($name, $value = null, array $attributes = [])
    {
        return static::field('file', $name, $value, $attributes);
    }

    /**
     * Render input hidden tag
     *
     * @access public
     *
     * @param  string $name hidden name
     * @param  string $value hidden value
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function hiddenField($name, $value = null, array $attributes = [])
    {
        return static::field('hidden', $name, $value, $attributes);
    }

    /**
     * Render input image tag
     *
     * @access public
     *
     * @param  string $name image name
     * @param  string $value image value
     * @param  string $srcFile path to image
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function imageField($name, $value = null, $srcFile, array $attributes = [])
    {
        $attributes['src'] = $srcFile;

        return static::field('image', $name, $value, $attributes);
    }

    /**
     * Render input password tag
     *
     * @access public
     *
     * @param  string $name password name
     * @param  string $value password value
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function passwordField($name, $value = null, array $attributes = [])
    {
        return static::field('password', $name, $value, $attributes);
    }

    /**
     * Render input text tag
     *
     * @access public
     *
     * @param  string $name text name
     * @param  string $value text value
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function textField($name, $value = null, array $attributes = [])
    {
        return static::field('text', $name, $value, $attributes);
    }

    /**
     * Render input color tag
     *
     * @access public
     *
     * @param  string $name color name
     * @param  string $value color value
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function colorField($name, $value = null, array $attributes = [])
    {
        return static::field('color', $name, $value, $attributes);
    }

    /**
     * Render input date tag
     *
     * @access public
     *
     * @param  string $name date name
     * @param  string $value date value
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function dateField($name, $value = null, array $attributes = [])
    {
        return static::field('date', $name, $value, $attributes);
    }

    /**
     * Render input datetime tag
     *
     * @access public
     *
     * @param  string $name datetime name
     * @param  string $value datetime value
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function datetimeField($name, $value = null, array $attributes = [])
    {
        return static::field('datetime', $name, $value, $attributes);
    }

    /**
     * Render input datetime-local tag
     *
     * @access public
     *
     * @param  string $name datetime-local name
     * @param  string $value datetime-local value
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function datetimeLocalField($name, $value = null, array $attributes = [])
    {
        return static::field('datetime-local', $name, $value, $attributes);
    }

    /**
     * Render input email tag
     *
     * @access public
     *
     * @param  string $name email name
     * @param  string $value email value
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function emailField($name, $value = null, array $attributes = [])
    {
        return static::field('email', $name, $value, $attributes);
    }

    /**
     * Render input number tag
     *
     * @access public
     *
     * @param  string $name number name
     * @param  string $value number value
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function numberField($name, $value = null, array $attributes = [])
    {
        return static::field('number', $name, $value, $attributes);
    }

    /**
     * Render input range tag
     *
     * @access public
     *
     * @param  string $name range name
     * @param  string $value range value
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function rangeField($name, $value = null, array $attributes = [])
    {
        return static::field('range', $name, $value, $attributes);
    }

    /**
     * Render input search tag
     *
     * @access public
     *
     * @param  string $name search name
     * @param  string $value search value
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function searchField($name, $value = null, array $attributes = [])
    {
        return static::field('search', $name, $value, $attributes);
    }

    /**
     * Render input tel tag
     *
     * @access public
     *
     * @param  string $name telephone name
     * @param  string $value telephone value
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function telField($name, $value = null, array $attributes = [])
    {
        return static::field('tel', $name, $value, $attributes);
    }

    /**
     * Render input time tag
     *
     * @access public
     *
     * @param  string $name time name
     * @param  string $value time value
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function timeField($name, $value = null, array $attributes = [])
    {
        return static::field('time', $name, $value, $attributes);
    }

    /**
     * Render input url tag
     *
     * @access public
     *
     * @param  string $name url name
     * @param  string $value url path
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function urlField($name, $value = null, array $attributes = [])
    {
        return static::field('url', $name, $value, $attributes);
    }

    /**
     * Render input month tag
     *
     * @access public
     *
     * @param  string $name month name
     * @param  string $value month value
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function monthField($name, $value = null, array $attributes = [])
    {
        return static::field('month', $name, $value, $attributes);
    }

    /**
     * Render input week tag
     *
     * @access public
     *
     * @param  string $name week name
     * @param  string $value week value
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function weekField($name, $value = null, array $attributes = [])
    {
        return static::field('week', $name, $value, $attributes);
    }

    /**
     * Render radio button list tag
     *
     * @access public
     *
     * @param string $name radio name
     * @param array $radios format array(text, value, attributes)
     * @param string $format %radio% - radio , %text% - text
     * @param string $selected name selected element
     *
     * @return string
     * @static
     */
    public static function radioButtonList($name, array $radios = [], $format = '<p>%radio% %text%</p>', $selected = '')
    {
        $rads = '';
        foreach ($radios AS $radio) {
            if (strcmp($radio['value'], $selected) === 0) {
                $radio['attributes']['checked'] = 'checked';
            }
            $rad = static::radioField($name, $radio['value'],
                !empty($radio['attributes']) ? $radio['attributes'] : []);
            $rads .= str_replace(['%radio%', '%text%'], [$rad, $radio['text']], $format);
        }

        return $rads;
    }

    /**
     * Render input radio tag
     *
     * @access public
     *
     * @param  string $name radio name
     * @param  string $value radio value
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function radioField($name, $value = null, array $attributes = [])
    {
        return static::field('radio', $name, $value, $attributes);
    }

    /**
     * Render checkBoxList (input checkbox tags)
     *
     * @access public
     *
     * @param string $name name for checkBox'es in list
     * @param array $checkboxes format array(text, value, attributes)
     * @param string $format %check% - checkbox , %text% - text
     * @param string $selected name selected element
     *
     * @return string
     * @static
     */
    public static function checkBoxList(
        $name,
        array $checkboxes = [],
        $format = '<p>%check% %text%</p>',
        $selected = ''
    ) {
        $checks = '';
        foreach ($checkboxes AS $checkbox) {
            if ($checkbox['value'] === $selected) {
                $checkbox['attributes']['selected'] = 'selected';
            }
            $check = static::checkBoxField($name, $checkbox['value'], $checkbox['attributes']);
            $checks .= str_replace('%text%', $checkbox['text'], str_replace('%check%', $check, $format));
        }

        return $checks;
    }

    /**
     * Render input checkbox tag
     *
     * @access public
     *
     * @param  string $name checkBox name
     * @param  string $value checkBox value
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function checkBoxField($name, $value = null, array $attributes = [])
    {
        return static::field('checkbox', $name, $value, $attributes);
    }
}
