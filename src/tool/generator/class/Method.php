<?php

class Nip_Tool_Generator_Class_Method
{
    
    protected $_class;

    protected $_comments;
    protected $_name;
    protected $_scope;
    protected $_arguments;
    protected $_content;

    public function setClass($class)
    {
        $this->_class = $class;
        return $this;
    }

    /**
     * @return Nip_Tool_Generator_Class
     */
    public function getClass()
    {
        return $this->_class;
    }

    public function setComments($comments)
    {
        $this->_comments = $comments;
        return $this;
    }

    public function getComments()
    {
        return $this->_comments;
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

    public function setScope($scope)
    {
        $this->_scope = $scope;
        return $this;
    }

    public function getScope()
    {
        return $this->_scope;
    }

    public function setArguments(array $arguments)
    {
        $this->_arguments = $arguments;
        return $this;
    }

    public function getArguments()
    {
        return $this->_arguments;
    }

    public function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }

    public function getContent()
    {
        return $this->_content;
    }



    public function generate()
    {
        $content = $this->generateComments();
        $content .= $this->generateHeader();
        $content .= $this->generateContent();
        $content .= $this->generateFooter();

        return $content;
    }

    public function generateComments()
    {
        $comments = $this->getComments();
        if ($comments) {
            $lines = explode("\n", $comments);
            $content[] = $this->getClass()->getTab() . '/**';
            foreach ($lines as $line) {
                $content[] = $this->getClass()->getTab() . ' * '. $line;
            }
            $content[] = $this->getClass()->getTab() . ' */';
            return implode("\n", $content) . "\n";
        }
        return;
    }

    public function generateHeader()
    {
        $content = $this->getClass()->getTab() .$this->getScope(). " function {$this->_name} (" . $this->generateArguments() .")". "\n".
                   $this->getClass()->getTab() ."{" . "\n";
        return $content;
    }

    public function generateScope()
    {
        $content = $this->getScope() ? $this->getScope(). ' ' : 'public ';
        return $content;
    }

    public function generateArguments()
    {
        $arguments = $this->getArguments();
        if (is_array($arguments)) {
            $content = array();
            foreach ($arguments as $name => $value) {
                $content[] = '$' . $name . ($value ? ' = ' . $value : '');
            }
            return implode(', ', $content);
        }
        return FALSE;
    }

    public function generateContent()
    {
        $lines = explode("\n", $this->getContent());
        $return = '';
        foreach ($lines as $line) {
            $return .= $this->getClass()->getTab() . $this->getClass()->getTab() . $line . "\n";
        }

        return $return;
    }

    public function generateFooter()
    {
        $content = $this->getClass()->getTab() .'}'. "\n\n";
        return $content;
    }



}