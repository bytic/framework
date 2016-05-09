<?php
class Nip_Profiler_Adapters_Console extends Nip_Profiler_Adapters_Abstract {

    protected $_plugin;

    public function output($name) {
        $this->_plugin = new Nip_Console_Plugin_Generic_Table($name);
        Console::instance()->plugIn($this->_plugin);

        $data = array();
        if ($this->data) {
            foreach ($this->data as $query) {
                $itemData = (array) $query;
                $item = array();
                foreach ($this->columns as $key) {
                    $item[$key] = $itemData[$key];
                }
                $data[] = $item;
            }
        }
        $this->_plugin->setData($data);
    }

}