<?php /** MicroFatalError */

namespace Micro\Base;

/**
 * Class FatalError
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Base
 * @version 1.0
 * @since 1.0
 */
class FatalError
{
    /** @var integer $number Error number */
    protected static $number = 0;
    /** @var string $message Error message */
    protected static $message = '';
    /** @var string $file File name */
    protected static $file = '';
    /** @var int $line Current line */
    protected static $line = 0;
    /** @var array $context Program execution context */
    protected static $context = [];
    /** @var array $trace Call trace */
    protected static $trace = [];


    /**
     * Register FatalError handler
     *
     * @access public
     *
     * @return void
     * @static
     */
    public static function register()
    {
        set_error_handler(['\Micro\Base\FatalError', 'handle']);
    }

    /**
     * Fatal error handle
     *
     * @access public
     * 
     * @param int $number
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array $context
     *
     * @return void
     * @static
     */
    public static function handle($number = 0, $message = '', $file = '', $line = 0, array $context = [])
    {
        self::$context = $context;
        self::$message = $message;
        self::$number = $number;
        self::$trace = debug_backtrace();
        self::$file = $file;
        self::$line = $line;

        $level = ob_get_level();
        if ($level > 0) {
            for ($i = ob_get_level(); $i >= 0; $i--) {
                ob_clean();
            }
        }

        print('cli' === php_sapi_name() ? static::doCli() : static::doRun());
    }

    /**
     * @access protected
     *
     * @return string
     * @static
     */
    protected static function doCli()
    {
        return static::$number.' - '.static::$message.' on '.static::$file.':'.static::$line;
    }

    /**
     * @access protected
     *
     * @return string
     * @static
     */
    protected static function doRun()
    {
        $str = '<div class="error" style="width: 100%;">';
        $str .= '<h2>FatalError '.static::$number.' - '.static::$message.' on '.static::$file.':'.static::$line.'</h2>';

        $str .= '<table width="100%" style="width: 100%">';
        $str .= '<tr>';
        $str .= '<th width="100px">Context</th>';
        $str .= '<td style="vertical-align: top; height: 300px">';
        $str .= '<textarea disabled style="width:100%; height: 100%">'.print_r(static::$context,
                true).'</textarea>';
        $str .= '</td>';
        $str .= '</tr>';
        $str .= '<tr>';
        $str .= '<th width="100px">Debug trace</th>';
        $str .= '<td style="vertical-align: top; height: 300px">';
        $str .= '<textarea disabled style="width: 100%; height: 100%">'.print_r(static::$trace, true).'</textarea>';
        $str .= '</td>';
        $str .= '</tr>';
        $str .= '</table>';
        $str .= '</div>';

        return $str;
    }
}
