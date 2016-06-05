<?php /** Micro */

namespace Micro;

use Micro\Base\Dispatcher;
use Micro\Base\Exception;
use Micro\Base\FatalError;
use Micro\Base\IDispatcher;
use Micro\Base\IInjector;
use Micro\Cli\Console;
use Micro\Cli\Consoles\DefaultConsoleCommand;
use Micro\Mvc\Controllers\IController;
use Micro\Resolver\IResolver;
use Micro\Web\IOutput;
use Micro\Web\IRequest;
use Micro\Web\IResponse;
use Micro\Web\Response;

/**
 * Micro class file.
 *
 * Base class for initialize MicroPHP, used as bootstrap framework.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/lugnsk/micro
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package micro
 * @version 1.0
 * @since 1.0
 */
class Micro
{
    /** @const string VERSION Version framework */
    const VERSION = '1.1';

    /** @var IInjector $injector */
    protected $injector;
    /** @var string $appDir */
    protected $appDir;
    /** @var string $webDir */
    protected $webDir;

    /** @var bool $loaded Micro loaded flag */
    private $loaded;
    /** @var bool $debug Debug-mode flag */
    private $debug = true;
    /** @var string $environment Application environment */
    private $environment = 'devel';
    /** @var float $startTime Time of start framework */
    private $startTime;


    /**
     * Initialize application
     *
     * @access public
     *
     * @param string $environment Application environment: devel , production , test, other
     * @param bool $debug Debug-mode flag
     *
     * @result void
     */
    public function __construct($environment = 'devel', $debug = true)
    {
        $this->webDir = getenv('DOCUMENT_ROOT');
        $this->environment = (string)$environment;
        $this->debug = (bool)$debug;
        $this->loaded = false;

        ini_set('display_errors', (integer)$this->debug);
        ini_set('log_errors', (integer)$this->debug);

        FatalError::register();

        if ($this->debug) {
            ini_set('error_reporting', -1);
            $this->startTime = microtime(true);
        }
    }

    /**
     * Clone application
     *
     * @access public
     *
     * @return void
     */
    public function __clone()
    {
        if ($this->debug) {
            $this->startTime = microtime(true);
        }

        $this->loaded = false;
    }

    /**
     * Running application
     *
     * @access public
     *
     * @param IRequest $request Request object
     *
     * @return \Micro\Web\IOutput|\Micro\Web\IResponse
     * @throws \Exception
     * @throws \Micro\Base\Exception
     */
    public function run(IRequest $request)
    {
        try {
            return $this->doRun($request);
        } catch (\Exception $e) {
            if ($this->debug) {
                if (($dispatcher = $this->injector->get('dispatcher')) && $dispatcher instanceof IDispatcher) {
                    $dispatcher->signal('kernel.exception', ['exception' => $e]);
                }

                throw $e;
            }

            return $this->doException($e);
        }
    }

    /**
     * Starting ...
     *
     * @access private
     *
     * @param IRequest $request
     *
     * @return \Micro\Web\IResponse|\Micro\Web\IOutput
     * @throws \Micro\Base\Exception
     */
    private function doRun(IRequest $request)
    {
        if (!$this->loaded) {
            $this->initialize();

            $this->addListener('kernel.kill', function() {
                /** @var IRequest $request */
                $request = $this->injector->get('request');

                if ($this->isDebug() && !$request->isCli() && !$request->isAjax()) {
                    echo '<div class="debug_timer">', (microtime(true) - $this->getStartTime()), '</div>';
                }

                if (false === $this->loaded) {
                    return;
                }

                $this->loaded = false;
            });
        }

        $this->injector->addRequirement('request', $request);

        if (($output = $this->sendSignal('kernel.request', [])) instanceof IResponse) {
            return $output;
        }

        /** @var IResolver $resolver */
        $resolver = $this->getResolver();
        if (($output = $this->sendSignal('kernel.router', ['resolver' => $resolver])) instanceof IResponse) {
            return $output;
        }

        /** @var IController|Console $app */
        $app = $resolver->getApplication();
        if (($output = $this->sendSignal('kernel.controller', ['application' => $app])) instanceof IResponse) {
            return $output;
        }

        $output = $app->action((string)$resolver->getAction());
        if (!$output instanceof IOutput) {
            $response = $this->injector->get('response') ?: new Response;
            $response->setBody((string)$output);
            $output = $response;
        }

        $this->sendSignal('kernel.response', ['output' => $output]);

        return $output;
    }

