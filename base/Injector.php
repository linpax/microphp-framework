<?php /** MicroInjector */

namespace Micro\Base;


/**
 * Class Injector
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
class Injector implements IInjector
{
    /** @var array $CONFIG Configuration */
    private static $CONFIG = [];
    /** @var array $INJECTS Configured injects */
    private static $INJECTS = [];


    /**
     * Injector constructor.
     *
     * @access public
     * @param string $configPath
     * @result void
     */
    public function __construct($configPath = '')
    {
        if ($configPath !== '' && file_exists($configPath)) {
            /** @noinspection PhpIncludeInspection */
            self::$CONFIG = array_merge_recursive(self::$CONFIG, require $configPath);
        }
    }

    /**
     * @param string $name
     * @return string
     */
    public function param($name)
    {
        return array_key_exists($name, self::$CONFIG) ? self::$CONFIG[$name] : null;
    }

    /**
     * Add requirement to injector
     *
     * @access public
     * @param string $name
     * @param mixed $component
     * @return void
     */
    public function addRequirement($name, $component)
    {
        if (is_object($component)) {
            self::$INJECTS[$name] = $component;
        } else {
            self::$CONFIG[$name] = $component;
        }
    }

    /**
     * Build object with injector
     *
     * class LogInject extends Injector {
     *  public function build() {
     *   return $this->get('logger');
     *  }
     * }
     * $log = (new LogInject())->build();
     *
     * @access protected
     * @param string $name
     * @return false
     */
    protected function get($name)
    {
        if (!empty(self::$CONFIG[$name])) {
            return self::$CONFIG[$name];
        }

        if (!empty(self::$CONFIG['components'][$name])) {
            return $this->loadInjection($name);
        }

        return false;
    }

    /**
     * Load injection
     *
     * @access public
     *
     * @param string $name Name injection
     *
     * @return false
     */
    private function loadInjection($name)
    {
        $options = self::$CONFIG['components'][$name];

        if (empty($options['class']) || !class_exists($options['class'])) {
            return false;
        }

        $className = $options['class'];

        $options['arguments'] = !empty($options['arguments']) ? $this->buildParams($options['arguments']) : null;
        $options['property'] = !empty($options['property']) ? $this->buildParams($options['property']) : null;
        $options['calls'] = !empty($options['calls']) ? $this->buildCalls($options['calls']) : null;

        /** Depends via construction */
        self::$INJECTS[$name] = $this->makeObject($className, $options['arguments']);
        if (!self::$INJECTS[$name]) {
            return false;
        }

        /** Depends via property */
        if (!empty($options['property'])) { // load properties
            foreach ($options['property'] as $property => $value) {
                if (property_exists(self::$INJECTS[$name], $property)) {
                    self::$INJECTS[$name]->$property = $value;
                }
            }
        }

        /** Depends via calls */
        if (!empty($options['calls'])) { // run methods
            foreach ($options['calls'] as $method => $arguments) {
                if (method_exists(self::$INJECTS[$name], $method)) {
                    $reflectionMethod = new \ReflectionMethod($className, $method);
                    if ($reflectionMethod->getNumberOfParameters() === 0) {
                        self::$INJECTS[$name]->$method();
                    } else {
                        call_user_func_array([self::$INJECTS[$name], $method], $arguments);
                    }
                }
            }
        }

        return self::$INJECTS[$name];
    }

    /**
     * Build params from array
     *
     * @access private
     * @param array $params
     * @return array
     */
    private function buildParams(array $params)
    {
        /** @noinspection AlterInForeachInspection */
        foreach ($params AS $key => &$val) { // IoC Constructor
            if (is_string($params[$key]) && (0 === strpos($val, '@'))) {
                if ($val === '@this') {
                    $val = $this;
                } else {
                    $val = $this->get(substr($val, 1));
                }
            }
        }

        return $params;
    }

    /**
     * Build calls arguments
     *
     * @access private
     * @param array $params
     * @return array
     */
    private function buildCalls(array $params)
    {
        $callers = [];

        if (!is_array($params[0])) {
            $params = [
                $params
            ];
        }

        foreach ($params as $arguments) {
            if (is_string($arguments[0])) {
                if (!empty($arguments[1]) && is_array($arguments[1])) {
                    $callers[$arguments[0]] = $this->buildParams($arguments[1]);
                } else {
                    $callers[$arguments[0]] = null;
                }
            }
        }

        return $callers;
    }

    /**
     * Make object with arguments
     *
     * @access private
     *
     * @param string $className
     * @param array $arguments
     *
     * @return mixed
     */
    private function makeObject($className, array $arguments = [])
    {
        try {
            $reflection = new \ReflectionClass($className);
            $reflectionMethod = new \ReflectionMethod($className, '__construct');

            if ($reflectionMethod->getNumberOfParameters() === 0) {
                return new $className;
            } else {
                return $reflection->newInstanceArgs($arguments);
            }
        } catch (Exception $e) {
            return false;
        }
    }
}