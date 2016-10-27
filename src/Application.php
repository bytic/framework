<?php

namespace Nip;

use Exception;
use Nip\AutoLoader\AutoLoader;
use Nip\AutoLoader\AutoLoaderAwareTrait;
use Nip\AutoLoader\AutoLoaderServiceProvider;
use Nip\Config\ConfigAwareTrait;
use Nip\Container\Container;
use Nip\Container\ContainerAwareTrait;
use Nip\Database\Manager as DatabaseManager;
use Nip\DebugBar\DataCollector\RouteCollector;
use Nip\DebugBar\StandardDebugBar;
use Nip\Dispatcher\DispatcherAwareTrait;
use Nip\Dispatcher\DispatcherServiceProvider;
use Nip\Http\Response\Response;
use Nip\Http\Response\ResponseFactory;
use Nip\Logger\Manager as LoggerManager;
use Nip\Mail\MailServiceProvider;
use Nip\Mvc\MvcServiceProvider;
use Nip\Router\RouterAwareTrait;
use Nip\Router\RouterServiceProvider;
use Nip\Staging\StagingAwareTrait;
use Nip\Staging\StagingServiceProvider;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run as WhoopsRun;

/**
 * Class Application
 * @package Nip
 */
class Application
{
    use ContainerAwareTrait;
    use ConfigAwareTrait;
    use AutoLoaderAwareTrait;
    use RouterAwareTrait;
    use DispatcherAwareTrait;
    use StagingAwareTrait;

    /**
     * Indicates if the application has "booted".
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * @var null|Request
     */
    protected $request = null;

    /**
     * @var null|LoggerManager
     */
    protected $logger = null;

    /**
     * @var null|Session
     */
    protected $sessionManager = null;

    protected $debugBar = null;

    public function run()
    {
        $this->loadFiles();
        $this->prepare();
        $this->setup();

        $request = $this->getRequest();
        $response = $this->handleRequest($request);

        $response = $this->filterResponse($response, $request);
        $response->send();
        $this->terminate($request, $response);
    }

    public function loadFiles()
    {
    }

    public function prepare()
    {
        $this->includeVendorAutoload();
        $this->registerContainer();
        $this->registerServices();
        $this->setupRequest();
        $this->setupAutoLoader();
        $this->setupErrorHandling();
        $this->setupURLConstants();
    }

    public function includeVendorAutoload()
    {
    }

    public function registerContainer()
    {
        $this->getContainer()->singleton('kernel', $this);
    }

    public function registerServices()
    {
        $this->getContainer()->addServiceProvider(AutoLoaderServiceProvider::class);
        $this->getContainer()->addServiceProvider(MailServiceProvider::class);
        $this->getContainer()->addServiceProvider(MvcServiceProvider::class);
        $this->getContainer()->addServiceProvider(DispatcherServiceProvider::class);
        $this->getContainer()->addServiceProvider(StagingServiceProvider::class);
        $this->getContainer()->addServiceProvider(RouterServiceProvider::class);
    }

    public function setupRequest()
    {
    }

    public function setupAutoLoader()
    {
        AutoLoader::registerHandler($this->getAutoLoader());

        $this->setupAutoLoaderCache();
        $this->setupAutoLoaderPaths();

        if ($this->getStaging()->getStage()->inTesting()) {
            $this->getAutoLoader()->getClassMapLoader()->setRetry(true);
        }
    }

    public function setupAutoLoaderCache()
    {
    }

    public function setupAutoLoaderPaths()
    {
    }

    public function setupErrorHandling()
    {
        fix_input_quotes();

        $this->getLogger()->init();

        if ($this->getStaging()->getStage()->inTesting()) {
            $this->getDebugBar()->enable();
            $this->getDebugBar()->addMonolog($this->getLogger()->getMonolog());
        }
    }

    /**
     * @return LoggerManager|null
     */
    public function getLogger()
    {
        if ($this->logger == null) {
            $this->initLogger();
        }

        return $this->logger;
    }

    /**
     * @param  LoggerManager $logger
     * @return $this
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }

    public function initLogger()
    {
        $logger = $this->newLogger();
        $logger->setBootstrap($this);
        $this->setLogger($logger);
    }

    /**
     * @return LoggerManager
     */
    public function newLogger()
    {
        $logger = new LoggerManager();

        return $logger;
    }

    /**
     * @return StandardDebugBar
     */
    public function getDebugBar()
    {
        if ($this->debugBar == null) {
            $this->initDebugBar();
        }

        return $this->debugBar;
    }

    /**
     * @param null $debugBar
     */
    public function setDebugBar($debugBar)
    {
        $this->debugBar = $debugBar;
    }

    public function initDebugBar()
    {
        $this->setDebugBar($this->newDebugBar());
    }

    /**
     * @return StandardDebugBar
     */
    public function newDebugBar()
    {
        $debugBar = new StandardDebugBar();

        return $debugBar;
    }

    public function setupURLConstants()
    {
        $this->determineBaseURL();
        define('CURRENT_URL', $this->getRequest()->getHttp()->getUri());
    }

    protected function determineBaseURL()
    {
        $stage = $this->getStage();
        $pathInfo = $this->getRequest()->getHttp()->getBaseUrl();

        $baseURL = $stage->getHTTP().$stage->getHost().$pathInfo;
        define('BASE_URL', $baseURL);
    }

