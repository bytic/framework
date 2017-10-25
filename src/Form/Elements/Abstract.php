<?php

use Nip\Form\AbstractForm;

/**
 * Class Nip_Form_Element_Abstract
 */
abstract class Nip_Form_Element_Abstract implements Nip_Form_Element_Interface
{
    protected $_form;

    protected $_attribs;
    protected $_options;

    /**
     * @var null|string
     */
    protected $_uniqueID = null;

    protected $_isRequired;
    protected $_isRendered = false;
    protected $_errors = [];
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
        $key = (string) $key;
        $this->_attribs[$key] = $value;

        return $this;
    }

    public function getId()
    {
        return $this->getAttrib('id');
    }

    /**
     * @param string $key
     */
    public function getAttrib($key)
    {
        $key = (string) $key;
        if (!isset($this->_attribs[$key])) {
            return null;
        }

        return $this->_attribs[$key];
    }

    /**
     * @return string
     */
    public function getJSID()
    {
        $name = $this->getUniqueId();

        return str_replace(['][', '[', ']'], ['-', '-', ''], $this->getUniqueId());
    }

    /**
     * @return null|string
     */
    public function getUniqueId()
    {
        if (!$this->_uniqueID) {
            $this->initUniqueId();
        }

        return $this->_uniqueID;
    }

    /**
     * @param null|string $uniqueID
     */
    public function setUniqueID($uniqueID)
    {
        $this->_uniqueID = $uniqueID;
    }

    protected function initUniqueId()
    {
        $this->setUniqueID($this->generateUniqueId());
    }

    /**
     * @return null|string
     */
    protected function generateUniqueId()
    {
        $name = $this->getName();
        $registeredNames = (array) $this->getForm()->getCache('elements_names');
        if (in_array($name, $registeredNames)) {
            $name = uniqid($name);
        }
        $registeredNames[] = $name;
        $this->getForm()->setCache('elements_names', $registeredNames);
        return $name;
    }

    public function getName()
    {
        return $this->getAttrib('name');
    }

    /**
     * @return AbstractForm
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * @param AbstractForm $form
     * @return $this
     */
    public function setForm(AbstractForm $form)
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

    /**
     * @param $data
     * @param string $source
     * @return Nip_Form_Element_Abstract
     */
    public function getData($data, $source = 'abstract')
    {
        if ($source == 'model') {
            return $this->getDataFromModel($data);
        }

        return $this->getDataFromRequest($data);
    }

    /**
     * @param $data
     * @return $this
     */
    public function getDataFromModel($data)
    {
        $this->setValue($data);

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->setAttrib('value', $value);

        return $this;
    }

    /**
     * @param $request
     * @return $this
     */
    public function getDataFromRequest($request)
    {
        $request = clean($request);
        $this->setValue($request);

        return $this;
    }

    /**
     * @param boolean $isRequired
     * @return $this
     */
    public function setRequired($isRequired)
    {
        $this->_isRequired = (bool) $isRequired;

        return $this;
    }

    /**
     * @param boolean $isRendered
     * @return $this
     */
    public function setRendered($isRendered)
    {
        $this->_isRendered = (bool) $isRendered;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRendered()
    {
        return (bool) $this->_isRendered;
    }

    /**
     * @return $this
     */
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

    /**
     * @return $this
     */
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
            $message = $this->getForm()->getMessageTemplate('no-' . $this->getName());
            if (!$message) {
                $translateSlug = 'general.form.errors.required';
                $message = app('translator')->translate($translateSlug, array('label' => $this->getLabel()));
                if ($message == $translateSlug) {
                    $message = $message ? $message : 'The field `' . $this->getLabel() . '` is mandatory.';
                }
            }
            $this->addError($message);
        }
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return (bool) $this->_isRequired;
    }

    /**
     * @param string $requester
     * @return null
     */
    public function getValue($requester = 'abstract')
    {
        return $this->getAttrib('value');
    }

    /**
     * @return string
     */
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

    /**
     * @return bool
     */
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

    /**
     * @param string $key
     * @return bool
     */
    public function delAttrib($key)
    {
        $key = (string) $key;
        unset($this->_attribs[$key]);

        return true;
    }

    /**
     * @return mixed
     */
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
        $this->_attribs = [];

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

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setOption($key, $value)
    {
        $key = (string) $key;
        $this->_options[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @return null
     */
    public function getOption($key)
    {
        $key = (string) $key;
        if (!isset($this->_options[$key])) {
            return null;
        }

        return $this->_options[$key];
    }

    /**
     * @param string $type
     * @return mixed
     */
    public function newDecorator($type = '')
    {
        $name = 'Nip_Form_Decorator_Elements_' . ucfirst($type);
        $decorator = new $name();
        $decorator->setElement($this);

        return $decorator;
    }

    /**
     * @param Nip_Form_Decorator_Elements_Abstract $decorator
     * @param string $position
     * @param bool $name
     * @return $this
     */
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

    /**
     * @param boolean $position
     * @return mixed
     */
    public function getDecoratorsByPosition($position)
    {
        return $this->_decorators[$position];
    }

    /**
     * @param $name
     * @param bool $position
     * @return bool
     */
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

    /**
     * @param $name
     * @param bool $position
     * @return $this
     */
    public function removeDecorator($name, $position = false)
    {
        if ($position) {
            unset($this->_decorators[$position][$name]);
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

    /**
     * @return bool
     */
    public function hasCustomRenderer()
    {
        return false;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        return $this->getRenderer()->render($this);
    }

    /**
     * @return mixed
     */
    public function getRenderer()
    {
        return $this->getForm()->getRenderer()->getElementRenderer($this);
    }

    /**
     * @return mixed
     */
    public function renderElement()
    {
        return $this->getRenderer()->renderElement($this);
    }

    /**
     * @return mixed
     */
    public function renderErrors()
    {
        return $this->getRenderer()->renderErrors($this);
    }
}
