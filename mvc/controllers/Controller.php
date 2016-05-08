<?php /** MicroController */

namespace Micro\Mvc\Controllers;

use Micro\Base\Exception;
use Micro\Base\IContainer;
use Micro\Mvc\Module;
use Micro\Web\IResponse;
use Micro\Web\Response;

/**
 * Class Controller
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/lugnsk/micro
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
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
    /** @var IResponse $response Response HTTP data */
    public $response;
    /** @var IContainer $container */
    protected $container;

    /**
     * Constructor controller
     *
     * @access public
     *
     * @param IContainer $container
     * @param string $modules
     *
     * @result void
     */
    public function __construct(IContainer $container, $modules = '')
    {
        $this->container = $container;

        // if module defined
        if ($modules) {
            $className = '\\App'.$modules.'\\'.ucfirst(basename(str_replace('\\', '/', $modules))).'Module';

            if (class_exists($className) && is_subclass_of($className, '\Micro\Mvc\Module')) {
                $this->module = new $className($this->container);
            }
        }

        if (!$this->response = $this->container->response) {
            $this->response = new Response;
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
     * @throws Exception
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
            $_filter = new $filter['class']($action, $this->container);
            $response = $isPre ? $_filter->pre($filter) : $_filter->post($filter + ['data' => $data]);

            if (!$response) {
                if (!empty($_filter->result['redirect'])) {
                    /** @var IResponse $redirect */
                    $redirect = $this->container->response ?: new Response;
                    $redirect->addHeader('Location', $_filter->result['redirect']);

                    return $redirect;
                }
                throw new Exception($_filter->result['message']);
            }
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
