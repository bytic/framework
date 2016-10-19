<?php

namespace Nip;

use Nip\Config\ConfigAwareTrait;
use Nip\Container\Container;
use Nip\Container\ContainerAwareTrait;
use Nip\Database\Manager as DatabaseManager;
use Nip\DebugBar\DataCollector\RouteCollector;
use Nip\DebugBar\StandardDebugBar;
use Nip\Logger\Manager as LoggerManager;
use Nip\Mail\MailServiceProvider;
use Nip\Mvc\MvcServiceProvider;
use Nip\Staging\Stage;

/**
 * Class Application
 * @package Nip
 */
class Application
{
    use ContainerAwareTrait;
    use ConfigAwareTrait;

    /**
     * Indicates if the application has "booted".
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * @var AutoLoader
     */
    protected $autoloader = null;

    protected $frontController = null;

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

    /**
     * @var Staging
     */
    protected $staging = null;

    /**
     * @var Stage
     */
    protected $stage = null;

    public function run()
    {
        $this->loadFiles();
        $this->prepare();
        $this->setup();
        $this->dispatch();
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
        $this->setupStaging();
        $this->setupAutoloader();
        $this->setupErrorHandling();
        $this->setupURLConstants();
    }

    public function includeVendorAutoload()
    {
    }

    public function registerContainer()
    {
    }

    public function registerServices()
    {
        $this->getContainer()->addServiceProvider(MailServiceProvider::class);
        $this->getContainer()->addServiceProvider(MvcServiceProvider::class);
    }

    /**
     *
     */
    public function setupRequest()
    {
        $request = $this->getRequest();
        $this->getFrontController()->setRequest($request);
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

    /**
     * @return FrontController|null
     */
    protected function getFrontController()
    {
        if ($this->frontController === null) {
            $this->initFrontController();
        }

        return $this->frontController;
    }

    /**
     * @param null $frontController
     */
    public function setFrontController($frontController)
    {
        $this->frontController = $frontController;
    }

    protected function initFrontController()
    {
        $fc = $this->newFrontController();
        $fc->setApplication($this);
        $this->setFrontController($fc);
    }

    /**
     * @return FrontController
     */
    public function newFrontController()
    {
        return FrontController::instance();
    }

    public function setupStaging()
    {
        $this->getFrontController()->setStaging($this->getStaging());
        $this->getFrontController()->setStage($this->getStage());
    }

    /**
     * @return Staging
     */
    public function getStaging()
    {
        if ($this->staging == null) {
            $this->initStaging();
        }

        return $this->staging;
    }

    public function initStaging()
    {
        $this->staging = $this->newStaging();
    }

    /**
     * @return Staging
     */
    public function newStaging()
    {
        return Staging::instance();
    }

    /**
     * @return Stage
     */
    public function getStage()
    {
        if ($this->stage == null) {
            $this->initStage();
        }

        return $this->stage;
    }

    public function initStage()
    {
        $this->stage = $this->getStaging()->getStage();
    }

    public function setupAutoloader()
    {
        AutoLoader::registerHandler($this->getAutoloader());

        $this->setupAutoloaderCache();
        $this->setupAutoloaderPaths();

        if ($this->getStage()->inTesting()) {
            $this->getAutoloader()->getClassMapLoader()->setRetry(true);
        }
    }

    /**
     * @return AutoLoader
     */
    public function getAutoloader()
    {
        if ($this->autoloader == null) {
            $this->initAutoloader();
        }

        return $this->autoloader;
    }

    /**
     * @param AutoLoader $autoloader
     */
    public function setAutoloader(AutoLoader $autoloader)
    {
        $this->autoloader = $autoloader;
    }

    public function initAutoloader()
    {
        $this->setAutoloader($this->newAutoloader());
    }

    /**
     * @return AutoLoader
     */
    public function newAutoloader()
    {
        return AutoLoader::instance();
    }

    public function setupAutoloaderCache()
    {
    }

    public function setupAutoloaderPaths()
    {
    }

    public function setupErrorHandling()
    {
        fix_input_quotes();

        $this->getLogger()->init();

        if ($this->getStage()->inTesting()) {
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
        define('CURRENT_URL', $this->getFrontController()->getRequest()->getHttp()->getUri());
    }

    protected function determineBaseURL()
    {
        $stage = $this->getStage();
        $pathInfo = $this->getFrontController()->getRequest()->getHttp()->getBaseUrl();

        $baseURL = $stage->getHTTP().$stage->getHost().$pathInfo;
        define('BASE_URL', $baseURL);
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
        if ($this->getFrontController()->getRequest()->isCLI() == false) {
            $requestHTTP = $this->getFrontController()->getRequest()->getHttp();
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
        $router = $this->newRouter();
        $router->setRequest($this->getFrontController()->getRequest());
        $this->getFrontController()->setRouter($router);
        if ($this->getDebugBar()->isEnabled()) {
            /** @var RouteCollector $routeCollector */
            $routeCollector = $this->getDebugBar()->getCollector('route');
            $routeCollector->setRouter($router);
        }
    }

    /**
     * @return Router\Router
     */
    public function newRouter()
    {
        return new Router\Router();
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

    public function dispatch()
    {
        try {
            ob_start();
            $this->preDispatch();

            $this->preRouting();
            $this->getFrontController()->route();
            $this->postRouting();

            $this->getFrontController()->dispatch();
            ob_end_flush();
        } catch (\Exception $e) {
            $this->logger->handleException($e);
        }
        $this->postDispatch();
    }

    public function preDispatch()
    {
    }

    public function preRouting()
    {
    }

    public function postRouting()
    {
    }

    public function postDispatch()
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
        $translator->setRequest($this->getFrontController()->getRequest());

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
