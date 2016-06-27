<?php

namespace Nip\FrontController;

class Trace
{

    protected $_traces = array();

    public function add($params)
    {
        if ($params) {
            if (!is_array($params)) {
                $trace['message'] = $params;
            } else {
                $trace = $params;
            }
            $this->_traces[] = $trace;
        }
        return true;
    }

    public function get()
    {
        return $this->_traces;
    }

    public function toString()
    {
        $output = '';
        $traces = $this->get();
        foreach ($traces as $trace) {
            $output .= $trace['message'] . '<br />';
        }
        return $output;
    }

    /**
     * Singleton
     *
     * @return Nip_FrontController_Trace
     */
    public static function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self;
        }
        return $instance;
    }
}