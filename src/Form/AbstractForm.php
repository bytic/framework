<?php

namespace Nip\Form;

use Nip\Form\Renderer\AbstractRenderer;
use Nip\Form\Traits\MagicMethodElementsFormTrait;
use Nip\Form\Traits\NewElementsMethods;
use Nip\View;
use Nip_Form_Button_Abstract as ButtonAbstract;
use Nip_Form_DisplayGroup;
use Nip_Form_Element_Abstract as ElementAbstract;

/**
 * Class AbstractForm
 *
 */
abstract class AbstractForm
{
    use MagicMethodElementsFormTrait;
    use NewElementsMethods;

    const ENCTYPE_URLENCODED = 'application/x-www-form-urlencoded';
    const ENCTYPE_MULTIPART = 'multipart/form-data';

    /**
     * @var array
     */
    protected $methods = ['delete', 'get', 'post', 'put'];

    protected $_attribs = [];
    protected $_options = [];
    protected $_displayGroups = [];

    protected $_elements = [];
    protected $_elementsLabel;
    protected $_elementsOrder = [];

    protected $_buttons;

    protected $_decorators = [];
    protected $_renderer;
    protected $_messages = [
        'error' => [],
    ];
    protected $_messageTemplates = [];
    protected $_cache;

    protected $controllerView = false;

    /**
     * AbstractForm constructor.
     */
    public function __construct()
    {
        $this->init();
        $this->postInit();
    }

    public function init()
    {
        $this->setAction(current_url());
    }

