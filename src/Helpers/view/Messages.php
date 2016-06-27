<?php

class Nip_Helper_View_Messages extends Nip_Helper_View_Abstract
{

    private $_cssClass = array(
        'warning'   => 'alert alert-warning',
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'info'    => 'alert alert-info',
        );

    public function warning($items = array(), $wrap = true)
    {
        return $this->render($items, 'warning', $wrap);
    }

    public function info($items = array(), $wrap = true)
    {
        return $this->render($items, 'info', $wrap);
    }
    
    public function success($items = array(), $wrap = true)
    {
        return $this->render($items, 'success', $wrap);
    }

    public function error($items = array(), $wrap = true)
    {
        return $this->render($items, 'error', $wrap);
    }

    public function render($items = array(), $type = false, $wrap = true)
    {
        $return = '';

        $items = (array) $items;
        
        if (count($items)) {
            if ($wrap) {
                $return .= '<div class="'.($type ? $this->_cssClass[$type] : '' ).'">';
                if (count($items) > 1) {
                    $return .= "<ul>";
                }
            }

            foreach ($items as $item) {
                $return .= is_array($item) ? $this->render($item, $type, false) : (count($items) > 1 ? "<li>$item</li>" : $item);
            }

            if ($wrap) {
                if (count($items) > 1) {
                    $return .= "</ul>";
                }
                $return .= "</div>";
            }
        }

        return $return;
    }

    /**
     * Singleton
     *
     * @return Nip_Helper_View_Errors
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