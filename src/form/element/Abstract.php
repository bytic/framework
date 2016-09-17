<?php

abstract class Nip_Form_Element_Abstract implements Nip_Form_Element_Interface
{

    protected $_form;

    protected $_attribs;
    protected $_options;

    protected $_uniqueID;
    protected $_isRequired;
    protected $_isRendered = false;
    protected $_errors = array();
    protected $_decorators;
    protected $_policies;

    protected $_type = 'abstract';

    public function __construct($form)
    {
        $this->setForm($form);
        $this->init();
    }

    public function init()
    {
    }

    public function setId($id)
    {
        $this->setAttrib('id', $id);

        return $this;
    }

    /**
     * @return Nip_Form_Element_Abstract
     */
    public function setAttrib($key, $value)
    {
        $key = (string)$key;
        $this->_attribs[$key] = $value;

        return $this;
    }

    public function getId()
    {
        return $this->getAttrib('id');
    }

    public function getAttrib($key)
    {
        $key = (string)$key;
        if (!isset($this->_attribs[$key])) {
            return null;
        }

        return $this->_attribs[$key];
    }

    public function getJSID()
    {
        $name = $this->getUniqueId();

        return str_replace(array('][', '[', ']'), array('-', '-', ''), $this->getUniqueId());
    }

    public function getUniqueId()
    {
        if (!$this->_uniqueID) {
            $name = $this->getName();
            $registeredNames = (array)$this->getForm()->getCache('elements_names');
            if (in_array($name, $registeredNames)) {
                $name = uniqid($name);
            }
            $registeredNames[] = $name;
            $this->getForm()->setCache('elements_names', $registeredNames);

            $this->_uniqueID = $name;
        }

        return $this->_uniqueID;
    }

    public function getName()
    {
        return $this->getAttrib('name');
    }

    /**
     * @return Nip_Form_Abstract
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * @param Nip_Form_Abstract $form
     * @return $this
     */
    public function setForm(Nip_Form_Abstract $form)
    {
        $this->_form = $form;

        return $this;
    }

    public function setName($name)
    {
        $this->setAttrib('name', $name);

        return $this;
    }

    public function setLabel($label)
    {
        $this->setAttrib('label', $label);

        return $this;
    }

    public function getData($data, $source = 'abstract')
    {
        if ($source == 'model') {
            return $this->getDataFromModel($data);
        }

        return $this->getDataFromRequest($data);
    }

    public function getDataFromModel($data)
    {
        $this->setValue($data);

        return $this;
    }

    public function setValue($value)
    {
        $this->setAttrib('value', $value);

        return $this;
    }

    public function getDataFromRequest($request)
    {
        $request = clean($request);
        $this->setValue($request);

        return $this;
    }

    public function setRequired($isRequired)
    {
        $this->_isRequired = (bool)$isRequired;

        return $this;
    }

    public function setRendered($isRendered)
    {
        $this->_isRendered = (bool)$isRendered;

        return $this;
    }

    public function isRendered()
    {
        return (bool)$this->_isRendered;
    }

    public function addClass()
    {
        $classes = func_get_args();
        if (is_array($classes)) {
            $oldClasses = explode(' ', $this->getAttrib('class'));
            $classes = array_merge($classes, $oldClasses);
            $this->setAttrib('class', implode(' ', $classes));
        }

        return $this;
    }

    public function removeClass()
    {
        $removeClasses = func_get_args();
        if (is_array($removeClasses)) {
            $classes = explode(' ', $this->getAttrib('class'));
            foreach ($removeClasses as $class) {
                $key = array_search($class, $classes);
                if ($key !== false) {
                    unset($classes[$key]);
                }
            }
            $this->setAttrib('class', implode(' ', $classes));
        }

        return $this;
    }

    public function validate()
    {
        if ($this->isRequired() && !$this->getValue()) {
            $message = $this->getForm()->getMessageTemplate('no-'.$this->getName());
            if (!$message) {
                $translateSlug = 'general.form.errors.required';
                $message = app('translator')->translate($translateSlug, array('label' => $this->getLabel()));
                if ($message == $translateSlug) {
                    $message = $message ? $message : 'The field `'.$this->getLabel().'` is mandatory.';
                }
            }
            $this->addError($message);
        }
    }

    public function isRequired()
    {
        return (bool)$this->_isRequired;
    }

    public function getValue($requester = 'abstract')
    {
        return $this->getAttrib('value');
    }

    public function getLabel()
    {
        return $this->getAttrib('label');
    }

    /**
     * @param $message
     * @return $this
     */
    public function addError($message)
    {
        $this->_errors[] = $message;

        return $this;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    public function isError()
    {
        return count($this->_errors) > 0;
    }

    /**
     * @return bool
     */
    public function isGroup()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    public function delAttrib($key)
    {
        $key = (string)$key;
        unset($this->_attribs[$key]);

        return true;
    }

    public function getAttribs()
    {
        return $this->_attribs;
    }

    /**
     * @param  array $attribs
     * @return Nip_Form_Element_Abstract
     */
    public function setAttribs(array $attribs)
    {
        $this->clearAttribs();

        return $this->addAttribs($attribs);
    }

    /**
     * @return Nip_Form_Element_Abstract
     */
    public function clearAttribs()
    {
        $this->_attribs = array();

        return $this;
    }

    /**
     * @param  array $attribs
     * @return Nip_Form_Element_Abstract
     */
    public function addAttribs(array $attribs)
    {
        foreach ($attribs as $key => $value) {
            $this->setAttrib($key, $value);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function removeAttrib($key)
    {
        if (isset($this->_attribs[$key])) {
            unset($this->_attribs[$key]);

            return true;
        }

        return false;
    }

    public function setOption($key, $value)
    {
        $key = (string)$key;
        $this->_options[$key] = $value;

        return $this;
    }

    public function getOption($key)
    {
        $key = (string)$key;
        if (!isset($this->_options[$key])) {
            return null;
        }

        return $this->_options[$key];
    }

    public function newDecorator($type = '')
    {
        $name = 'Nip_Form_Decorator_Elements_'.ucfirst($type);
        $decorator = new $name();
        $decorator->setElement($this);

        return $decorator;
    }

    public function attachDecorator(
        Nip_Form_Decorator_Elements_Abstract $decorator,
        $position = 'element',
        $name = false
    ) {
        $decorator->setElement($this);
        $name = $name ? $name : $decorator->getName();
        $this->_decorators[$position][$name] = $decorator;

        return $this;
    }

    public function getDecoratorsByPosition($position)
    {
        return $this->_decorators[$position];
    }

    public function getDecorator($name, $position = false)
    {
        if ($position) {
            return $this->_decorators[$position][$name];
        } else {
            foreach ($this->_decorators as $position => $decorators) {
                if (isset($decorators[$name])) {
                    return $decorators[$name];
                }
            }
        }

        return false;
    }

    public function removeDecorator($name, $position = false)
    {
        if ($position) {
            unset ($this->_decorators[$position][$name]);
        } else {
            foreach ($this->_decorators as $position => $decorators) {
                if (isset($decorators[$name])) {
                    unset($decorators[$name]);

                    return $this;
                }
            }
        }

        return $this;
    }

    public function hasCustomRenderer()
    {
        return false;
    }

    public function render()
    {
        return $this->getRenderer()->render($this);
    }

    public function getRenderer()
    {
        return $this->getForm()->getRenderer()->getElementRenderer($this);
    }

    public function renderElement()
    {
        return $this->getRenderer()->renderElement($this);
    }

    public function renderErrors()
    {
        return $this->getRenderer()->renderErrors($this);
    }

}