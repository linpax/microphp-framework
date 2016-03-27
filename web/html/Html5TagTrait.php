<?php

namespace Micro\Web\Html;


/**
 * Html5TagTrait trait file.
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
trait Html5TagTrait
{
    use TagTrait;


    /**
     * Render charset tag
     *
     * @access public
     *
     * @param  string $name charset name
     *
     * @return string
     * @static
     */
    public static function charset($name)
    {
        return static::tag('meta', ['charset' => $name]);
    }

    /**
     * Render video tag
     *
     * @access public
     *
     * @param array $sources format type=>src
     * @param array $tracks format array(kind, src, srclang, label)
     * @param array $attributes attributes tag
     * @param string $noCodec text
     *
     * @return string
     * @static
     */
    public static function video(array $sources = [], array $tracks = [], array $attributes = [], $noCodec = '')
    {
        $srcs = '';

        foreach ($sources AS $name => $value) {
            $srcs .= static::tag('source', ['type' => $name, 'src' => $value]);
        }

        foreach ($tracks AS $track) {
            $srcs .= static::tag('track', [
                'kind' => $track['kind'],
                'src' => $track['src'],
                'srclang' => $track['srclang'],
                'label' => $track['label']
            ]);
        }

        return static::openTag('video', $attributes) . $srcs . $noCodec . static::closeTag('video');
    }

    /**
     * Render audio tag
     *
     * @access public
     *
     * @param array $sources format type=>src
     * @param array $tracks format array(kind, src, srclang, label)
     * @param array $attributes attributes tag
     * @param string $noCodec text
     *
     * @return string
     * @static
     */
    public static function audio(array $sources = [], array $tracks = [], array $attributes = [], $noCodec = '')
    {
        $srcs = '';

        foreach ($sources AS $name => $value) {
            $srcs .= static::tag('audio', ['type' => $name, 'src' => $value]);
        }

        foreach ($tracks AS $track) {
            $srcs .= static::tag('track', [
                'kind' => $track['kind'],
                'src' => $track['src'],
                'srclang' => $track['srclang'],
                'label' => $track['label']
            ]);
        }

        return static::openTag('audio', $attributes) . $srcs . $noCodec . static::closeTag('audio');
    }

    /**
     * Render canvas tag
     *
     * @access public
     *
     * @param array $attributes attributes tag
     * @param string $noCodec text
     *
     * @return string
     * @static
     */
    public static function canvas(array $attributes = [], $noCodec = '')
    {
        return static::openTag('canvas', $attributes) . $noCodec . static::closeTag('canvas');
    }
}
