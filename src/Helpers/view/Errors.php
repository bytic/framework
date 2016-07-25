<?php

namespace Nip\Helpers\View;

class Errors extends AbstractHelper
{

    public function render($items = array(), $wrap = true)
    {
        $return = '';

        if (count($items)) {
            if ($wrap) {
                $return .= '<div class="alert alert-danger">';
                if (count($items) > 1) {
                    $return .= '<strong>' . __('general.form.errors.explanation') . ':</strong>';
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
}