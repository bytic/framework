<?php

class Nip_Tool_Generator_Entity_Module extends Nip_Tool_Generator_Entity_Abstract
{
    
    public function generate($name = false)
    {
        if ($name) {
            $this->setName($name);
        }

        $mainDir = $this->generatePath();
        $directories = array('controllers', 'forms', 'library', 'routes', 'views');

        foreach ($directories as $dirName) {
            $dir = $mainDir . $dirName;
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        $this->generateControllers();
        $this->generateRoutes();
        $this->generateViews();

        return $this->getConsole()->success('module [' . $name . '] generated');
    }

    public function generatePath()
    {
	    return MODULES_PATH . $this->getName() .DS;
    }

    public function generateControllers()
    {
        $this->getGenerator()->generateController($this->getName() . '-page');
        $this->getGenerator()->generateController($this->getName() . '-index');
    }
    
    public function generateRoutes()
    {
        $mainDir = $this->generatePath() . 'routes/';

        /** Static Route */
        $content = $this->getGenerator()->newClass()
                ->setName(ucfirst($this->getName()) . '_Route_Static')->setExtends('Nip_Route_Static')
                ->appendProperty('_params', 'protected', 'array("module" => "'.$this->getName().'")')
                ->generate();
        file_put_contents($mainDir . 'Static.php', $content);

        /** Regex Route */
        $content = $this->getGenerator()->newClass()
                ->setName(ucfirst($this->getName()) . '_Route_Regex')->setExtends('Nip_Route_Regex')
                ->appendProperty('_params', 'protected', 'array("module" => "'.$this->getName().'")')
                ->generate();
        file_put_contents($mainDir . 'Regex.php', $content);
        
        /** Dynamic Route */
        $content = $this->getGenerator()->newClass()
                ->setName(ucfirst($this->getName()) . '_Route_Dynamic')->setExtends('Nip_Route_Dynamic')
                ->appendProperty('_params', 'protected', 'array("module" => "'.$this->getName().'")')
                ->generate();
        file_put_contents($mainDir . 'Dynamic.php', $content);
        
        /** Default Route */
        $content = $this->getGenerator()->newClass()
                ->setName(ucfirst($this->getName()) . '_Route_Default')->setExtends('Nip_Route_Default')
                ->appendMethod('preMatch', 'protected', false, 'return true;')
                ->appendMethod('match', 'protected', array('uri' => ''), '$return = parent::match($uri);
if ($return && !empty($return->_params[\'controller\'])) {
    return $return;
}
return false;')->generate();
        file_put_contents($mainDir . 'Default.php', $content);
    }

    public function generateViews()
    {
        $mainDir = $this->generatePath() . 'views/';
        $directories = array(
            $mainDir . 'layouts',
            $mainDir . 'index',
            $mainDir . 'modules');

        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

}