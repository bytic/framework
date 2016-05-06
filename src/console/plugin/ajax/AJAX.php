<?php
class Console_Plugin_AJAX extends Console_Plugin implements Console_Plugin_Interface {

    public function output() {
        include(dirname(__FILE__) . '/index.php');
    }

}