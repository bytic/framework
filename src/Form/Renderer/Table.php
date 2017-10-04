<?php

use Nip\Form\Renderer\AbstractRenderer;

class Nip_Form_Renderer_Table extends AbstractRenderer
{
    protected $_table = [];
    protected $_tbody = [];
    protected $_data = [];
    protected $_rows = [];
    protected $_cols = [];

    public function __construct()
    {
        parent::__construct();
        $this->setTableAttrib('class', 'form horizontal');
        $this->setTableAttrib('cellspacing', '0');
        $this->setTableAttrib('cellpadding', '0');
    }

    public function setTableAttrib($type, $value)
    {
        $this->_table[$type] = $value;
        return $this;
    }

    public function setTBodyAttrib($type, $value)
    {
        $this->_tbody[$type] = $value;
        return $this;
    }

    public function setRowAttrib($idRow, $type, $value)
    {
        $this->_rows[$idRow][$type] = $value;
        return $this;
    }

    public function addClassName($name)
    {
        $this->_table['class'] .= ' ' . $name;
        return $this;
    }

    public function addCell($idRow, $idCol, $element, $type = 'text')
    {
        $this->_data[$idRow][$idCol]['element'] = $element;
        $this->_data[$idRow][$idCol]['type'] = $type;
        if (!in_array($idCol, $this->_cols)) {
            $this->_cols[] = $idCol;
        }
    }

    public function setCols()
    {
        $this->_cols = func_get_args();
    }


    public function renderElements()
    {
        $return = '<table';
        foreach ($this->_table as $attrib => $value) {
            $return .= ' ' . $attrib . '="' . $value . '"';
        }
        $return .= '>';
        $renderRows = $this->renderRows();
        $return .= '<tbody';
        foreach ($this->_tbody as $attrib => $value) {
            $return .= ' ' . $attrib . '="' . $value . '"';
        }
        $return .= '>';
        if ($renderRows) {
            $return .= $renderRows;
        }
        $return .= '</tbody>';
        $return .= '</table>';
        return $return;
    }

    public function renderRows()
    {
        $return = '';
        foreach ($this->_data as $idRow=>$row) {
            $cell = reset($row);
            $element = $cell['element'];

            if (!$element->isRendered()) {
                $return .= '<tr';
                if ($this->_rows[$idRow]) {
                    foreach ($this->_rows[$idRow] as $attrib => $value) {
                        $return .= ' ' . $attrib . '="' . $value . '"';
                    }
                }

                $return .= '>';
                foreach ($this->_cols as $idCol) {
                    $cell = $row[$idCol];
                    switch ($cell['type']) {
                        case 'label':
                            $return .= '<td class="label' . ($element->isError() ? ' error' : '') . '">';
                            $return .= $element->getLabel();
                            if ($element->isRequired()) {
                                $return .= '<span class="required">*</span>';
                            }
                            $return .= ":";
                            break;

                        case 'value':
                            $return .= '<td class="value">';
                            $return .= $this->renderElement($element);
                            $return .= '</td>';
                            break;

                        case 'text':
                        default:
                            $return .= '<td>';
                            $return .= $cell['element'];
                            $return .= '</td>';
                    }
                }
                $return .= '</tr>';
            }
        }
        return $return;
    }
}
