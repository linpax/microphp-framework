<?php

namespace Micro\base;

use Micro\Web\ResponseInjector;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Base class for applications
 *
 * Start point for application and sender system messages to dispatcher
 *
 * @package Micro\base
 */
abstract class Application implements ApplicationInterface
{
    /** @var KernelInterface $kernel */
    protected $kernel;

    /**
     * Application constructor.
     * @param KernelInterface $kernel
     */
    final public function __construct(KernelInterface $kernel = null)
    {
        $this->kernel = $kernel;

        if (!$this->kernel) {
            $this->kernel = new Kernel('devel', true);
        }

        // init injectors
        $this->kernel->loadInjectorsFromCache(); // TODO: load injectors
    }

    /**
     * Running application
     *
     * @param ServerRequestInterface $server
     * @return ResponseInterface
     * @throws \Exception
     */
    final public function run(ServerRequestInterface $server)
    {
        $this->kernel->initialize($server);

        FatalError::register(); // TODO: fix for FatalErrors

        try {
            return $this->doRun();
        } catch (\Exception $e) {
            if ($this->kernel->isDebug()) {
                (new DispatcherInjector)->build()->signal('kernel.exception', ['exception' => $e]);

                throw $e;
            }

            return $this->doException($e);
        }
    }

    /**
     * Real run application
     *
     * @return ResponseInterface
     */
    private function doRun()
    {
        $dispatcher = (new DispatcherInjector)->build();

        //покажем событие Request
        if (($response = $dispatcher->signal('kernel.request')) instanceof ResponseInterface) {
            return $response; // событие завершило очередь
        }

        $resolver = $this->getResolver();

        //покажем событие Route
        if (($response = $dispatcher->signal('kernel.route', ['resolver' => $resolver])) instanceof ResponseInterface) {
            return $response; // событие завершило очередь
        }

        $controller = $resolver->getApp();
        $action = $resolver->getAction();

        //покажем событие Controller
        if (($response = $dispatcher->signal('kernel.controller',
                ['controller' => $controller, 'action' => $action])) instanceof ResponseInterface
        ) {
            return $response; // событие завершило очередь
        }

        $response = $controller->action((string)$action);

        if (!($response instanceof ResponseInterface)) {
            $responser = (new ResponseInjector)->build();
            $stream = $responser->getBody();
            $stream->write((string)$response);

            $response = $responser->withBody($stream);
        }

        $dispatcher->signal('kernel.response', ['response' => $response]);

        return $response;
    }

    /**
     * Run application with error
     *
     * @param \Exception $error
     * @return ResponseInterface
     */
    abstract protected function doException(\Exception $error);

    /**
     * Send response to client
     *
     * @param ResponseInterface $response
     */
    public function send(ResponseInterface $response)
    {
        printf($response->getBody().PHP_EOL);
    }

    /**
     * Terminate application
     */
    public function terminate()
    {
        try {
            (new DispatcherInjector)->build()->signal('kernel.kill', []);
        } catch (Exception $e) {
            (new Dispatcher)->signal('kernel.kill', []);
        }
    }
}