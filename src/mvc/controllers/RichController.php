<?php /** MicroRichController */

namespace Micro\Mvc\Controllers;

use Micro\Base\Exception;
use Micro\Web\RequestInjector;
use Psr\Http\Message\ServerRequestInterface;


/**
 * Class RichController
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
abstract class RichController extends Controller
{
    /** @var string $format Format for response */
    public $format = 'application/json';

    /** @var string $methodType */
    protected $methodType = 'get';


    /**
     * Construct RICH controller
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
        parent::__construct($modules);

        /** @var ServerRequestInterface $request */
        $request = (new RequestInjector)->build();

        $this->methodType = $request->getMethod() ?: 'GET';
    }

    /**
     * @inheritdoc
     * @throws \InvalidArgumentException
     */
    public function action($name = 'index')
    {
        $actionClass = false;

        // check action exists
        if (!method_exists($this, 'action'.ucfirst($name)) && !$actionClass = $this->getActionClassByName($name)) {
            return $this->response->withStatus(500, 'Action `'.$name.'` not found into '.get_class($this));
        }

        $types = $this->actionsTypes();

        if (!empty($types[$name]) && $this->methodType !== $types[$name]) {
            return $this->response->withStatus(500,
                'Action `'.$name.'` not run with method `'.$this->methodType.'` into '.get_class($this)
            );
        }

        $filters = method_exists($this, 'filters') ? $this->filters() : [];

        $this->applyFilters($name, true, $filters, null);

        if ($actionClass) {
            /** @var \Micro\Mvc\Action $cl */
            $cl = new $actionClass();
            $view = $cl->run();
        } else {
            $view = $this->{'action'.ucfirst($name)}();
        }

        $this->response = $this->response->withHeader('Content-Type', $this->format);

        $contentType = $this->response->getHeader('Content-Type') ?: 'text/html';
        if ($contentType !== $this->format) {
            $this->response = $this->response->withHeader('Content-Type', $this->format);
        }

        $stream = $this->response->getBody();
        $stream->write($this->switchContentType($this->applyFilters($name, false, $filters, $view)));

        return $this->response->withBody($stream);
    }

    /**
     * Define types for actions
     *
     * <code>
     *  // DELETE, GET, HEAD, OPTIONS, POST, PUT
     * public function actionsTypes() {
     *  return [
     *     'create' => 'POST',
     *     'read'   => 'GET',
     *     'update' => 'UPDATE'
     *     'delete' => 'DELETE'
     *  ];
     * }
     * </code>
     *
     * @access public
     *
     * @return array
     * @abstract
     */
    abstract public function actionsTypes();

    /**
     * Switch content type
     *
     * @access protected
     *
     * @param null|string $data Any content
     *
     * @return string
     */
    protected function switchContentType($data)
    {
        switch ($this->format) {
            case 'application/json':
                $data = json_encode(is_object($data) ? (array)$data : $data);
                break;

            case 'application/xml':
                $data = is_object($data) ? (string)$data : $data;
                break;

            default:
                $data = (string)$data;
        }

        return $data;
    }
}
