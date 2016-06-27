<?php

namespace Nip;

use Nip\Staging\Stage;

class Bootstrap
{
    protected $_autoloader = null;

    protected $_frontController = null;

    protected $_staging;

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
        $this->setupStaging();
        $this->setupAutoloader();
        $this->setupErrorHandling();
        $this->setupURLConstants();
    }

    public function setup()
    {
        $this->setupConfig();
        $this->setupDatabase();
        $this->setupRequest();
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

        if ($this->getStage()->isPublic()) {
            ini_set('display_errors', 0);
            error_reporting(0);
        } else {
            ini_set('html_errors', 1);
            ini_set('display_errors', 1);
            error_reporting(E_ALL ^ E_NOTICE);
        }

        $logger = new \Nip\Logger\Manager();
        $adapter = new \Nip\Logger\Adapter\Console();
        $logger->setAdapter($adapter);

        set_error_handler(array($logger, "errorHandler"), E_ALL ^ E_NOTICE);
    }

    protected function determineBaseURL()
    {
        $stage = $this->getStage();
        $pathInfo = $this->getFrontController()->getRequest()->getHttp()->getPathInfo();

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
        \Nip_DB_Wrapper::instance($stageConfig->DB->adapter,
            $stageConfig->DB->prefix)
            ->connect($stageConfig->DB->host, $stageConfig->DB->user,
                $stageConfig->DB->password, $stageConfig->DB->name);
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
        $router = $this->initRouter();
        $this->getFrontController()->setRouter($router);
    }

    public function initRouter()
    {
        return new Nip_Router();
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
        } catch (\Nip_PHPException $e) {
            $e->log();
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
        define('CURRENT_URL', $this->getFrontController()->getRequest()->getHttp()->getURI());
    }

}