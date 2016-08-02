<?php

namespace Nip\DebugBar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

class RouteCollector extends DataCollector implements Renderable
{

    protected $_router;

    public function getRouter()
    {

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
        $route = $this->router->current();
        return $this->getRouteInformation($route);
    }

    public function getRouteInformation()
    {

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
            ]
        ];
        $widgets['currentroute'] = [
            "icon" => "share",
            "tooltip" => "Route",
            "map" => "route.uri",
            "default" => ""
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
        $this->table->setHeaders($this->headers)->setRows($routes);
        $this->table->render($this->getOutput());
    }
}