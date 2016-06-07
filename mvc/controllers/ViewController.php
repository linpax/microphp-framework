<?php /** MicroController */

namespace Micro\Mvc\Controllers;

use Micro\Base\Exception;
use Micro\Web\IResponse;
use Micro\Web\Response;
use Micro\Web\ResponseInjector;

/**
 * Class Controller
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package Micro
 * @subpackage Mvc\Controllers
 * @version 1.0
 * @since 1.0
 */
abstract class ViewController extends Controller
{
    /** @var string $layout */
    public $layout;
    /** @var bool $asWidget */
    public $asWidget = false;


    /**
     * @inheritdoc
     * @throws Exception
     */
    public function action($name = 'index')
    {
        // Set widgetStack for widgets
        if (empty($GLOBALS['widgetStack'])) {
            $GLOBALS['widgetStack'] = [];
        }

        $actionClass = false;

        if (!method_exists($this, 'action'.ucfirst($name))) {
            $actionClass = $this->getActionClassByName($name);
            if (!$actionClass) {
                throw new Exception('Action "'.$name.'" not found into '.get_class($this));
            }
        }

        $filters = method_exists($this, 'filters') ? $this->filters() : [];

        $result = $this->applyFilters($name, true, $filters, null);
        if ($result instanceof IResponse) {
            return $result;
        }

        if ($actionClass) {
            /** @var \Micro\mvc\Action $cl */
            $cl = new $actionClass();
            $view = $cl->run();
        } else {
            $view = $this->{'action'.ucfirst($name)}();
        }

        if (is_object($view)) {
            $view->module = get_class($this->module);
            $view->layout = $view->layout ?: $this->layout;
            $view->view = $view->view ?: $name;
            $view->path = get_called_class();
            $view = $view->render();
        }

        $response = (new ResponseInjector)->build() ?: new Response;
        $response->setBody($this->applyFilters($name, false, $filters, $view));

        return $response;
    }

    /**
     * Redirect user to path
     *
     * @access public
     *
     * @param string $path path to redirect
     * @param integer $status status for redirect
     *
     * @return bool|IResponse
     */
    public function redirect($path, $status = 301)
    {
        if (!$this->asWidget) {
            return new Response(['status' => $status, 'headers' => ['location' => $path]]);
        }

        return false;
    }
}
