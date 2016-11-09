<?php /** MicroTableTagTrait */

namespace Micro\Web\Html;


/**
 * TableTagTrait trait file.
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
trait TableTagTrait
{
    use TagTrait;


    /**
     * Render table element
     *
     * How to use $elements:
     * array(
     *     array( // row
     *         'cells'=>array( // cell
     *             'value'=>'text',
     *             'attributes'=>[]
     *         ),
     *         attributes'=>[]
     *     )
     * )
     *
     * @access public
     *
     * @param array $elements table elements
     * @param array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function table(array $elements = [], array $attributes = [])
    {
        $output = null;
        foreach ($elements AS $value) {
            $output .= static::tableRow(
                !empty($value['cells']) ? $value['cells'] : [],
                !empty($value['header']) ? $value['header'] : false,
                !empty($value['attributes']) ? $value['attributes'] : []
            );
        }

        return static::beginTable($attributes).$output.static::endTable();
    }

    /**
     * Render table row element
     *
     * @access public
     *
     * @param array $elements array(value, attributes)
     * @param boolean $isHeading row is heading?
     * @param array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function tableRow(array $elements = [], $isHeading = false, array $attributes = [])
    {
        $output = null;
        foreach ($elements as $value) {
            if ($isHeading === false) {
                $output .= static::tableCell(
                    !empty($value['value']) ? $value['value'] : [],
                    !empty($value['attributes']) ? $value['attributes'] : []
                );
            } else {
                $output .= static::tableHeading(
                    !empty($value['value']) ? $value['value'] : [],
                    !empty($value['attributes']) ? $value['attributes'] : []
                );
            }
        }

        return static::openTag('tr', $attributes).$output.static::closeTag('tr');
    }

    /**
     * Render table cell element
     *
     * @access public
     *
     * @param string $text table cell text
     * @param array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function tableCell($text, array $attributes = [])
    {
        return static::openTag('td', $attributes).$text.static::closeTag('td');
    }

    /**
     * Render table heading tag
     *
     * @access public
     *
     * @param string $text table heading text
     * @param array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function tableHeading($text, array $attributes = [])
    {
        return static::openTag('th', $attributes).$text.static::closeTag('th');
    }

    /**
     * Render begin table element
     *
     * @access public
     *
     * @param array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function beginTable(array $attributes = [])
    {
        return static::openTag('table', $attributes);
    }

    /**
     * Render end table element
     *
     * @access public
     * @return string
     * @static
     */
    public static function endTable()
    {
        return static::closeTag('table');
    }

    /**
     * Render table caption element
     *
     * @access public
     *
     * @param string $text table caption text
     * @param array $attributes attributes tag
     *
     * @return string
     * @static
     */
    public static function tableCaption($text, array $attributes = [])
    {
        return static::openTag('caption', $attributes).$text.static::closeTag('caption');
    }
}
