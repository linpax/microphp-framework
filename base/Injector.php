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
class Injector
{
    /** @var array $CONFIG Config data for components */
    private static $CONFIG;


    /**
     * Injector constructor.
     *
     * @access public
     * @param array $config
     * @result void
     */
    public function __construct(array $config = [])
    {
        if (0 === count($config)) {
            self::$CONFIG = (0 === count(self::$CONFIG)) ? array_merge_recursive(self::$CONFIG, $config) : $config;
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
     * @return bool
     */
    protected function get($name)
    {
        if (!empty(self::$CONFIG[$name])) {
            return $this->loadComponent(self::$CONFIG[$name]);
        }

        return false;
    }

    /**
     * Load component
     *
     * @access public
     *
     * @param array $options component configs
     *
     * @return bool
     */
    private function loadComponent($options)
    {
        if (empty($options['class']) || !class_exists($options['class'])) {
            return false;
        }

        $className = $options['class'];
        $object = null;

        $options['arguments'] = !empty($options['arguments']) ? $this->buildParams($options['arguments']) : null;
        $options['property'] = !empty($options['property']) ? $this->buildParams($options['property']) : null;
        $options['calls'] = !empty($options['calls']) ? $this->buildCalls($options['calls']) : null;

        $object = $this->makeObject($className, $options['arguments']);
        if (!$object) {
            return false;
        }

        if (!empty($options['property'])) { // load properties
            foreach ($options['property'] as $property => $value) {
                if (property_exists($object, $property)) {
                    $object->$property = $value;
                }
            }
        }

        if (!empty($options['calls'])) { // run methods
            foreach ($options['calls'] as $method => $arguments) {
                if (method_exists($object, $method)) {
                    $reflectionMethod = new \ReflectionMethod($className, $method);
                    if ($reflectionMethod->getNumberOfParameters() === 0) {
                        $object->$method();
                    } else {
                        call_user_func_array([$object, $method], $arguments);
                    }
                }
            }
        }

        return true;
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