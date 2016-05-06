<?php

class Nip_Tool_Menu_Abstract
{
    protected $_tool;

    public function setTool($tool)
    {
        $this->_tool = $tool;
        return $this;
    }

    /**
     * @return Nip_Tool
     */
    public function getTool()
    {
        return $this->_tool;
    }

    public function main()
    {
        $this->intro();
        return $this->menu();
    }

    public function intro()
    {        
    }
    
    public function menu()
    {
        do {
            $response = $this->getTool()->getConsole()->askQuestion($this->_entity . ' name ? ( back for previous menu)');
            if ($response) {
                if ($response == 'back') {
                    return $this->getTool()->mainMenu();
                }
                
                $this->getTool()->getGenerator()->{'generate' . ucfirst ($this->_entity)}($response);
            } else {
                $this->getTool()->getConsole()->error("Empty response");
            }
        } while (1 == 1);
    }
}