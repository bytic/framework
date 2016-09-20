<?php

namespace Nip\Helpers\View;

/**
 * Class Errors
 * @package Nip\Helpers\View
 */
class Errors extends AbstractHelper
{

    /**
     * @param array $items
     * @param bool $wrap
     * @return string
     */
    public static function render($items = [], $wrap = true)
    {
        $return = '';

        if (count($items)) {
            if ($wrap) {
                $return .= '<div class="alert alert-danger">';
                if (count($items) > 1) {
                    $return .= '<strong>';
                    $return .= translator()->translate('general.form.errors.explanation');
                    $return .= ':</strong>';
                    $return .= "<ul>";
                }
            }

            foreach ($items as $item) {
                $return .= is_array($item) ? self::render($item,
                    false) : (count($items) > 1 ? "<li>$item</li>" : $item);
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