<?php /** MicroController */

namespace Micro\Mvc\Controllers;

use Micro\Base\Exception;
use Micro\Mvc\Module;
use Micro\Web\ResponseInjector;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Controller
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Mvc\Controllers
 * @version 1.0
 * @since 1.0
 * @abstract
 */
abstract class Controller implements IController
{
    /** @var Module $module */
    public $module;
    /** @var ResponseInterface $response Response HTTP data */
    public $response;


    /**
     * Constructor controller
     *
     * @access public
     *
     * @param string $modules
     *
     * @result void
     * @throws Exception
     */
    public function __construct($modules = '')
    {
        // if module defined
        if ($modules) {
            $className = '\\App'.$modules.'\\'.ucfirst(basename(str_replace('\\', '/', $modules))).'Module';

            if (class_exists($className) && is_subclass_of($className, '\Micro\Mvc\Module')) {
                $this->module = new $className();
            }
        }

        if (!$this->response = (new ResponseInjector)->build()) {
            throw new Exception('Component `response` not configured');
        }
    }

    /**
     * Apply filters
     *
     * @access public
     *
     * @param string $action current action name
     * @param bool $isPre is pre or post
     * @param array $filters defined filters
     * @param string $data data to parse
     *
     * @return null|string
     * @throws Exception|\InvalidArgumentException
     */
    public function applyFilters($action, $isPre = true, array $filters = [], $data = null)
    {
        if (!$filters) {
            return $data;
        }

        foreach ($filters as $filter) {
            if (empty($filter['class']) || !class_exists($filter['class'])) {
                continue;
            }

            if (empty($filter['actions']) || !in_array($action, $filter['actions'], true)) {
                continue;
            }

            /** @var \Micro\Filter\IFilter $_filter */
            $_filter = new $filter['class']($action);
            /** @var ResponseInterface $response */
            $response = $isPre ? $_filter->pre($filter) : $_filter->post($filter + ['data' => $data]);

            if (!$response) {
                if (!empty($_filter->result['redirect'])) {
                    /** @var ResponseInterface $redirect */
                    $redirect = (new ResponseInjector)->build();
                    return $redirect->withHeader('Location', $_filter->result['redirect']);
                }
                throw new Exception($_filter->result['message']);
            }
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $data = $response;
        }

        return $data;
    }

    /**
     * Get action class by name
     *
     * @access public
     *
     * @param string $name action name
     *
     * @return bool
     */
    public function getActionClassByName($name)
    {
        if (method_exists($this, 'actions')) {
            $actions = $this->actions();

            if (
                !empty($actions[$name]) &&
                class_exists($actions[$name]) &&
                is_subclass_of($actions[$name], '\Micro\Mvc\Action')
            ) {
                return $actions[$name];
            }
        }

        return false;
    }
}
