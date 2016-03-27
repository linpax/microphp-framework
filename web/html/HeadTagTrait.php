<?php

namespace Micro\Web\Html;


/**
 * HeadTagTrait trait file.
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
trait HeadTagTrait
{
    use TagTrait;


    /**
     * @var array $docTypes Document types
     */
    private static $docTypes = [
        'xhtml11' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
        'xhtml1-trans' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
        'xhtml1-strict' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
        'xhtml1-frame' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
        'html4-trans' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
        'html4-strict' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
        'html4-frame' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
        'html5' => '<!DOCTYPE html>'
    ];


    /**
     * Render meta tag
     *
     * @access public
     *
     * @param  string $name name of element
     * @param  string $content content of element
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function meta($name, $content, array $attributes = [])
    {
        return static::tag('meta', array_merge($attributes, [
            'name'=> $name,
            'content' => $content
        ]));
    }

    /**
     * Render link tag
     *
     * @access public
     *
     * @param  string $name name of element
     * @param  string $url url path
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function link($name, $url, array $attributes = [])
    {
        return static::openTag('link', array_merge($attributes, ['href' => $url])) .
            $name .
            static::closeTag('link');
    }

    /**
     * Render favicon file
     *
     * @access public
     *
     * @param string $url path to favicon
     *
     * @return string
     * @static
     */
    public static function favicon($url)
    {
        return static::tag('link', ['href' => $url, 'rel' => 'shortcut icon', 'type' => 'image/x-icon']);
    }

    /**
     * Render css file
     *
     * @access public
     *
     * @param  string $file path to css
     *
     * @return string
     * @static
     */
    public static function cssFile($file)
    {
        return static::tag('link', ['href' => $file, 'rel' => 'stylesheet']);
    }

    /**
     * Render script file
     *
     * @access public
     *
     * @param  string $file path to script
     *
     * @return string
     * @static
     */
    public static function scriptFile($file)
    {
        return static::openTag('script', ['src' => $file, 'type' => 'text/javascript']) . static::closeTag('script');
    }

    /**
     * Render style source
     *
     * @access public
     *
     * @param  string $text style
     * @param  array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function css($text, array $attributes = [])
    {
        return static::openTag('style', array_merge($attributes, ['type' => 'text/css'])) .
            $text .
            static::closeTag('style');
    }

    /**
     * Render script source
     *
     * @access public
     *
     * @param string $text script
     * @param array $attributes attributes tag
     * @param string $type type of script
     *
     * @return string
     * @static
     */
    public static function script($text, array $attributes = [], $type = 'text/javascript')
    {
        return static::openTag('script',
            array_merge($attributes, ['type' => $type])) .
            ' /*<![CDATA[*/ ' . $text . ' /*]]>*/ ' .
            static::closeTag('script');
    }

    /**
     * Render docType tag
     *
     * @access public
     *
     * @param  string $name doctype name
     *
     * @return string|boolean
     * @static
     */
    public static function doctype($name)
    {
        return empty(static::$docTypes[$name]) ? false : static::$docTypes[$name];
    }

    /**
     * Render title tag
     *
     * @access public
     *
     * @param string $name title name
     *
     * @return string
     * @static
     */
    public static function title($name)
    {
        return static::openTag('title') . $name . static::closeTag('title');
    }
}
