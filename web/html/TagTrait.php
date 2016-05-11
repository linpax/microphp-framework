<?php /** MicroTagTrait */

namespace Micro\Web\Html;


/**
 * TagTrait trait file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Web\Html
 * @version 1.0
 * @since 1.0
 */
trait TagTrait
{
    /**
     * Render BR tag
     *
     * @access public
     *
     * @param integer $num number of render BR's
     * @param array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function br($num = 1, array $attributes = [])
    {
        $str = '';
        for ($i = 0; $i < $num; $i++) {
            $str .= static::tag('br', $attributes);
        }

        return $str;
    }

    /**
     * Render tag
     *
     * @access public
     *
     * @param  string $name tag name
     * @param  array $attributes tag attributes
     *
     * @return string
     * @static
     */
    public static function tag($name, array $attributes = [])
    {
        $result = '';
        foreach ($attributes AS $elem => $value) {
            $result .= ' ' . $elem . '="' . $value . '" ';
        }

        return '<' . $name . $result . '/>';
    }

    /**
     * Render mail a tag
     *
     * @access public
     *
     * @param  string $name name of e-mail
     * @param  string $email e-mail path
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function mailto($name, $email, array $attributes = [])
    {
        return static::openTag('a', array_merge($attributes, ['href' => 'mailto:' . $email])) .
        $name .
        static::closeTag('a');
    }

    /**
     * Render open tag
     *
     * @access public
     *
     * @param  string $name tag name
     * @param  array $attributes tag attributes
     *
     * @return string
     * @static
     */
    public static function openTag($name, array $attributes = [])
    {
        $result = '';
        foreach ($attributes AS $key => $value) {
            $result .= ' ' . $key . '="' . $value . '"';
        }

        return '<' . $name . $result . '>';
    }

    /**
     * Render close tag
     *
     * @access public
     *
     * @param  string $name tag name
     *
     * @return string
     * @static
     */
    public static function closeTag($name)
    {
        return '</' . $name . '>';
    }

    /**
     * Render anchor
     *
     * @access public
     *
     * @param string $name name to link
     * @param string $url path to link
     * @param array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function href($name, $url, array $attributes = [])
    {
        return static::openTag('a', array_merge($attributes, ['href' => $url])).
            $name.
            static::closeTag('a');
    }

    /**
     * Render H{1-N} tag
     *
     * @access public
     *
     * @param  string $num H number
     * @param  string $value H value
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function heading($num, $value = null, array $attributes = [])
    {
        return static::openTag('h'.$num, $attributes).$value.static::closeTag('h'.$num);
    }

    /**
     * Render image map tag
     *
     * @access public
     *
     * @param string $alt Alternative text
     * @param string $source Path to image
     * @param string $name Map name
     * @param array $attributeImg Attributes for image
     * @param array $coordinates Coordinates for image
     *
     * @return string
     * @static
     */
    public static function imageMap($alt, $source, $name, array $attributeImg = [], array $coordinates = [])
    {
        $areas = '';
        foreach ($coordinates AS $coord) {
            $areas .= static::tag('area', $coord);
        }

        return static::image($alt, $source, array_merge($attributeImg, ['usemap' => $name])).
        static::openTag('map', ['name' => $name, 'id' => $name]).
        $areas.
        static::closeTag('map');
    }

    /**
     * Render image file
     *
     * @access public
     *
     * @param  string $name name of image
     * @param  string $file path image file
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function image($name, $file, array $attributes = [])
    {
        return static::tag('img', array_merge($attributes, [
            'src' => $file,
            'alt' => $name
        ]));
    }

    /**
     * Render object tag
     *
     * @access public
     *
     * @param string $source Path to content
     * @param array $attributes Attributes for object
     * @param array $params Parameters for object
     *
     * @return string
     * @static
     */
    public static function object($source, array $attributes = [], array $params = [])
    {
        $attributes['data'] = $source;
        $paramsConverted = '';

        foreach ($params AS $key => $val) {
            $paramsConverted .= static::tag('param', ['name' => $key, 'value' => $val]);
        }

        return static::openTag('object', $attributes).$paramsConverted.static::closeTag('object');
    }

    /**
     * Embedding objects (video, audio, flash, etc.)
     *
     * @access public
     *
     * @param string $source Path to content
     * @param array $attributes Attributes for embedding
     *
     * @return string
     */
    public static function embed($source, array $attributes = [])
    {
        $attributes['source'] = $source;

        return static::openTag('embed', $attributes).static::closeTag('embed');
    }

    /**
     * List elements generator
     *
     * @access public
     *
     * @param array $items lists multiple array
     * @param array $attributes attributes tag
     * @param bool $isNumeric Is a numeric list?
     *
     * @return string
     * @static
     */
    public static function lists(array $items = [], array $attributes = [], $isNumeric = false)
    {
        $parentTag = $isNumeric ? 'ol' : 'ul';

        $result = null;
        foreach ($items AS $item) {
            $result .= static::openTag('li', !empty($item['attr']) ? $item['attr'] : []);
            if (!empty($item['parents'])) {
                $result .= !empty($item['text']) ? $item['text'] : null;
                $result .= static::lists(
                    $item['parents'],
                    (!empty($item['parentsAttr']) ? $item['parentsAttr'] : []),
                    (!empty($item['parentsIsNumeric']) ? true : false)
                );
            } else {
                $result .= $item['text'];
            }
            $result .= static::closeTag('li');
        }

        return static::openTag($parentTag, $attributes).$result.static::closeTag($parentTag);
    }
}
