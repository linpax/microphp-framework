<?php /** Micro */

namespace Micro;

use Micro\Base\Dispatcher;
use Micro\Base\DispatcherInjector;
use Micro\Base\Exception;
use Micro\Base\FatalError;
use Micro\Base\IInjector;
use Micro\Base\Injector;
use Micro\Cli\Console;
use Micro\Cli\Consoles\DefaultConsoleCommand;
use Micro\Mvc\Controllers\IController;
use Micro\Resolver\ConsoleResolverInjector;
use Micro\Resolver\IResolver;
use Micro\Resolver\ResolverInjector;
use Micro\Web\ResponseInjector;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


/**
 * Micro class file.
 *
 * Base class for initialize MicroPHP, used as bootstrap framework.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/lugnsk/micro
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @version 1.0
 * @since 1.0
 */
class Micro
{
    /** @const string VERSION Version framework */
    const VERSION = '1.1';

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
     * @param ServerRequestInterface $request Request object
     *
     * @return \Micro\Web\IOutput|ResponseInterface
     * @throws \Exception
     * @throws \Micro\Base\Exception
     */
    public function run(ServerRequestInterface $request)
    {
        try {
            return $this->doRun($request);
        } catch (\Exception $e) {
            if ($this->debug) {
                (new DispatcherInjector)->build()->signal('kernel.exception', ['exception' => $e]);

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
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface|\Micro\Web\IOutput
     * @throws \Micro\Base\Exception|\RuntimeException|\InvalidArgumentException
     */
    private function doRun(ServerRequestInterface $request)
    {
        $params = $request->getServerParams();
        $isAjax = strtolower(
                filter_var(!empty($params['HTTP_X_REQUESTED_WITH']) ? $params['HTTP_X_REQUESTED_WITH'] : null)
            ) === 'xmlhttprequest';

        if (!$this->loaded) {
            $this->initialize($request);

            $this->addListener('kernel.kill', function () use ($isAjax) {
                if ($this->isDebug() && !$this->isCli() && !$isAjax) {
                    echo '<div class="debug_timer">', (microtime(true) - $this->getStartTime()), '</div>';
                }

                if (false === $this->loaded) {
                    return;
                }

                $this->loaded = false;
            });
        }

        if (($output = $this->sendSignal('kernel.request', [])) instanceof ResponseInterface) {
            return $output;
        }

        /** @var IResolver $resolver */
        $resolver = $this->getResolver();
        if (($output = $this->sendSignal('kernel.router', ['resolver' => $resolver])) instanceof ResponseInterface) {
            return $output;
        }

        /** @var IController|Console $app */
        $app = $resolver->getApplication();
        if (($output = $this->sendSignal('kernel.controller', ['application' => $app])) instanceof ResponseInterface) {
            return $output;
        }

        $output = $app->action((string)$resolver->getAction());
        if (!($output instanceof ResponseInterface)) {
            $response = (new ResponseInjector)->build();
            $stream = $response->getBody();
            $stream->write((string)$output);
            $output = $response->withBody($stream);
        }

        $this->sendSignal('kernel.response', ['output' => $output]);

        return $output;
    }

    /**
     * Initialization
     *
     * @access protected
     * @param ServerRequestInterface $request
     * @return void
     * @throws Exception
     */
    protected function initialize(ServerRequestInterface $request)
    {
        $class = $this->getInjectorClass();
        if (!$class || !class_exists($class)) {
            $class = '\Micro\Base\Injector';
        }

        /** @var IInjector $inject */
        $inject = new $class($this->getConfig());
        $inject->addRequirement('kernel', $this);
        $inject->addRequirement('request', $request);

        $dispatcherInjector = new DispatcherInjector;
        try {
            $dispatcher = $dispatcherInjector->build();
        } catch (Exception $e) {
            $dispatcher = new Dispatcher;
            $dispatcherInjector->addRequirement('dispatcher', $dispatcher);
        }
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
     * @throws Exception
     */
    protected function addListener($listener, $event, $prior = null)
    {
        if (!is_string($listener)) {
            return false;
        }

        return (new DispatcherInjector)->build()->addListener($listener, $event, $prior);
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

    public function isCli()
    {
        return PHP_SAPI === 'cli';
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
     * @throws Exception
     */
    protected function sendSignal($signal, $params)
    {
        return (new DispatcherInjector)->build()->signal($signal, $params);
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
        try {
            if ($this->isCli()) {
                $resolver = (new ConsoleResolverInjector)->build();
            } else {
                $resolver = (new ResolverInjector)->build();
            }
        } catch (Exception $e) {
            $class = $this->isCli() ? '\Micro\Resolver\ConsoleResolver' : '\Micro\Resolver\HMVCResolver';
            $resolver = new $class;
        }

        if (is_string($resolver) && is_subclass_of($resolver, '\Micro\Resolver\IResolver')) {
            $resolver = new $resolver();
        }

        if (!($resolver instanceof IResolver)) {
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
     * @return \Micro\Web\IOutput|ResponseInterface
     * @throws \Micro\Base\Exception
     */
    private function doException(\Exception $e)
    {
        $output = $this->isCli() ? new DefaultConsoleCommand([]) : (new ResponseInjector)->build();

        if ($this->isCli()) { // Render CLI error
            $output->data = '"Error #'.$e->getCode().' - '.$e->getMessage().'"';
            $output->execute();

            return $output;
        }

        $errorController = (new Injector)->param('errorController');
        $errorAction = (new Injector)->param('errorAction');

        if (!$errorController || !$errorAction) { // render SAPI error not configured
            $stream = $output->getBody();
            $stream->write('Option `errorController` or `errorAction` not configured');

            return $output->withBody($stream);
        }

        // Render SAPI error
        $_POST['error'] = $e;

        $controller = $errorController;

        /** @var \Micro\Mvc\Controllers\IController $result */
        $result = new $controller(false);
        $result = $result->action($errorAction);

        if ($result instanceof ResponseInterface) {
            return $result;
        }

        $stream = $output->getBody();
        $stream->write((string)$result);

        return $output->withBody($stream);
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
        try {
            (new DispatcherInjector)->build()->signal('kernel.kill', []);
        } catch (Exception $e) {
            (new Dispatcher)->signal('kernel.kill', []);
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

    public function send(ResponseInterface $response)
    {
        header(
            'HTTP/' . $response->getProtocolVersion() . ' ' .
            $response->getStatusCode() . ' ' .
            $response->getReasonPhrase()
        );

        foreach ($response->getHeaders() as $header => $values) {
            header($header . ': ' . implode(', ', $values));
        }

        printf($response->getBody());
    }
}