    /**
     * @param string $action
     * @return AbstractForm
     */
    public function setAction($action)
    {
        return $this->setAttrib('action', (string)$action);
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setAttrib($key, $value)
    {
        $key = (string)$key;
        $this->_attribs[$key] = $value;

        return $this;
    }

    public function postInit()
    {
    }

    /**
     * @param $name
     * @param bool $label
     * @param string $type
     * @param bool $isRequired
     * @return $this
     */
    public function add($name, $label = false, $type = 'input', $isRequired = false)
    {
        $label = ($label) ? $label : ucfirst($name);
        $element = $this->getNewElement($type)
            ->setName($name)
            ->setLabel($label)
            ->setRequired($isRequired);
        $this->addElement($element);

        return $this;
    }

    /**
     * @param string $type
     * @return ElementAbstract
     */
    public function getNewElement($type)
    {
        $className = $this->getElementClassName($type);

        return $this->getNewElementByClass($className);
    }

    /**
     * @param $type
     * @return string
     */
    public function getElementClassName($type)
    {
        return 'Nip_Form_Element_'.ucfirst($type);
    }

    /**
     * @param $className
     * @return ElementAbstract
     */
    public function getNewElementByClass($className)
    {
        $element = new $className($this);

        return $this->initNewElement($element);
    }

    /**
     * @param ElementAbstract $element
     * @return ElementAbstract
     */
    public function initNewElement($element)
    {
        $element->setForm($this);

        return $element;
    }

    /**
     * @param ElementAbstract $element
     * @return $this
     */
    public function addElement(ElementAbstract $element)
    {
        $name = $element->getUniqueId();
        $this->_elements[$name] = $element;
        $this->_elementsLabel[$element->getLabel()] = $name;
        $this->_elementsOrder[] = $name;

        return $this;
    }

    /**
     * @param $name
     * @return ElementAbstract|null
     */
    public function __get($name)
    {
        $element = $this->getElement($name);
        if ($element) {
            return $element;
        }

        return null;
    }

    /**
     * @param $name
     * @return ElementAbstract
     */
    public function getElement($name)
    {
        if (array_key_exists($name, $this->_elements)) {
            return $this->_elements[$name];
        }

        return null;
    }

    /**
     * @param $className
     * @param $name
     * @param bool $label
     * @param bool $isRequired
     * @return $this
     */
    public function addCustom($className, $name, $label = false, $isRequired = false)
    {
        $label = ($label) ? $label : ucfirst($name);
        $element = $this->getNewElementByClass($className)
            ->setName($name)
            ->setLabel($label)
            ->setRequired($isRequired);
        $this->addElement($element);

        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function removeElement($name)
    {
        unset($this->_elements[$name]);

        $key = array_search($name, $this->_elementsOrder);
        if ($key) {
            unset($this->_elementsOrder[$key]);
        }

        return $this;
    }

    /**
     * Add a display group
     * Groups named elements for display purposes.
     * @param array $elements
     * @param $name
     * @return $this
     */
    public function addDisplayGroup(array $elements, $name)
    {
        $group = $this->newDisplayGroup();
        foreach ($elements as $element) {
            if (isset($this->_elements[$element])) {
                $add = $this->getElement($element);
                if (null !== $add) {
                    $group->addElement($add);
                }
            }
        }
        if (empty($group)) {
            trigger_error('No valid elements specified for display group');
        }

        $name = (string)$name;
        $group->setLegend($name);

        $this->_displayGroups[$name] = $group;

        return $this;
    }

    /**
     * @return Nip_Form_DisplayGroup
     */
    public function newDisplayGroup()
    {
        $group = new Nip_Form_DisplayGroup();
        $group->setForm($this);

        return $group;
    }

    /**
     * @param string $name
     * @return Nip_Form_DisplayGroup
     */
    public function getDisplayGroup($name)
    {
        if (array_key_exists($name, $this->_displayGroups)) {
            return $this->_displayGroups[$name];
        }

        return null;
    }

    /**
     * @return Nip_Form_DisplayGroup[]
     */
    public function getDisplayGroups()
    {
        return $this->_displayGroups;
    }

    /**
     * @param $name
     * @param bool $label
     * @param string $type
     * @return $this
     */
    public function addButton($name, $label = false, $type = 'button')
    {
        $this->_buttons[$name] = $this->newButton($name, $label, $type);

        return $this;
    }

    /**
     * @param $name
     * @param bool $label
     * @param string $type
     * @return ButtonAbstract
     */
    protected function newButton($name, $label = false, $type = 'button')
    {
        $class = 'Nip_Form_Button_'.ucfirst($type);
        /** @var ButtonAbstract $button */
        $button = new $class($this);
        $button->setName($name)
            ->setLabel($label);

        return $button;
    }

    /**
     * @param $name
     * @return ElementAbstract
     */
    public function getButton($name)
    {
        if (array_key_exists($name, $this->_buttons)) {
            return $this->_buttons[$name];
        }

        return null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasElement($name)
    {
        return array_key_exists($name, $this->_elements);
    }

    /**
     * @param $label
     * @return ElementAbstract
     */
    public function getElementByLabel($label)
    {
        if (array_key_exists($label, $this->_elementsLabel)) {
            return $this->_elements[$this->_elementsLabel[$label]];
        }

        return null;
    }

    /**
     * @param $element
     * @param $neighbour
     * @param string $type
     * @return $this
     */
    public function setElementOrder($element, $neighbour, $type = 'bellow')
    {
        if (in_array($element, $this->_elementsOrder) && in_array($neighbour, $this->_elementsOrder)) {
            $newOrder = [];
            foreach ($this->_elementsOrder as $current) {
                if ($current == $element) {
                } elseif ($current == $neighbour) {
                    if ($type == 'above') {
                        $newOrder[] = $element;
                        $newOrder[] = $neighbour;
                    } else {
                        $newOrder[] = $neighbour;
                        $newOrder[] = $element;
                    }
                } else {
                    $newOrder[] = $current;
                }
            }
            $this->_elementsOrder = $newOrder;
        }

        return $this;
    }

    /**
     * @return ButtonAbstract[]
     */
    public function getButtons()
    {
        return $this->_buttons;
    }

    /**
     * @param bool $params
     * @return array
     */
    public function findElements($params = false)
    {
        $elements = [];
        foreach ($this->_elements as $element) {
            if (isset($params['type'])) {
                if ($element->getType() != $params['type']) {
                    continue;
                }
            }
            if (isset($params['attribs']) && is_array($params['attribs'])) {
                foreach ($params['attribs'] as $name => $value) {
                    if ($element->getAttrib($name) != $value) {
                        continue(2);
                    }
                }
            }
            $elements[$element->getUniqueId()] = $element;
        }

        return $elements;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setOption($key, $value)
    {
        $key = (string)$key;
        $this->_options[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getOption($key)
    {
        $key = (string)$key;
        if (!isset($this->_options[$key])) {
            return null;
        }

        return $this->_options[$key];
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
     * @param string $key
     * @return string
     */
    public function getAttrib($key)
    {
        $key = (string)$key;
        if (!isset($this->_attribs[$key])) {
            return null;
        }

        return $this->_attribs[$key];
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

    /**
     * @param string $class
     * @return bool
     */
    public function hasClass($class)
    {
        return in_array($class, explode(' ', $this->getAttrib('class')));
    }

    /**
     * @return array
     */
    public function getAttribs()
    {
        return $this->_attribs;
    }

    /**
     * @param  array $attribs
     * @return $this
     */
    public function setAttribs(array $attribs)
    {
        $this->clearAttribs();

        return $this->addAttribs($attribs);
    }

    /**
     * @return $this
     */
    public function clearAttribs()
    {
        $this->_attribs = [];

        return $this;
    }

    /**
     * @param  array $attribs
     * @return $this
     */
    public function addAttribs(array $attribs)
    {
        foreach ($attribs as $key => $value) {
            $this->setAttrib($key, $value);
        }

        return $this;
    }

    /**
     * @param $key
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
     * @param $method
     * @return AbstractForm
     */
    public function setMethod($method)
    {
        if (in_array($method, $this->methods)) {
            return $this->setAttrib('method', $method);
        }
        trigger_error('Method is not valid', E_USER_ERROR);

        return null;
    }

    /**
     * @return bool
     */
    public function execute()
    {
        if ($this->submited()) {
            return $this->processRequest();
        }

        return false;
    }

    /**
     * @return bool
     */
    public function submited()
    {
        $request = $this->getAttrib('method') == 'post' ? $_POST : $_GET;
        if (count($request)) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function processRequest()
    {
        if ($this->validate()) {
            $this->process();

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        $request = $this->getAttrib('method') == 'post' ? $_POST : $_GET;
        $this->getDataFromRequest($request);
        $this->processValidation();

        return $this->isValid();
    }

    /**
     * @param $request
     */
    protected function getDataFromRequest($request)
    {
        $elements = $this->getElements();
        if (is_array($elements)) {
            foreach ($elements as $name => $element) {
                if ($element->isGroup() && $element->isRequestArray()) {
                    $name = str_replace('[]', '', $name);
                    $data = is_array($request[$name]) ? $request[$name] : [$request[$name]];
                    $element->getData($data, 'request');
                } else {
                    $value = $request[$name];
                    if (strpos($name, '[') && strpos($name, ']')) {
                        $arrayPrimary = substr($name, 0, strpos($name, '['));
                        $arrayKeys = str_replace($arrayPrimary, '', $name);

                        preg_match_all('/\[([^\]]*)\]/', $arrayKeys, $arr_matches, PREG_PATTERN_ORDER);
                        $value = $request[$arrayPrimary];
                        foreach ($arr_matches[1] as $dimension) {
                            $value = $value[$dimension];
                        }
                    }
                    $element->getData($value, 'request');
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getElements()
    {
        $return = [];
        foreach ($this->_elementsOrder as $current) {
            $return[$current] = $this->_elements[$current];
        }

        return $return;
    }

    public function processValidation()
    {
        $elements = $this->getElements();
        if (is_array($elements)) {
            foreach ($elements as $name => $element) {
                $element->validate();
            }
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return count($this->getErrors()) > 0 ? false : true;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        $errors = array_merge((array)$this->getMessagesType('error'), $this->getElementsErrors());

        return $errors;
    }

    /**
     * @param string $type
     * @return mixed
     */
    public function getMessagesType($type = 'error')
    {
        return $this->_messages[$type];
    }

    /**
     * @return array
     */
    public function getElementsErrors()
    {
        $elements = $this->getElements();
        $errors = [];
        if (is_array($elements)) {
            foreach ($elements as $name => $element) {
                $errors = array_merge($errors, $element->getErrors());
            }
        }

        return $errors;
    }

    public function process()
    {
    }

    /**
     * @param $message
     * @return $this
     */
    public function addError($message)
    {
        $this->_messages['error'][] = $message;

        return $this;
    }

    /**
     * @param $message
     * @param string $type
     * @return $this
     */
    public function addMessage($message, $type = 'error')
    {
        $this->_messages[$type][] = $message;

        return $this;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        $messages = $this->_messages;
        $messages['error'] = $this->getErrors();

        return $messages;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getMessageTemplate($name)
    {
        return $this->_messageTemplates[$name];
    }


    /**
     * @param $type
     * @return $this
     */
    public function setRendererType($type)
    {
        $this->setRenderer($this->getNewRenderer($type));

        return $this;
    }

    /**
     * @param string $class
     */
    protected function setRendererClass($class)
    {
        /** @var AbstractRenderer $renderer */
        $renderer = new $class();
        $renderer->setForm($this);
        $this->setRenderer($renderer);
    }

    /**
     * @param AbstractRenderer $renderer
     */
    public function setRenderer($renderer)
    {
        $this->_renderer = $renderer;
    }

    /**
     * @param string $type
     * @return AbstractRenderer
     */
    public function getNewRenderer($type = 'basic')
    {
        $name = 'Nip_Form_Renderer_'.ucfirst($type);
        /** @var AbstractRenderer $renderer */
        $renderer = new $name();
        $renderer->setForm($this);

        return $renderer;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getCache($key)
    {
        return $this->_cache[$key];
    }

    /**
     * @param string $key
     * @param $value
     */
    public function setCache($key, $value)
    {
        $this->_cache[$key] = $value;
    }

    /**
     * @param $key
     * @return bool
     */
    public function isCache($key)
    {
        return isset($this->_cache[$key]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return get_class($this);
    }

    /**
     * @return null|string
     */
    public function __toString()
    {
        $backtrace = debug_backtrace();
        if ($backtrace[1]['class'] == 'Monolog\Formatter\NormalizerFormatter') {
            return null;
        }
        trigger_error('form __toString', E_USER_WARNING);

        return $this->render();
    }

    /**
     * @return string
     */
    public function render()
    {
        return $this->getRenderer()->render();
    }

    /**
     * @return AbstractRenderer
     */
    public function getRenderer()
    {
        if (!$this->_renderer) {
            $this->_renderer = $this->getNewRenderer();
        }

        return $this->_renderer;
    }

    /**
     * @return View|null
     */
    public function getControllerView()
    {
        if (!$this->controllerView) {
            $this->controllerView = app('app')->getDispatcher()->getCurrentController()->getView();
        }

        return $this->controllerView;
    }

    /**
     * @return array
     */
    protected function getData()
    {
        $data = [];
        $elements = $this->getElements();
        if (is_array($elements)) {
            foreach ($elements as $name => $element) {
                $data[$name] = $element->getValue();
            }
        }

        return $data;
    }
}
