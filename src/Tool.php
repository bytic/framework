<?php

class Nip_Tool
{

    protected $_console;
    protected $_generator;
    protected $_menus = array();

    /**
     * @return Nip_Tool_Console
     */
    public function getConsole()
    {
        if (!$this->_console) {
            $this->_console = new Nip_Tool_Console();
        }
        return $this->_console;
    }

    /**
     * @return Nip_Tool_Generator
     */
    public function getGenerator()
    {
        if (!$this->_generator) {
            $this->_generator = new Nip_Tool_Generator();
            $this->_generator->setTool($this);
        }
        return $this->_generator;
    }

    /**
     * @return Nip_Tool_Menu_Abstract
     */
    public function getMenu($name)
    {
        if (!$this->_menus[$name]) {
            $class = 'Nip_Tool_Menu_' . ucfirst($name);
            $this->_menus[$name] = new $class();
            $this->_menus[$name]->setTool($this);
        }
        return $this->_menus[$name];
    }

    /**
     * @return Nip_Tool_Menu_Abstract
     */
    public function runMenu($name)
    {
        return $this->getMenu($name)->main();
    }

    public function bootstrap()
    {
    }


    public function run()
    {
        $this->intro();
        return $this->mainMenu();
    }


    public function intro()
    {
        $this->getConsole()->output("***************************
***       Nip Tool      ***
***************************
");
    }
    
    public function mainMenu()
    {
        $response = $this->getConsole()->askVariant('How can i serve you today master ?', array(
                'model'     => 'Models',
                'module'    => 'Module',
                'controller' => 'Controller',
                'exit' => 'Exit',
            ));
        return $this->runMenu($response);
    }

}