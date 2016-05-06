<?php
class Nip_Router
{

    protected $_route;
    protected $_routes;

    public function connect($route, $name)
    {
        $this->_routes[$name] = $route;
    }

    public function connected($name)
    {
        return ($this->getRoute($name) instanceof Nip_Route_Abstract);
    }

    public function route($uri)
    {
        $this->_route = false;
        
        foreach ($this->_routes as $name=>$route) {
            Nip_Profiler::instance()->start('route ['.$name.']');
            if ($route->match($uri)) {
                $this->_route = $route;
			    Nip_Profiler::instance()->end('route ['.$name.']');
                break;
            }
            
		    Nip_Profiler::instance()->end('route ['.$name.']');
        }

        if ($this->_route) {
            return $this->_route->getParams() + $this->_route->getMatches();
        } else {
            return array();
        }
    }

    public function assemble($name, $params = array())
    {
        $route = $this->getRoute($name);
        if (!$route) {
            $parts = explode(".", $name);
            if (count($parts) <= 2) {
                list($params['controller'], $params['action']) = $parts;
                $route = $this->getRoute('default');
            }
        }

        if ($route) {
            return $route->assemble($params);
        }

        trigger_error("Route \"$name\" not connected", E_USER_ERROR);
    }

    public function getCurrent()
    {
        return $this->_route;
    }

    public function getRoute($name)
    {
        return $this->_routes[$name];
    }

    /**
     * Singleton
     * @return Nip_Router
     */
    static public function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }
}