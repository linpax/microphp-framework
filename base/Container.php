<?php /** MicroContainer */

namespace Micro\Base;

/**
 * Container class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/lugnsk/micro
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Base
 * @version 1.0
 * @since 1.0
 */
class Container extends \stdClass implements IContainer
{
    /** @var array $data data */
    protected $data = [];
    /** @var array $config Configs */
    protected $config = [];
    /** @var array $components Components config */
    protected $components = [];


    /**
     * Load more configs from file
     *
     * @access public
     *
     * @param string $filename
     *
     * @return void
     */
    public function load($filename)
    {
        if (file_exists($filename)) {
            /** @noinspection PhpIncludeInspection */
            $this->config = array_merge_recursive($this->config, require $filename);
            $this->components = array_merge_recursive($this->components, $this->config['components']);
            unset($this->config['components']);
        }
    }

    /**
     * Is set component or option name into Container
     *
     * @access public
     *
     * @param string $name Name attribute
     *
     * @return bool
     */
    public function __isset($name)
    {
        if (array_key_exists($name, $this->config)) {
            return true;
        }
        if (array_key_exists($name, $this->data)) {
            return true;
        }
        if (array_key_exists($name, $this->components)) {
            return true;
        }

        return false;
    }

    /**
     * Get Container value
     *
     * @access public
     *
     * @param string $name element name
     *
     * @return mixed
     */
    public function __get($name = '')
    {
        if (!empty($this->config[$name])) {
            return $this->config[$name];
        }

        if (empty($this->data[$name]) && !$this->configure($name)) {
            return false;
        }

        return $this->data[$name];
    }

    /**
     * Set attribute
     *
     * @access public
     *
     * @param string $name Name attribute
     * @param mixed $component Component or option
     *
     * @return void
     */
    public function __set($name, $component)
    {
        $this->data[$name] = $component;
    }

    /**
     * Get component's
     *
     * @access public
     *
     * @param string|null $name name element to initialize
     *
     * @return bool
     */
    public function configure($name = null)
    {
        if (0 === count($this->components)) {
            return false;
        }

        if ($name === null) {
            foreach ($this->components AS $key => $options) {
                if (!$this->loadComponent($key, $options)) {
                    return false;
                }
            }

            return true;
        }

        if (empty($this->components[$name])) {
            return false;
        }

        if (!$this->loadComponent($name, $this->components[$name])) {
            return false;
        }

        return true;
    }

    /**
     * Load component
     *
     * @access public
     *
     * @param string $name component name
     * @param array $options component configs
     *
     * @return bool
     */
    public function loadComponent($name, $options)
    {
        if (empty($options['class']) || !class_exists($options['class'])) {
            return false;
        }

        $className = $options['class'];
        $this->data[$name] = null;

        $options['arguments'] = !empty($options['arguments']) ? $this->buildParams($options['arguments']) : null;
        $options['property'] = !empty($options['property']) ? $this->buildParams($options['property']) : null;
        $options['calls'] = !empty($options['calls']) ? $this->buildCalls($options['calls']) : null;

        try { // create object
            $reflection = new \ReflectionClass($className);
            $reflectionMethod = new \ReflectionMethod($className, '__construct');

            if ($reflectionMethod->getNumberOfParameters() === 0) {
                $this->data[$name] = new $className;
            } else {
                $this->data[$name] = $reflection->newInstanceArgs($options['arguments']);
            }

            unset($reflection, $reflectionMethod);
        } catch (Exception $e) {
            return false;
        }

        if (!empty($options['property'])) { // load properties
            foreach ($options['property'] as $property => $value) {
                if (property_exists($this->data[$name], $property)) {
                    $this->data[$name]->$property = $value;
                }
            }
        }

        if (!empty($options['calls'])) { // run methods
            foreach ($options['calls'] as $method => $arguments) {
                if (method_exists($this->data['name'], $method)) {
                    $reflectionMethod = new \ReflectionMethod($className, $method);
                    if ($reflectionMethod->getNumberOfParameters() === 0) {
                        $this->data['name']->$method();
                    } else {
                        call_user_func_array([$this->data['name'], $method], $arguments);
                    }
                }
            }
        }

        return true;
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
                    if (null === $this->{substr($val, 1)}) {
                        return false;
                    }
                    $val = $this->{substr($val, 1)};
                }
            }
        }

        return $params;
    }
}
