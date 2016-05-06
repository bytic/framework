<?php

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id: Firebug.php 60 2009-04-28 14:50:04Z victor.stanciu $
 */

class Nip_Profiler_Adapters_Firebug extends Nip_Profiler_Adapters_Abstract {

    protected $columns;
    protected $FirePHP;


    public function __construct() {
        $this->FirePHP = FirePHP::getInstance(true);
    }


    public function output($name) {
        $table   = array();

        $table[] = array_merge(array('#'), (array) $this->columns);

        if (is_array($this->data)) {
            foreach ($this->data as $key => $qp) {
                $line = array();
                $line[] = $key;
                foreach ($this->columns as $column) {
                    $line[] = $qp->$column;
                }
                $table[] = $line;
            }
        }
        $this->FirePHP->table($name, $table);
    }
}