<?php /** MicroAutoload */

namespace Micro\Base;

/**
 * Autoload class file.
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
class Autoload
{
    /** @var array $aliases Autoload aliases maps */
    private static $aliases = [];


    /**
     * Setting or installing new alias
     *
     * @access public
     *
     * @param string $alias name for new alias
     * @param string $realPath path of alias
     *
     * @return void
     * @static
     */
    public static function setAlias($alias, $realPath)
    {
        static::$aliases[strtolower($alias)] = $realPath;
    }

    /**
     * Loader classes
     *
     * @access public
     *
     * @param string $className search class name
     *
     * @return bool
     * @static
     */
    public static function loader($className)
    {
        if ($path = static::getClassPath(ltrim($className, '\\'))) {
            /** @noinspection PhpIncludeInspection */
            require_once $path;

            return true;
        }

        return false;
    }

    /**
     * Get class path
     *
     * @access public
     *
     * @param string $className search class name
     * @param string $extension extension of class
     *
     * @return string
     * @static
     */
    public static function getClassPath($className, $extension = '.php')
    {
        $prefix = $className = static::CamelCaseToLowerNamespace(str_replace('_', '\\', $className));

        while (false !== $position = strrpos($prefix, '\\')) {
            $prefix = substr($prefix, 0, $position);
            $alias = self::getAlias($prefix);

            if (!$alias) {
                continue;
            }

            $path = $alias.'\\'.substr($className, mb_strlen($prefix) + 1);
            $absolutePath = str_replace('\\', DIRECTORY_SEPARATOR, $path).$extension;

            if (is_readable($absolutePath)) {
                return $absolutePath;
            }
        }

        return false;
    }

    /**
     * Convert first symbols of namespace to lowercase
     *
     * @access protected
     *
     * @param string $path
     *
     * @return string
     * @static
     */
    private static function CamelCaseToLowerNamespace($path)
    {
        $classNameArr = array_map(function($val) {
            return lcfirst($val);
        }, explode('\\', $path));

        $classNameArr[] = ucfirst(array_pop($classNameArr));

        return implode('\\', $classNameArr);
    }

    /**
     * Get alias
     *
     * @access public
     * @param string $alias
     * @return string|bool
     * @static
     */
    public static function getAlias($alias)
    {
        $alias = strtolower($alias);

        return array_key_exists($alias, static::$aliases) ? static::$aliases[$alias] : false;
    }
}
