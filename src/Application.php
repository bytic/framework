<?php

namespace Nip;

use Exception;
use Nip\Application\ApplicationInterface;
use Nip\Application\Bootstrap\CoreBootstrapersTrait;
use Nip\Application\Traits\BindPathsTrait;
use Nip\Application\Traits\EnviromentConfiguration;
use Nip\AutoLoader\AutoLoader;
use Nip\AutoLoader\AutoLoaderAwareTrait;
use Nip\AutoLoader\AutoLoaderServiceProvider;
use Nip\Container\Container;
use Nip\Container\ContainerAliasBindingsTrait;
use Nip\Container\ServiceProviders\ServiceProviderAwareTrait;
use Nip\Database\Manager as DatabaseManager;
use Nip\Dispatcher\DispatcherAwareTrait;
use Nip\Dispatcher\DispatcherServiceProvider;
use Nip\Http\Response\Response;
use Nip\Http\Response\ResponseFactory;
use Nip\Mail\MailServiceProvider;
use Nip\Mvc\MvcServiceProvider;
use Nip\Router\RouterAwareTrait;
use Nip\Router\RouterServiceProvider;
use Nip\Staging\StagingAwareTrait;
use Nip\Staging\StagingServiceProvider;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run as WhoopsRun;

/**
 * Class Application
 * @package Nip
 */
class Application implements ApplicationInterface
{
    use ContainerAliasBindingsTrait;
    use CoreBootstrapersTrait;
    use ServiceProviderAwareTrait;
    use BindPathsTrait;
    use EnviromentConfiguration;
    use AutoLoaderAwareTrait;
    use RouterAwareTrait;
    use DispatcherAwareTrait;
    use StagingAwareTrait;

    /**
     * The ByTIC framework version.
     *
     * @var string
     */
    const VERSION = '1.0.1';

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
     * @var null|Session
     */
    protected $sessionManager = null;

    /**
     * Create a new Illuminate application instance.
     *
     * @param  string|null $basePath
     *
     * @return void
     */
    public function __construct($basePath = null)
    {
        if ($basePath) {
            $this->setBasePath($basePath);
        }
    }

    public function run()
    {
        $this->bootstrap();
        $this->prepare();
        $this->setup();

        $request = $this->getRequest();
        $response = $this->handleRequest($request);

        $response = $this->filterResponse($response, $request);
        $response->send();
        $this->terminate($request, $response);
    }

    public function prepare()
    {
        $this->setupRequest();
        $this->setupAutoLoader();
        $this->setupURLConstants();
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

    public function setupURLConstants()
    {
        $this->determineBaseURL();
        define('CURRENT_URL', $this->getRequest()->getHttp()->getUri());
    }

    protected function determineBaseURL()
    {
        $stage = $this->getStaging()->getStage();
        $pathInfo = $this->getRequest()->getHttp()->getBaseUrl();

        $baseURL = $stage->getHTTP() . $stage->getHost() . $pathInfo;
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
        $this->setupDatabase();
        $this->setupSession();
        $this->setupTranslation();
        $this->setupLocale();
        $this->setupRouting();
        $this->boot();
    }

    public function setupDatabase()
    {
        $stageConfig = $this->getStaging()->getStage()->getConfig();
        $dbManager = new DatabaseManager();
        $dbManager->setBootstrap($this);

        $connection = $dbManager->newConnectionFromConfig($stageConfig->get('DB'));
        $this->share('database', $connection);

//        if ($this->getDebugBar()->isEnabled()) {
//            $adapter = $connection->getAdapter();
//            $this->getDebugBar()->initDatabaseAdapter($adapter);
//        }
    }

    public function setupSession()
    {
        if ($this->getRequest()->isCLI() == false) {
            $requestHTTP = $this->getRequest()->getHttp();
            $domain = $requestHTTP->getRootDomain();
            $sessionManager = $this->getSession();

            if (!$sessionManager->isAutoStart()) {
                $sessionManager->setRootDomain($domain);
                $sessionManager->setLifetime($this->get('config')->get('SESSION')->get('lifetime'));
            }

            if ($domain != 'localhost') {
                Cookie\Jar::instance()->setDefaults(
                    ['domain' => '.' . $domain]
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
//        if ($this->getDebugBar()->isEnabled()) {
//            /** @var RouteCollector $routeCollector */
//            $routeCollector = $this->getDebugBar()->getCollector('route');
//            $routeCollector->setRouter($router);
//        }
    }

    public function boot()
    {
        if ($this->isBooted()) {
            return;
        }

        $this->bootProviders();
        $this->booted = true;
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

    /** @noinspection PhpUnusedParameterInspection
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function filterResponse(Response $response, Request $request)
    {
        return $response;
    }

    public function terminate()
    {
    }

    /**
     * @return array
     */
    public function getConfiguredProviders()
    {
        return [
            AutoLoaderServiceProvider::class,
            MailServiceProvider::class,
            MvcServiceProvider::class,
            DispatcherServiceProvider::class,
            StagingServiceProvider::class,
            RouterServiceProvider::class,
        ];
    }

    /**
     * Determine if the application configuration is cached.
     *
     * @return bool
     */
    public function configurationIsCached()
    {
        return false;
//        return file_exists($this->getCachedConfigPath());
    }

    /**
     * Throw an HttpException with the given data.
     *
     * @param  int $code
     * @param  string $message
     * @param  array $headers
     * @return void
     *
     * @throws HttpException
     */
    public function abort($code, $message = '', array $headers = [])
    {
        if ($code == 404) {
            throw new NotFoundHttpException($message);
        }
        throw new HttpException($code, $message, null, $headers);
    }

    /**
     * @return I18n\Translator
     */
    public function getTranslator()
    {
        if (!$this->has('translator')) {
            $this->initTranslator();
        }

        return $this->get('translator');
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
        if ($this->getStaging()->getStage()->isPublic()) {
            $this->getDispatcher()->setErrorController();

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
     * @return Response
     */
    protected function getResponseFromRequest($request)
    {
        if ($request->hasMCA()) {
            $response = $this->dispatchRequest($request);
            ob_get_clean();

            return $response;
        }

        throw new NotFoundHttpException('No MCA in Request');
    }
}
