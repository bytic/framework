<?php

namespace Nip;

class Bootstrap
{
    protected $_autoloader;

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
        $this->setupAutoloader();
        $this->setupErrorHandling();
        $this->setupURLConstants();
    }

    public function setup()
    {
        $this->setupConfig();
        $this->setupDatabase();
        $this->setupSession();
        $this->setupLocale();
        $this->setupRouting();
    }

    public function setupConfig()
    {

    }

    public function setupAutoloader()
    {
        $this->_autoloader = Nip_AutoLoader::instance();

        if (Nip_Staging::instance()->inProduction()) {
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

        if (Nip_Staging::instance()->isPublic()) {
            ini_set('display_errors', 0);
            error_reporting(0);
        } else {
            ini_set('html_errors', 1);
            ini_set('display_errors', 1);
            error_reporting(E_ALL ^ E_NOTICE);
        }

        $logger = Logger::instance();
        $adapter = new Logger_Adapter_Console();
        $logger->setAdapter($adapter);

        set_error_handler(array($logger, "errorHandler"), E_ALL ^ E_NOTICE);
    }

    protected function determineBaseURL()
    {
        $stage = Nip_Staging::instance()->getStage();

        $projectDirectoryParser = new Nip_Request_ProjectDirectory();
        $projectDirectoryParser->setScriptName(Nip_Request::instance()->getHTTP()->determineScriptNameByFilePath(ROOT_PATH));

        $baseURL = $stage->getHTTP() . $stage->getHost() . $projectDirectoryParser->determine();
        define('BASE_URL', $baseURL);
    }

    public function setupDatabase()
    {
        $stageConfig = Nip_Staging::instance()->getStage()->getConfig();
        Nip_DB_Wrapper::instance($stageConfig->DB->adapter,
            $stageConfig->DB->prefix)
            ->connect($stageConfig->DB->host, $stageConfig->DB->user,
                $stageConfig->DB->password, $stageConfig->DB->name);
    }

    public function setupSession()
    {
        $domain = Nip_Request::instance()->getHttp()->getRootDomain();

        if (!ini_get('session.auto_start') || (strtolower(ini_get('session.auto_start'))
                == 'off')
        ) {
            if ($domain !== 'localhost') {
                ini_set('session.cookie_domain',
                    '.' . Nip_Request::instance()->getHttp()->getRootDomain());
            }
            Session::instance()->setLifetime(Nip_Config::instance()->SESSION->lifetime);
        } else {

        }

        if ($domain != 'localhost') {
            Nip_Cookie_Jar::instance()->setDefaults(
                array('domain' => '.' . $domain)
            );
        }
    }

    public function setupLocale()
    {

    }

    public function setupRouting()
    {
        $router = $this->initRouter();
        Nip_FrontController::instance()->setRouter($router);
    }

    public function initRouter()
    {
    }

    public function dispatch()
    {
        $fc = Nip_FrontController::instance();
        try {
            ob_start();
            $this->preDispatch();
            $fc->getRequestURI();

            $this->preRouting();
            $params = $fc->routeURI();
            $this->postRouting();

            $fc->dispatch($params);
            ob_end_flush();
            $this->postDispatch();
        } catch (Nip_PHPException $e) {
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
}