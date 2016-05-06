<?php

class Nip_Tool_Generator_Class
{
    protected $_tab = '    ';

    protected $_name;
    protected $_extends;
    protected $_methods;
    protected $_properties;
    
    protected $_content;

    public function getTab()
    {
        return $this->_tab;
    }


    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setExtends($extends)
    {
        $this->_extends = $extends;
        return $this;
    }

    public function getExtends()
    {
        return $this->_extends;
    }

    public function newProperty()
    {
        $obj = new Nip_Tool_Generator_Class_Property();
        $obj->setClass($this);
        return $obj;
    }

    public function appendProperty($name, $scope = null, $value = null)
    {
        if (is_object($name)) {
            $property = $name;
        } else {
            $property = $this->newProperty()->setName($name);
            if ($scope) {
                $property->setScope($scope);
            }
            if ($value) {
                $property->setValue($value);
            }

        }
        $this->_properties[] = $property;
        return $this;
    }


    public function newMethod()
    {
        $obj = new Nip_Tool_Generator_Class_Method();
        $obj->setClass($this);
        return $obj;
    }

    public function appendMethod($name, $scope = null, $arguments = null, $content = null)
    {
        if (is_object($name)) {
            $method = $name;
        } else {
            $method = $this->newMethod()->setName($name);
            if ($scope) {
                $method->setScope($scope);
            }
            if ($arguments) {
                $method->setArguments($arguments);
            }
            if ($content) {
                $method->setContent($content);
            }

        }
        
        $this->_methods[] = $method;
        return $this;
    }

    public function addInstanceMethod()
    {
        $method = $this->newMethod()
                ->setComments('Singleton' . "\n" . '@return ' . $this->getName())
                ->setScope('static public')
                ->setName('instance')
                ->setContent('static $instance;
if (!($instance instanceof self)) {
    $instance = new self();
}
return $instance;');

        $this->appendMethod($method);
        return $this;
    }

    public function generate()
    {
        $this->_content = $this->generateHeader();
        $this->_content .= $this->generateProperties();
        $this->_content .= $this->generateMethods();
        $this->_content .= $this->generateFooter();
        return $this->_content;
    }

    public function generateHeader()
    {
        $content = '<?php' . "\n";
        $content .= "class {$this->_name} ";
        if ($this->_extends) {
            $content .= "extends {$this->_extends} ";
        }    
        $content .= "\n";
        $content .= "{" . "\n\n";
        return $content;
    }

    public function generateProperties()
    {
        $content = '';
        if ($this->_properties) {
			foreach ($this->_properties as $property) {
			    $pContent = $property->generate();
			    $content .= $pContent;
			}
		}
        return $content;
    }


    public function generateMethods()
    {
        $content = '';
        if ($this->_methods) {
			foreach ($this->_methods as $method) {
			    $mContent = $method->generate();
			    $content .= $mContent;
			}
		}
        return $content;
    }

    public function generateFooter()
    {
        $content = '}';
        return $content;
    }

}