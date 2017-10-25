<?php

namespace Nip\View;

use Nip\View;

/**
 * Class XLS
 * @package Nip\View
 */
class XLS extends View
{

    /**
     * Singleton
     *
     * @return self
     */
    public static function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }

    public function initBasePath()
    {
        $this->setBasePath(MODULES_PATH . request()->getModuleName() . '/views/');
    }

    /**
     * @param $view
     * @param $name
     */
    public function output($view, $name)
    {
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$name\"");
        header("Cache-Control: private, max-age=1, pre-check=1", true);
        header("Pragma: none", true);

        echo $this->load($view);
        exit();
    }
}
