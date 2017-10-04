<?php

namespace Nip\Database;

use Nip\Application;
use Nip\Container\ServiceProviders\Providers\AbstractServiceProvider;
use Nip\Database\Connections\ConnectionFactory;

/**
 * Class Manager
 * @package Nip\Database
 */
class DatabaseServiceProvider extends AbstractServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConnectionServices();
    }

    /**
     * Register the primary database bindings.
     *
     * @return void
     */
    protected function registerConnectionServices()
    {
        // The connection factory is used to create the actual connection instances on
        // the database. We will inject the factory into the manager so that it may
        // make the connections while they are actually needed and not of before.
        $this->getContainer()->share('db.factory', ConnectionFactory::class);

        // The database manager is used to resolve various connections, since multiple
        // connections might be managed. It also implements the connection resolver
        // interface which may be used by other components requiring connections.
        $this->getContainer()->share('db', DatabaseManager::class)
            ->withArgument(Application::class)
            ->withArgument(ConnectionFactory::class);

        $this->getContainer()->share('db.connection', function () {
            return app('db')->connection();
        });
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['db', 'db.factory', 'db.connection'];
    }
}
