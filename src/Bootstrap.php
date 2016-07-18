<?php

namespace Nip;

use Nip\Container\Container;
use Nip\DebugBar\StandardDebugBar;
use Nip\Logger\Manager;
use Nip\Staging\Stage;

class Bootstrap
{
    protected $_autoloader = null;

    protected $_frontController = null;

    protected $_staging;

    /**
     * @var null|Manager
     */
    protected $_logger = null;

    protected $_sessionManager = null;

    protected $_debugBar = null;

    /**
     * @var Stage
     */
    protected $_stage;

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
        $this->setupRequest();
        $this->setupStaging();
        $this->setupAutoloader();
        $this->setupErrorHandling();
        $this->setupURLConstants();
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

    public function setupStaging()
    {
        $this->_staging = $this->initStaging();

        $this->_stage = $this->_staging->getStage();

        $this->getFrontController()->setStaging($this->_staging);
        $this->getFrontController()->setStage($this->_stage);
    }

    public function initStaging()
    {
        return Staging::instance();
    }

    public function getStage()
    {
        return $this->_stage;
    }

    public function setupAutoloader()
    {
        $this->_autoloader = AutoLoader::instance();

        if ($this->getStage()->inProduction()) {
            $this->_autoloader->setRetry(false);
        }

        $this->setupAutoloaderCache();
        $this->setupAutoloaderPaths();
    }

    public function setupAutoloaderCache()
    {
    }

    public function setupAutoloaderPaths()
    {
    }

    public function includeVendorAutoload()
    {
    }


    public function setupErrorHandling()
    {
        fix_input_quotes();

        $this->_logger = new Logger\Manager();
        $this->_logger->setBootstrap($this);
        $this->_logger->init();

        if ($this->getStage()->inTesting()) {
            $this->getDebugBar()->enable();
            $this->getDebugBar()->addMonolog($this->_logger->getMonolog());
        }
    }

    protected function determineBaseURL()
    {
        $stage = $this->getStage();
        $pathInfo = $this->getFrontController()->getRequest()->getHttp()->getBaseUrl();

        $baseURL = $stage->getHTTP() . $stage->getHost() . $pathInfo;
        define('BASE_URL', $baseURL);
    }

    public function setupRequest()
    {
        $request = $this->initRequest();
        $this->getFrontController()->setRequest($request);
    }

    public function initRequest()
    {
        $request = Request::createFromGlobals();
        return $request;
    }

    public function setupDatabase()
    {
        $stageConfig = $this->getStage()->getConfig();
        $connection = \Nip\Database\Connection::instance();
        $connection->setAdapterName($stageConfig->DB->adapter);
        $connection->connect(
            $stageConfig->DB->host,
            $stageConfig->DB->user,
            $stageConfig->DB->password,
            $stageConfig->DB->name);
        Container::getInstance()->set('database', $connection);
    }

    public function setupSession()
    {
        $this->_sessionManager = $this->initSession();
        $requestHTTP = $this->getFrontController()->getRequest()->getHttp();
        $domain = $requestHTTP->getRootDomain();


        if (!ini_get('session.auto_start') || (strtolower(ini_get('session.auto_start'))
                == 'off')
        ) {
            if ($domain !== 'localhost') {
                ini_set('session.cookie_domain',
                    '.' . $requestHTTP->getRootDomain());
            }
            $this->_sessionManager->setLifetime(\Nip_Config::instance()->SESSION->lifetime);
        } else {

        }

        if ($domain != 'localhost') {
            \Nip_Cookie_Jar::instance()->setDefaults(
                array('domain' => '.' . $domain)
            );
        }
        $this->_sessionManager->init();
    }

    public function initSession()
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
        $i18n = \Nip_I18n::instance();
        $i18n->setRequest($this->getFrontController()->getRequest());
        return $i18n;
    }

    public function initLanguages($translation)
    {
        return $translation;
    }

    public function setupLocale()
    {

    }

    public function setupRouting()
    {
        $router = $this->newRouter();
        $router->setRequest($this->getFrontController()->getRequest());
        $this->getFrontController()->setRouter($router);
    }

    /**
     * @return \Nip_Router
     */
    public function newRouter()
    {
        return new \Nip_Router();
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
            $this->postDispatch();
        } catch (\Exception $e) {
            $this->_logger->handleException($e);
        }
    }

    public function preDispatch()
    {
    }

    public function postDispatch()
    {

    }

    public function preRouting()
    {
    }

    public function postRouting()
    {
    }

    public function setupURLConstants()
    {
        $this->determineBaseURL();
        define('CURRENT_URL', $this->getFrontController()->getRequest()->getHttp()->getUri());
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

    /**
     * @param null $debugBar
     */
    public function setDebugBar($debugBar)
    {
        $this->_debugBar = $debugBar;
    }


}