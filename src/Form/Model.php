<?php

use Nip\Records\AbstractModels\Record as Record;

/**
 * Class Nip_Form_Model
 */
class Nip_Form_Model extends Nip_Form
{

    /**
     * @var Record
     */
    protected $_model;

    /**
     * @param $name
     * @return $this
     */
    public function addModelError($name)
    {
        return $this->addError($this->getModelMessage($name));
    }

    public function getModelMessage($name, $variables = array())
    {
        return $this->getModel()->getManager()->getMessage('form.' . $name, $variables);
    }

    /**
     * @return Record
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @param Record $model
     * @return $this
     */
    public function setModel(Record $model)
    {
        $this->_model = $model;
        $this->getDataFromModel();

        return $this;
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

    /**
     * @param $input
     * @param $name
     * @param array $variables
     * @return $this
     */
    public function addInputModelError($input, $name, $variables = array())
    {
        return $this->$input->addError($this->getModelMessage($name, $variables));
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getModelLabel($name)
    {
        return $this->getModel()->getManager()->getLabel($name);
    }

    public function process()
    {
        $this->saveToModel();
        $this->saveModel();
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

    public function saveModel()
    {
        $this->getModel()->save();
    }

    protected function _addModelFormMessage($form, $model)
    {
        $this->_messageTemplates[$form] = $this->getModelMessage($model);

        return $this;
    }
}
