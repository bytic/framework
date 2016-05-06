<?php

class Nip_Tool_Generator_Entity_Controller extends Nip_Tool_Generator_Entity_Abstract
{

    public function generate($name)
    {
        $file = $this->generatePath($name);
        if ($file) {
            if (is_file($file)) {
                return $this->getConsole()->error('controller exits ['.$file.']');
            }

            $this->getConsole()->log('Creating controller ['.$file.']');
            $content = $this->generateContent($name);
            $dir = dirname($file);
            if (!is_dir($dir)) {
                $this->getConsole()->log('Creating dir ['.$dir.']');
                mkdir($dir, 0755, true);
            }
            file_put_contents($file, $content);
            
			return $this->getConsole()->success('controller [' . $name . '] generated');
        }
        return $this->getConsole()->error('controller [' . $name . '] not generated');
    }

    public function generatePath($name)
    {
        if (substr_count($name, '-') > 0) {
			$parts = explode('-', $name);
			$module = array_shift($parts);

			if (!is_dir(MODULES_PATH . $module)) {
			    $this->getConsole()->error('No module ['.$module.']');
			    if (!$this->getConsole()->askConfirmation('Create it ?')) {
                    return false;
			    }

                $this->getConsole()->log('Creating module ['.$module.']');
                $this->getGenerator()->generateModule($module);
			}
            $fileName = Nip_Inflector::instance()->camelize(array_pop($parts)) . '.php';
            $path  = implode('/', $parts);
            
            return MODULES_PATH . $module .DS.  'controllers' .DS. ($path ? $path . DS : '') . $fileName;
		} else {
            $this->getConsole()->error('bad format. Ex: module-controller');
        }

        return false;
    }

    public function generateContent($name)
    {
		$parts = explode('-', $name);
		$module = array_shift($parts);
        $cName = implode('-', $parts);

        $name = Nip_Dispatcher::instance()->getFullControllerName($module, implode('-', $parts));
        $extends = $cName == 'page' ? 'PageController' : Nip_Dispatcher::instance()->getFullControllerName($module, 'page');

        $class = $this->getGenerator()->newClass();
        $class->setName($name);
        $class->setExtends($extends);

        return $class->generate();
    }
}