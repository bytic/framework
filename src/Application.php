<?php

namespace Nip;

use Nip\Container\Container;
use Nip\DebugBar\StandardDebugBar;
use Nip\Logger\Manager as LoggerManager;
use Nip\Staging\Stage;

class Application
{
    /**
     * @var AutoLoader
     */
    protected $_autoloader = null;

    protected $_frontController = null;

    protected $_container = null;

    /**
     * @var null|Request
     */
    protected $_request = null;

    /**
     * @var null|LoggerManager
     */
    protected $_logger = null;

    /**
     * @var null|Session
     */
    protected $_sessionManager = null;

    protected $_debugBar = null;

    /**
     * @var Staging
     */
    protected $_staging = null;

    /**
     * @var Stage
     */
    protected $_stage = null;

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
        $this->setupContainer();
        $this->setupRequest();
        $this->setupStaging();
        $this->setupAutoloader();
        $this->setupErrorHandling();
        $this->setupURLConstants();
    }

    public function includeVendorAutoload()
    {
    }

    public function setupContainer()
    {
        $this->getContainer()->add('mvc.modules', 'Nip\Mvc\Modules', true);
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        if ($this->_container == null) {
            $this->initContainer();
        }

        return $this->_container;
    }

    /**
     * @param $container
     */
    public function setContainer($container)
    {
        $this->_container = $container;
    }

    public function initContainer()
    {
        $this->_container = $this->newContainer();
        Container::setInstance($this->_container);
    }

    /**
     * @return Container
     */
    public function newContainer()
    {
        return new Container();
    }

    /**
     *
     */
    public function setupRequest()
    {
        $request = $this->getRequest();
        $this->getFrontController()->setRequest($request);
    }

    public function getRequest()
    {
        if ($this->_request == null) {
            $this->initRequest();
        }

        return $this->_request;
    }

    public function initRequest()
    {
        $request = Request::createFromGlobals();
        Request::instance($request);
        $this->_request = $request;

        return $this;
    }

    protected function getFrontController()
    {
        if ($this->_frontController === null) {
            $this->_frontController = $this->initFrontController();
        }

        return $this->_frontController;
    }

    protected function initFrontController()
    {
        $fc = FrontController::instance();
        $fc->setBootstrap($this);

        return $fc;
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
        if ($this->_staging == null) {
            $this->initStaging();
        }

        return $this->_staging;
    }

    public function initStaging()
    {
        $this->_staging = $this->newStaging();
    }

    public function newStaging()
    {
        return Staging::instance();
    }

    public function getStage()
    {
        if ($this->_stage == null) {
            $this->initStage();
        }

        return $this->_stage;
    }

    public function initStage()
    {
        $this->_stage = $this->getStaging()->getStage();
    }

    public function setupAutoloader()
    {
        $this->setupAutoloaderCache();
        $this->setupAutoloaderPaths();

        if ($this->getStage()->inTesting()) {
            $this->getAutoloader()->setRetry(true);
        }
    }

    public function setupAutoloaderCache()
    {
    }

    public function setupAutoloaderPaths()
    {
    }

    public function getAutoloader()
    {
        if ($this->_autoloader == null) {
            $this->initAutoloader();
        }

        return $this->_autoloader;
    }

    public function initAutoloader()
    {
        $this->_autoloader = AutoLoader::instance();
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
        if ($this->_logger == null) {
            $this->initLogger();
        }

        return $this->_logger;
    }

    /**
     * @param  LoggerManager $logger
     * @return $this
     */
    public function setLogger($logger)
    {
        $this->_logger = $logger;

        return $this;
    }

    /**
     * @return null
     */
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
        if ($this->_debugBar == null) {
            $this->initDebugBar();
        }

        return $this->_debugBar;
    }

    /**
     * @param null $debugBar
     */
    public function setDebugBar($debugBar)
    {
        $this->_debugBar = $debugBar;
    }

    /**
     * @return null
     */
    public function initDebugBar()
    {
        $this->setDebugBar($this->newDebugBar());
    }

    /**
     * @return null
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
    }

    public function setupConfig()
    {

    }

    public function setupDatabase()
    {
        $stageConfig = $this->getStage()->getConfig();

        $connection = new Database\Connection();

        $adapter = $connection->newAdapter($stageConfig->DB->adapter);
        $connection->setAdapter($adapter);

        if ($this->getDebugBar()->isEnabled()) {
            $this->getDebugBar()->initDatabaseAdapter($adapter);
        }
        $connection->connect(
            $stageConfig->DB->host,
            $stageConfig->DB->user,
            $stageConfig->DB->password,
            $stageConfig->DB->name);
        Container::getInstance()->set('database', $connection);
    }

    public function setupSession()
    {
        if ($this->getFrontController()->getRequest()->isCLI() == false) {
            $requestHTTP = $this->getFrontController()->getRequest()->getHttp();
            $domain = $requestHTTP->getRootDomain();
            $sessionManager = $this->getSession();

            if (!$sessionManager->isAutoStart()) {
                $sessionManager->setRootDomain($domain);
                $sessionManager->setLifetime(\Nip_Config::instance()->get('SESSION')->lifetime);
            }

            if ($domain != 'localhost') {
                Cookie\Jar::instance()->setDefaults(
                    array('domain' => '.'.$domain)
                );
            }
            $this->_sessionManager->init();
        }
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        if ($this->_sessionManager === null) {
            $this->initSession();
        }

        return $this->_sessionManager;
    }

    public function initSession()
    {
        $this->_sessionManager = $this->newSession();
    }

    public function newSession()
    {
        return new Session();
    }

    public function setupTranslation()
    {
        $translation = $this->initTranslation();
        $this->initLanguages($translation);
    }

    public function initTranslation()
    {
        $translator = $this->newTranslator();
        $translator->setRequest($this->getFrontController()->getRequest());

        Container::getInstance()->set('translator', $translator);

        return $translator;
    }

    /**
     * @return I18n\Translator
     */
    public function newTranslator()
    {
        return new I18n\Translator();
    }

    /**
     * @param $translation
     */
    public function initLanguages($translation)
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
            $this->getDebugBar()->getCollector('route')->setRouter($router);
        }
    }

    /**
     * @return Router\Router
     */
    public function newRouter()
    {
        return new Router\Router();
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
            $this->_logger->handleException($e);
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

}