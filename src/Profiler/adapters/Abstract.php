<?php

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id: Abstract.php 60 2009-04-28 14:50:04Z victor.stanciu $
 */

class Nip_Profiler_Adapters_Abstract {
    protected $data;
    protected $columns;

    public function setProfiles($profiles) {
        if (is_array($profiles)) {
            foreach ($profiles as $p){
                $this->addProfile($p);
            }
        }
    }


    public function addProfile($profile) {
        if (!is_array($this->columns)) {
            $this->columns = $profile->columns;
        }
        $this->data[] = $profile;
    }


    public function output($name) {
    }
}