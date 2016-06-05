<?php /** MicroHMVCResolver */

namespace Micro\Resolver;

use Micro\Base\Exception;
use Micro\Base\KernelInjector;
use Micro\Mvc\Controllers\IController;
use Micro\Web\IRequest;
use Micro\Web\RequestInjector;
use Micro\Web\RouterInjector;

/**
 * hMVC Resolver class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Resolver
 * @version 1.0
 * @since 1.0
 */
class HMVCResolver extends Resolver
{
    /** @var string $uri converted URL */
    protected $uri;

    /** @var string $extensions Extensions in request */
    private $extensions;
    /** @var string $modules Modules in request */
    private $modules;
    /** @var string $controller IController to run */
    private $controller;
    /** @var string $action Action to run */
    private $action;


    /**
     * Get instance application
     *
     * @access public
     *
     * @return IController
     * @throws Exception
     */
    public function getApplication()
    {
        /** @var IRequest $request */
        $request = (new RequestInjector)->get();

        $query = $request->query('r') ?: '/default';
        $query = (substr($query, -1) === '/') ? substr($query, 0, -1) : $query;

        $this->uri = (new RouterInjector)->get()->parse($query, $request->getMethod());

        $this->initialize();

        /** @var string $cls */
        $cls = $this->getCalculatePath();

        if (!class_exists($cls) || !is_subclass_of($cls, '\Micro\Mvc\Controllers\IController')) {
            throw new Exception('Controller '.$cls.' not found or not a valid');
        }

        return new $cls($this->getModules());
    }

    /**
     * Initialize request object
     *
     * @access public
     *
     * @return void
     * @throws Exception
     */
    protected function initialize()
    {
        $key = strpos($this->uri, '?');
        $params = $key ? substr($this->uri, $key + 2) : null;
        $uriBlocks = explode('/', substr($this->uri, 0, $key ?: strlen($this->uri)));

        if (0 === strpos($this->uri, '/')) {
            array_shift($uriBlocks);
        }

        $this->prepareExtensions($uriBlocks);
        $this->prepareModules($uriBlocks);
        $this->prepareController($uriBlocks);
        $this->prepareAction($uriBlocks);

        if ($params) {
            $paramBlocks = explode('&', $params);

            foreach ($paramBlocks AS $param) {
                $val = explode('=', $param);
                (new RequestInjector)->get()->setQuery($val[0], $val[1]);
            }
        }
    }

    /**
     * Prepare extensions
     *
     * @access private
     *
     * @param array $uriBlocks uri blocks from URL
     *
     * @return void
     * @throws Exception
     */
    protected function prepareExtensions(&$uriBlocks)
    {
        foreach ($uriBlocks as $i => $block) {
            if (file_exists((new KernelInjector)->get()->getAppDir().$this->extensions.'/extensions/'.$block)) {
                $this->extensions .= '/Extensions/'.ucfirst($block);

                unset($uriBlocks[$i]);
            } else {
                break;
            }
        }

        $this->extensions = str_replace('/', '\\', $this->extensions);
    }

    /**
     * Prepare modules
     *
     * @access private
     *
     * @global      Micro
     *
     * @param array $uriBlocks uri blocks from URL
     *
     * @return void
     * @throws Exception
     */
    protected function prepareModules(&$uriBlocks)
    {
        $path = (new KernelInjector)->get()->getAppDir().($this->extensions ?: '');

        foreach ($uriBlocks as $i => $block) {
            if ($block && file_exists($path.strtolower($this->modules).'/modules/'.$block)) {
                $this->modules .= '/Modules/'.ucfirst($block);

                unset($uriBlocks[$i]);
            } else {
                break;
            }
        }

        $this->modules = str_replace('/', '\\', $this->modules);
    }

    /**
     * Prepare controller
     *
     * @access private
     *
     * @param array $uriBlocks uri blocks from URL
     *
     * @return void
     * @throws Exception
     */
    protected function prepareController(&$uriBlocks)
    {
        $path = (new KernelInjector)->get()->getAppDir().($this->extensions ?: '').strtolower($this->modules ?: '');
        $str = array_shift($uriBlocks);

        if (file_exists(str_replace('\\', '/', $path.'/controllers/'.ucfirst($str).'Controller.php'))) {
            $this->controller = $str;
        } else {
            $this->controller = 'default';

            array_unshift($uriBlocks, $str);
        }
    }

    /**
     * Prepare action
     *
     * @access private
     *
     * @param array $uriBlocks uri blocks from URL
     *
     * @return void
     */
    protected function prepareAction(&$uriBlocks)
    {
        $this->action = array_shift($uriBlocks) ?: 'index';
    }


    /**
     * Get calculate path to controller
     *
     * @access public
     *
     * @return string
     */
    public function getCalculatePath()
    {
        return '\\App'.$this->getExtensions().$this->getModules().'\\Controllers\\'.$this->getController();
    }

    /**
     * Get extensions from request
     *
     * @access public
     *
     * @return string
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Get modules from request
     *
     * @access public
     *
     * @return string
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Get controller from request
     *
     * @access public
     *
     * @return string
     */
    public function getController()
    {
        return ucfirst($this->controller).'Controller';
    }


    /**
     * Get action from request
     *
     * @access public
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
}
