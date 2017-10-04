<?php

namespace Nip\Application;

class Trace
{
    protected $_traces = [];

    /**
     * Singleton
     *
     * @return self
     */
    public static function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self;
        }

        return $instance;
    }

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

    public function toString()
    {
        $output = '';
        $traces = $this->get();
        foreach ($traces as $trace) {
            $output .= $trace['message'] . '<br />';
        }
        return $output;
    }

    public function get()
    {
        return $this->_traces;
    }
}
