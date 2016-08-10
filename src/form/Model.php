<?php

use Nip\Records\_Abstract\Row as Record;

/**
 * Class Nip_Form_Model
 */
class Nip_Form_Model extends Nip_Form
{

    /**
     * @var Record
     */
    protected $_model;

    public function setModel(Record $model)
    {
        $this->_model = $model;
        $this->getDataFromModel();
        return $this;
    }

    /**
     * @return Record
     */
    public function getModel()
    {
        return $this->_model;
    }

    protected function getDataFromModel()
    {
        $elements = $this->getElements();
        if (is_array($elements)) {
            foreach ($elements as $name => $element) {
                if (isset($this->getModel()->$name)) {
                    $element->getData($this->getModel()->$name, 'model');
                }
            }
        }
    }

    protected function _addModelFormMessage($form, $model)
    {
        $this->_messageTemplates[$form] = $this->getModelMessage($model);
        return $this;
    }

    public function addModelError($name)
    {
        return $this->addError($this->getModelMessage($name));
    }

    public function addInputModelError($input, $name, $variables = array())
    {
        return $this->$input->addError($this->getModelMessage($name, $variables));
    }

    public function getModelMessage($name, $variables = array())
    {
        return $this->getModel()->getManager()->getMessage('form.' . $name, $variables);
    }

    public function getModelLabel($name)
    {
        return $this->getModel()->getManager()->getLabel($name);
    }


    public function saveToModel()
    {
        $elements = $this->getElements();
        if (is_array($elements)) {
            foreach ($elements as $name => $element) {
                $this->getModel()->$name = $element->getValue('model');
            }
        }
    }

    public function process()
    {
        $this->saveToModel();
        $this->saveModel();
    }

    public function saveModel()
    {
        $this->getModel()->save();
    }

}