    /**
     * @return Request|null
     */
    public function getRequest()
    {
        if ($this->request == null) {
            $this->initRequest();
        }

        return $this->request;
    }

    /**
     * @return $this
     */
    public function initRequest()
    {
        $request = Request::createFromGlobals();
        Request::instance($request);
        $this->request = $request;

        return $this;
    }

    public function setup()
    {
        $this->setupConfig();
        $this->setupDatabase();
        $this->setupSession();
        $this->setupTranslation();
        $this->setupLocale();
        $this->setupRouting();
        $this->boot();
    }

    public function setupConfig()
    {
        $this->registerContainerConfig();
    }

    public function setupDatabase()
    {
        $stageConfig = $this->getStage()->getConfig();
        $dbManager = new DatabaseManager();
        $dbManager->setBootstrap($this);

        $connection = $dbManager->newConnectionFromConfig($stageConfig->get('DB'));
        $this->getContainer()->set('database', $connection);

        if ($this->getDebugBar()->isEnabled()) {
            $adapter = $connection->getAdapter();
            $this->getDebugBar()->initDatabaseAdapter($adapter);
        }
    }

    public function setupSession()
    {
        if ($this->getRequest()->isCLI() == false) {
            $requestHTTP = $this->getRequest()->getHttp();
            $domain = $requestHTTP->getRootDomain();
            $sessionManager = $this->getSession();

            if (!$sessionManager->isAutoStart()) {
                $sessionManager->setRootDomain($domain);
                $sessionManager->setLifetime($this->getContainer()->get('config')->get('SESSION')->get('lifetime'));
            }

            if ($domain != 'localhost') {
                Cookie\Jar::instance()->setDefaults(
                    ['domain' => '.'.$domain]
                );
            }
            $this->sessionManager->init();
        }
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        if ($this->sessionManager === null) {
            $this->initSession();
        }

        return $this->sessionManager;
    }

    public function initSession()
    {
        $this->sessionManager = $this->newSession();
    }

    /**
     * @return Session
     */
    public function newSession()
    {
        return new Session();
    }

    public function setupTranslation()
    {
        $this->initLanguages();
    }

    public function initLanguages()
    {
    }

    public function setupLocale()
    {
    }

    public function setupRouting()
    {
        $router = $this->getRouter();
        $router->setRequest($this->getRequest());
        if ($this->getDebugBar()->isEnabled()) {
            /** @var RouteCollector $routeCollector */
            $routeCollector = $this->getDebugBar()->getCollector('route');
            $routeCollector->setRouter($router);
        }
    }

    public function boot()
    {
        if ($this->isBooted()) {
            return;
        }

        $this->getContainer()->getProviders()->boot();
    }

    /**
     * Determine if the application has booted.
     *
     * @return bool
     */
    public function isBooted()
    {
        return $this->booted;
    }

    /**
     * @param null|Request $request
     * @return Response
     */
    public function handleRequest($request = null)
    {
        $request = $request ? $request : $this->getRequest();
        try {
            ob_start();
            $this->preHandleRequest();

            $this->preRouting();
            $this->route($request);
            $this->postRouting();
            return $this->getResponseFromRequest($request);
        } catch (Exception $e) {
            return $this->handleException($request, $e);
        }
    }

    public function preHandleRequest()
    {
    }

    public function preRouting()
    {
    }

    public function postRouting()
    {
    }

    /**
     * @param Request $request
     * @return Response
     */
    protected function getResponseFromRequest($request)
    {
        $this->dispatchRequest($request);
        $content = ob_get_clean();

        return ResponseFactory::make($content);
    }

    /**
     * @param Exception $e
     * @param Request $request
     * @return Response
     */
    protected function handleException(Request $request, Exception $e)
    {
        $this->reportException($e);

        return $this->renderException($request, $e);
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param  Exception $e
     * @return void
     */
    protected function reportException(Exception $e)
    {
        $this->getLogger()->handleException($e);
    }

    /**
     * @param Request $request
     * @param Exception $e
     * @return Response
     */
    protected function renderException(Request $request, Exception $e)
    {
        if ($this->getStage()->isPublic()) {
            $this->getDispatcher()->setErrorControler();

            return $this->getResponseFromRequest($request);
        } else {
            $whoops = new WhoopsRun;
            $whoops->allowQuit(false);
            $whoops->writeToOutput(false);
            $whoops->pushHandler(new PrettyPageHandler());

            return ResponseFactory::make($whoops->handleException($e));
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function filterResponse(Response $response, Request $request)
    {
        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function terminate(Request $request, Response $response)
    {
    }

    /**
     * @return I18n\Translator
     */
    public function getTranslator()
    {
        if (!$this->getContainer()->has('translator')) {
            $this->initTranslator();
        }

        return $this->getContainer()->get('translator');
    }

    public function initTranslator()
    {
        $translator = $this->newTranslator();
        $translator->setRequest($this->getRequest());

        Container::getInstance()->set('translator', $translator);
    }

    /**
     * @return I18n\Translator
     */
    public function newTranslator()
    {
        return new I18n\Translator();
    }

    /**
     * @return string
     */
    public function getRootNamespace()
    {
        return 'App\\';
    }
}
