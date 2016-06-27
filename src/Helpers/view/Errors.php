<?php

class Nip_Helper_View_Errors extends Nip_Helper_View_Abstract
{

    public function render($items = array(), $wrap = true)
    {
        $return = '';

        if (count($items)) {
            if ($wrap) {
                $return .= '<div class="alert alert-danger">';
                if (count($items) > 1) {
                    $return .= '<strong>'.__('general.form.errors.explanation').':</strong>';
                    $return .= "<ul>";
                }
            }

            foreach ($items as $item) {
                $return .= is_array($item) ? $this->render($item, false) : (count($items) > 1 ? "<li>$item</li>" : $item);
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