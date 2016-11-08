<?php /** MicroController */

namespace Micro\Mvc\Controllers;

use Micro\Base\Exception;
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
                throw new Exception('Action `' . $name . '` not found into ' . get_class($this));
            }
        }

        $filters = method_exists($this, 'filters') ? $this->filters() : [];
        $result = $this->applyFilters($name, true, $filters, null);

        if ($result instanceof ResponseInterface) {
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

        $response = (new ResponseInjector)->build();
        if (!$response) {
            throw new Exception('Component `response` not configured');
        }
        $stream = $response->getBody();
        $stream->write($this->applyFilters($name, false, $filters, $view));

        return $response->withBody($stream);
    }

    /**
     * Redirect user to path
     *
     * @access public
     *
     * @param string $path path to redirect
     * @param integer $status status for redirect
     *
     * @return bool|ResponseInterface
     * @throws Exception|\InvalidArgumentException
     */
    public function redirect($path, $status = 301)
    {
        if (!$this->asWidget) {
            /** @var ResponseInterface $response */
            $response = (new ResponseInjector)->build();
            if (!$response) {
                throw new Exception('Component `response` not configured');
            }

            $response = $response->withStatus($status);
            $response = $response->getHeaderLine('Location: ' . $path);

            return $response;
        }

        return false;
    }
}
