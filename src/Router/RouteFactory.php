<?php

namespace Nip\Router;

/**
 * Class RouteCollection
 * @package Nip\Router
 */
class RouteFactory
{


    /**
     * @param $name
     * @param RouteCollection $collection
     * @param $class
     * @param string $mapPrefix
     * @param string $map
     * @param array $params
     * @return mixed
     */
    public static function generateStandardRoute(
        $name,
        $collection,
        $class,
        $mapPrefix = '',
        $map = '/:controller/:action',
        $params = []
    ) {
        $route = new $class($mapPrefix.$map, $params);

        return $collection->add($route, $name);
    }
}
