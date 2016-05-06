<?php

class Nip_Tool_Generator_Entity_Model extends Nip_Tool_Generator_Entity_Abstract
{

    public function generate($name)
    {
        $dir = $this->getDir($name);
	    if (!is_dir($dir)) {
			mkdir($dir, 0755, true);
	    }

        $this->generateRecords($name);
        $this->generateRecord($name);
        
        return $this->getConsole()->success('model ' . $name . ' generated');
    }

    public function getDir($name)
    {
        $name = str_replace('-', '/', $name);
        return MODELS_PATH . $name . '/';
    }

    public function getFilePath($name, $singular = false)
    {
        $fName = end(explode('-', $name));
        $fName = inflector()->classify($fName);
        if ($singular) {
            $fName = inflector()->singularize($fName);
        }
        $fName .= '.php';
        return $this->getDir($name) . $fName;
    }


    public function generateRecords($name)
    {
        $class = inflector()->classify($name);
        $content = $this->getGenerator()->newClass()
                ->setName($class)->setExtends('Records')
                ->addInstanceMethod()
                ->generate();
        $this->getConsole()->log('generate records [' . $class . ']');
        return file_put_contents($this->getFilePath($name), $content);
    }

    public function generateRecord($name)
    {
        $class = inflector()->singularize(inflector()->classify($name));
        $content = $this->getGenerator()->newClass()
                ->setName($class)->setExtends('Record')
                ->generate();
        $this->getConsole()->log('generate record [' . $class . ']');
        return file_put_contents($this->getFilePath($name, true), $content);
    }

}