<?php

namespace Nip\Router;

/**
 * Class RouteCollection
 * @package Nip\Router
 */
class RouteFactory
{


    /**
     * @param RouteCollection $collection
     * @param $name
     * @param $class
     * @param string $mapPrefix
     * @param string $map
     * @param array $params
     * @return mixed
     */
    public static function generateLiteralRoute(
        $collection,
        $name,
        $class,
        $mapPrefix = '',
        $map = '/',
        $params = []
    ) {
        return self::generateGenericRoute($collection, $name, $class, $mapPrefix.$map, $params);
    }

    /**
     * @param RouteCollection $collection
     * @param $name
     * @param $class
     * @param string $map
     * @param array $params
     * @return mixed
     */
    public static function generateGenericRoute(
        $collection,
        $name,
        $class,
        $map,
        $params = []
    ) {
        $route = new $class($map, $params);

        return $collection->add($route, $name);
    }

    /**
     * @param RouteCollection $collection
     * @param $name
     * @param $class
     * @param string $mapPrefix
     * @param string $map
     * @param array $params
     * @return mixed
     */
    public static function generateStandardRoute(
        $collection,
        $name,
        $class,
        $mapPrefix = '',
        $map = '/:controller/:action',
        $params = []
    ) {
        return self::generateGenericRoute($collection, $name, $class, $mapPrefix.$map, $params);
    }
}
