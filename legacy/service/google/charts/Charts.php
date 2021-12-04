<?php

class Nip_Service_Google_Charts
{
    protected $_url = 'http://chart.apis.google.com/chart';

    /**
     * Chart factory.
     *
     * @param string $type
     *
     * @return Nip_Service_Google_Charts_Chart
     */
    public function getChart($type = 'Line')
    {
        $class = 'Nip_Service_Google_Charts_Chart_'.$type;

        $chart = new $class();
        $chart->setService($this);

        return $chart;
    }

    public function getURL()
    {
        return $this->_url;
    }

    /**
     * Singleton.
     *
     * @return Nip_Service_Google_Charts
     */
    public static function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();}

        return $instance;
    }
}
