<?php

namespace Nip\DebugBar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Nip\Router\Route\AbstractRoute as Route;
use Nip\Router\Router as Router;

class RouteCollector extends DataCollector implements Renderable
{

    /**
     * @var Router
     */
    protected $_router;

    public function getRouter()
    {
        return $this->_router;
    }

    /**
     * @param Router $router
     */
    public function setRouter($router)
    {
        $this->_router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'route';
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        $route = $this->getRouter()->getCurrent();
        return $this->getRouteInformation($route);
    }

    /**
     * @param Route $route
     * @return array
     */
    public function getRouteInformation($route)
    {

        $result = [
            'uri' => $route->getUri(),
            'name' => $route->getName(),
            'class' => $route->getClassName(),
            'params' =>  $this->getDataFormatter()->formatVar($route->getParams())
        ];

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        $widgets = [
            "route" => [
                "icon" => "share",
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "route",
                "default" => "{}"
            ],
            "currentroute" => [
                "icon" => "share",
                "tooltip" => "Route",
                "map" => "route.uri",
                "default" => ""
            ]
        ];
        return $widgets;
    }

    /**
     * Display the route information on the console.
     *
     * @param  array $routes
     * @return void
     */
    protected function displayRoutes(array $routes)
    {
        $routes = array('1',2,5);
        $this->table->setHeaders($this->headers)->setRows($routes);
        $this->table->render($this->getOutput());
    }
}