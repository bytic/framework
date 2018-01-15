<?php

namespace Nip\DebugBar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Nip\Router\Route\AbstractRoute as Route;
use Nip\Router\RouterAwareTrait;

/**
 * Class RouteCollector.
 */
class RouteCollector extends DataCollector implements Renderable
{
    use RouterAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'route';
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        $route = $this->getRouter()->getCurrent();

        return $this->getRouteInformation($route);
    }

    /**
     * @param Route $route
     *
     * @return array
     */
    public function getRouteInformation($route)
    {
        if ($route) {
            $result = [
                'uri'    => $route->getUri(),
                'name'   => $route->getName(),
                'class'  => $route->getClassName(),
                'params' => $this->getDataFormatter()->formatVar($route->getParams()),
            ];
        } else {
            $result = [];
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgets()
    {
        $widgets = [
            'route' => [
                'icon'    => 'share',
                'widget'  => 'PhpDebugBar.Widgets.VariableListWidget',
                'map'     => 'route',
                'default' => '{}',
            ],
            'currentroute' => [
                'icon'    => 'share',
                'tooltip' => 'Route',
                'map'     => 'route.uri',
                'default' => '',
            ],
        ];

        return $widgets;
    }

    /**
     * Display the route information on the console.
     *
     * @param array $routes
     *
     * @return void
     */
    protected function displayRoutes(array $routes)
    {
        $routes = ['1', 2, 5];
        $this->table->setHeaders($this->headers)->setRows($routes);
        $this->table->render($this->getOutput());
    }
}
