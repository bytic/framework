<?php

namespace Nip\Http\Kernel;

use Exception;
use Nip\Application;
use Nip\Application\ApplicationInterface;
use Nip\Http\Response\Response;
use Nip\Http\Response\ResponseFactory;
use Nip\Request;
use Nip\Router\Router;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run as WhoopsRun;

/**
 * Class Kernel
 * @package Nip\Http\Kernel
 */
class Kernel implements KernelInterface
{
    /**
     * The application implementation.
     *
     * @var Application
     */
    protected $app;

    /**
     * The router instance.
     *
     * @var Router
     */
    protected $router;
    /**
     * The application's middleware stack.
     *
     * @var array
     */
    protected $middleware = [];
    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [];
    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [];

    /**
     * Create a new HTTP kernel instance.
     *
     * @param  ApplicationInterface $app
     * @param  Router $router
     */
    public function __construct(ApplicationInterface $app, Router $router)
    {
        $this->app = $app;
        $this->router = $router;
    }

    /**
     * Handle an incoming HTTP request.
     *
     * @param  SymfonyRequest $request
     * @param int $type
     * @param bool $catch
     * @return Response
     */
    public function handle(SymfonyRequest $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        try {
//            $request->enableHttpMethodParameterOverride();
            $response = $this->sendRequestThroughRouter($request);
        } catch (Exception $e) {
            $this->reportException($e);
            $response = $this->renderException($request, $e);
        } catch (Throwable $e) {
            $this->reportException($e = new FatalThrowableError($e));
            $response = $this->renderException($request, $e);
        }
        return $response;
    }

    /**
     * Send the given request through the middleware / router.
     *
     * @param  Request $request
     * @return Response
     */
    protected function sendRequestThroughRouter($request)
    {
        $this->app->share('request', $request);

//        Facade::clearResolvedInstance('request');

        $this->preHandleRequest();
        $this->preRouting();

        // check is valid request
        if ($this->isValidRequest($request)) {
            $this->route($request);
        } else {
            die('');
        }

        $this->postRouting();
    }

    public function preHandleRequest()
    {
    }

    public function preRouting()
    {
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function isValidRequest($request)
    {
        if ($request->isMalicious()) {
            return false;
        }

        return true;
    }

    public function postRouting()
    {
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
     * @param Response $response
     */
    public function terminate(Request $request, Response $response)
    {
        $this->terminateMiddleware($request, $response);
        $this->getApplication()->terminate();
    }

    /**
     * Call the terminate method on any terminable middleware.
     *
     * @param  Request  $request
     * @param  Response  $response
     * @return void
     */
    protected function terminateMiddleware($request, $response)
    {
    }


    /**
     * Get the application instance.
     *
     * @return Application
     */
    public function getApplication()
    {
        return $this->app;
    }
}
