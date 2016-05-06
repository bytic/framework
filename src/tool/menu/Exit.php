<?php

class Nip_Tool_Menu_Exit  extends Nip_Tool_Menu_Abstract
{    
    
    public function menu()
    {
        if ($this->getTool()->getConsole()->askConfirmation('Are you sure you want to exit ?')) {
            $this->getTool()->getConsole()->output("Thank you. Good bye");
            $this->getTool()->getConsole()->output("");
            return;
        }
        return $this->getTool()->mainMenu();
    }

}