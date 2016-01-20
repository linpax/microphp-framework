<?php /** MicroAutoloader */

namespace Micro\base;

/**
 * Autoload class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/lugnsk/micro
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package micro
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
        static::$aliases[$alias][] = $realPath;
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
     *
     * @return string
     * @static
     */
    public static function getClassPath($className)
    {
        $className = str_replace('_', '\\', $className); // fix PSR-0 compatible
        $prefix    = $className;

        while (false !== $position = strrpos($prefix, '\\')) {
            $prefix = substr($prefix, 0, $position);

            if (array_key_exists($prefix, static::$aliases)) {
                foreach (static::$aliases[$prefix] AS $dir) {
                    $path = $dir . '\\' . substr($className, mb_strlen($prefix) + 1);
                    $absolutePath = str_replace('\\', DIRECTORY_SEPARATOR, $path) . '.php';

                    if (is_readable($absolutePath)) {
                        return $path;
                    }
                }
            }
        }

        return false;
    }
}
