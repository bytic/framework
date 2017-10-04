<?php

namespace Nip\Router;

use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;
use Nip\Router\Generator\UrlGenerator;

/**
 * Class MailServiceProvider
 * @package Nip\Mail
 */
class RouterServiceProvider extends AbstractSignatureServiceProvider
{

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerRouter();

        $this->registerUrlGenerator();
    }

    protected function registerRouter()
    {
        $this->getContainer()->share('router', self::newRouter());
    }

    /**
     * @return Router
     */
    public static function newRouter()
    {
        return new Router();
    }

    /**
     * Register the URL generator service.
     *
     * @return void
     */
    protected function registerUrlGenerator()
    {
        $this->getContainer()->share('url', function () {
            $routes = app('router')->getRoutes();

            // The URL generator needs the route collection that exists on the router.
            // Keep in mind this is an object, so we're passing by references here
            // and all the registered routes will be available to the generator.
            app()->share('routes', $routes);

            $url = new UrlGenerator(
                $routes,
                request()
            );

            // If the route collection is "rebound", for example, when the routes stay
            // cached for the application, we will need to rebind the routes on the
            // URL generator instance so it has the latest version of the routes.
//            $app->rebinding('routes', function ($app, $routes) {
//                $app['url']->setRoutes($routes);
//            });
            return $url;
        });
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['router', 'url'];
    }
}
