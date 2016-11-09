<?php

namespace Micro\base;

use Micro\Web\RequestInjector;
use Psr\Http\Message\ServerRequestInterface;

class Kernel implements KernelInterface
{
    /** @const string VERSION Version framework */
    const VERSION = '2.0';

    /** @var string $appDir */
    protected $appDir;
    /** @var string $webDir */
    protected $webDir;

    /** @var bool $debug Debug-mode flag */
    private $debug = true;
    /** @var string $environment Application environment */
    private $environment = 'devel';
    /** @var float $startTime Time of start framework */
    private $startTime;

    /** @var bool $loaded Micro loaded flag */
    private $loaded;


    /**
     * Kernel constructor.
     * @param string $environment
     * @param bool $debug
     */
    public function __construct($environment, $debug = false)
    {
        $this->webDir = getenv('DOCUMENT_ROOT');
        $this->environment = (string)$environment;
        $this->debug = (bool)$debug;
        $this->loaded = false;

        ini_set('display_errors', (integer)$this->debug);
        ini_set('log_errors', (integer)$this->debug);

        if ($this->debug) {
            ini_set('error_reporting', -1);
            $this->startTime = microtime(true);
        }
    }

    /**
     * Clone kernel
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

    final public function loadInjectorsFromCache()
    {
        // load injectors
        $injectors = ['name' => 'value_object']; // TODO: fixme

        // insert injectors from cache
        $baseInjector = new Injector;
        foreach ($injectors as $name => $injector) {
            $baseInjector->addRequirement($name, $injector);
        }
    }

    public function initialize(ServerRequestInterface $request)
    {
        $inject = new Injector($this->getConfig());
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

        // Hack - killer application
        (new DispatcherInjector)->build()->addListener('kernel.kill', function() {
            $params = (new RequestInjector)->build()->getServerParams();
            $isAjax = strtolower(
                    filter_var(!empty($params['HTTP_X_REQUESTED_WITH']) ? $params['HTTP_X_REQUESTED_WITH'] : null)
                ) === 'xmlhttprequest';

            if ($this->isDebug() && !$this->isCli() && !$isAjax) {
                echo '<div class="debug_timer">', (microtime(true) - $this->getStartTime()), '</div>';
            }

            if (false === $this->loaded) {
                return;
            }

            $this->loaded = false;
        });

        $this->loaded = true;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->getAppDir().'/configs/index.php';
    }

    /**
     * @return mixed
     */
    public function getAppDir()
    {
        if (!$this->appDir) {
            $this->appDir = realpath(dirname((new \ReflectionObject($this))->getFileName()));
        }

        return $this->appDir;
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @return bool
     */
    public function isCli()
    {
        return PHP_SAPI === 'cli';
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return mixed
     */
    public function getWebDir()
    {
        return $this->webDir;
    }

    public function getCacheDir()
    {
        return $this->getAppDir().'/cache/'.$this->getEnvironment();
    }

    /**
     * @return mixed
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    public function getLogDir()
    {
        return $this->getAppDir().'/logs';
    }
}