    /**
     * Initialization
     *
     * @access protected
     * @return void
     */
    protected function initialize()
    {
        $class = $this->getInjectorClass();
        if (!$class || !class_exists($class)) {
            $class = '\Micro\Base\Injector';
        }

        /** @var IInjector $inject */
        $inject = new $class($this->getConfig());
        $inject->addRequirement('kernel', $this);

        if (!$inject->check('dispatcher') || !($inject->get('dispatcher') instanceof IDispatcher)) {
            $inject->addRequirement('dispatcher', new Dispatcher);
        }

        /** @var IDispatcher $dispatcher */
        $dispatcher = $inject->get('dispatcher');
        $dispatcher->signal('kernel.boot', ['injector' => $inject]);

        $this->loaded = true;
    }

    /**
     * Get full class name
     * @return string
     */
    protected function getInjectorClass()
    {
        return '';
    }

    /**
     * Default config path
     *
     * @return string
     */
    protected function getConfig()
    {
        return $this->getAppDir().'/configs/index.php';
    }

    /**
     * Get application directory
     *
     * @return string
     */
    public function getAppDir()
    {
        if (!$this->appDir) {
            $this->appDir = realpath(dirname((new \ReflectionObject($this))->getFileName()));
        }

        return $this->appDir;
    }

    /**
     * Add listener on event
     *
     * @access public
     *
     * @param string $listener listener name
     * @param \Closure $event ['Object', 'method'] or callable
     * @param int|null $prior priority
     *
     * @return boolean|null
     */
    protected function addListener($listener, $event, $prior = null)
    {
        if (!is_string($listener) || !$this->injector) {
            return false;
        }

        if (($dispatcher = $this->injector->get('dispatcher')) && $dispatcher instanceof IDispatcher) {
            return $dispatcher->addListener($listener, $event, $prior);
        }

        return null;
    }

    /**
     * Get status of debug
     *
     * @access public
     *
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * Get start time
     *
     * @access public
     *
     * @return double
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Send signal to dispatcher
     *
     * @param string $signal
     * @param $params
     * @return mixed
     */
    protected function sendSignal($signal, $params)
    {
        if (($dispatcher = $this->injector->get('dispatcher')) && $dispatcher instanceof IDispatcher) {
            return $dispatcher->signal($signal, $params);
        }

        return null;
    }

    /**
     * Get resolver
     *
     * @access protected
     *
     * @return IResolver
     * @throws \Micro\Base\Exception
     */
    protected function getResolver()
    {
        /** @var IRequest $request */
        $request = $this->injector->get('request');

        if ($request->isCli()) {
            $resolver = $this->injector->get('consoleResolver') ?: '\Micro\Resolver\ConsoleResolver';
        } else {
            $resolver = $this->injector->get('resolver') ?: '\Micro\Resolver\HMVCResolver';
        }

        if (is_string($resolver) && is_subclass_of($resolver, '\Micro\Resolver\IResolver')) {
            $resolver = new $resolver();
        }

        if (!$resolver instanceof IResolver) {
            throw new Exception('Resolver is not implement an IResolver');
        }

        return $resolver;
    }

    /**
     * Do exception
     *
     * @access private
     *
     * @param \Exception $e Exception
     *
     * @return \Micro\Web\IOutput|\Micro\Web\IResponse
     * @throws \Micro\Base\Exception
     */
    private function doException(\Exception $e)
    {
        /** @var IRequest $request */
        $request = $this->injector->get('request');

        $output = $request->isCli() ? new DefaultConsoleCommand([]) : new Response();

        if ($request->isCli()) {
            $output->data = '"Error #'.$e->getCode().' - '.$e->getMessage().'"';
            $output->execute();

            return $output;
        }
        if (!$this->injector->get('errorController') || !$this->injector->get('errorAction')) {
            $output->setBody('Option `errorController` or `errorAction` not configured');

            return $output;
        }

        $request->setPost('error', $e);
        $controller = $this->injector->get('errorController');

        /** @var \Micro\Mvc\Controllers\IController $result */
        $result = new $controller(false);
        $result = $result->action($this->injector->get('errorAction'));
        if ($result instanceof IOutput) {
            return $result;
        }

        $output->setBody((string)$result);

        return $output;
    }

    /**
     * Get web root directory
     *
     * @return string
     */
    public function getWebDir()
    {
        return $this->webDir;
    }

    /**
     * Terminate application
     *
     * @access public
     *
     * @return void
     */
    public function terminate()
    {
        if (($dispatcher = $this->injector->get('dispatcher')) && $dispatcher instanceof IDispatcher) {
            $dispatcher->signal('kernel.kill', []);
        }
    }

    /**
     * Get character set
     *
     * @access public
     *
     * @return string
     */
    public function getCharset()
    {
        return 'UTF-8';
    }

    /**
     * Get logs directory
     *
     * @return string
     */
    public function getLogDir()
    {
        return $this->getAppDir().'/logs';
    }

    /**
     * Get cache directory
     *
     * @return string
     */
    public function getCacheDir()
    {
        return $this->getAppDir().'/cache/'.$this->getEnvironment();
    }

    /**
     * Get environment name
     *
     * @access public
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }
}
