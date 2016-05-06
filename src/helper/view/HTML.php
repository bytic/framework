<?php

class Nip_Helper_View_HTML extends Nip_Helper_View_Abstract
{

    public function booleanOptions($selected = "")
    {
        $return = "";

        $return .= '<option value="0"'.($selected !== "" && $selected == '0' ? ' selected="selected"'
                    : '').'>'.__("NO").'</option>';
        $return .= '<option value="1"'.($selected == '1' ? ' selected="selected"'
                    : '').'>'.__("YES").'</option>';

        return $return;
    }

    public function options($options, $value = false, $string = false,
                            $selected = false)
    {
        if (count($options)) {
            $return = '';
            foreach ($options as $key => $option) {
                if (is_string($key) && is_array($option) && !isset($option[$value])) {
                    $return .= '<optgroup label="'.$key.'">';
                    $return .= $this->options($option, $value, $string,
                        $selected);
                    $return .= '</optgroup>';
                } else {
                    if (is_object($option)) {
                        $oValue    = $option->$value;
                        $oString   = $option->$string;
                        $oDisabled = $option->disabled;
                    } elseif (is_array($option)) {
                        $oValue    = $option[$value];
                        $oString   = $option[$string];
                        $oDisabled = $option['disabled'];
                    } elseif ($value == true) {
                        $oValue  = $key;
                        $oString = $option;
                    } else {
                        $oValue  = $option;
                        $oString = $option;
                    }

                    $oSelected = ($oValue == $selected) ? ' selected="selected" '
                            : '';
                    $oDisabled = ($oDisabled === true) ? ' disabled="disabled" '
                            : '';

                    $return.= '<option value="'.$oValue.'"'.$oSelected.''.$oDisabled.'>'.$oString.'</option>';
                }
            }
            return $return;
        } else {
            return false;
        }
    }

    public function treeOptions($value, $string, $tree, $selected = false)
    {
        $optionsArray = $this->optionTree($tree, $value, $string);
        return $this->options('value', 'string', $optionsArray, $selected);
    }

    public function optionTree($tree, $value, $string, $array = array(),
                               $level = 0)
    {
        if (is_array($tree)) {
            foreach ($tree as $page) {
                $oString = '';
                for ($i = 0; $i < $level; $i++) {
                    $oString .= '---';
                }
                $oString .= '|--';

                $oString .= $page->$string;
                $array[] = array(
                    'value' => $page->$value,
                    'string' => $oString,
                    'disabled' => ($page->has_children == '1') ? false : true
                );


                if (is_array($page->children) && count($page->children) > 0) {
                    $array = $this->optionTree($page->children, $value, $string,
                        $array, ($level + 1));
                }
            }
        }
        return $array;
    }

    public function radios($name, $value, $string, $options,
                           $separator = '&nbsp;', $selected = false)
    {
        if (is_array($options)) {
            $return = '';
            foreach ($options as $option) {
                if (is_object($option)) {
                    $oValue  = $option->$value;
                    $oString = $option->$string;
                } elseif (is_array($option)) {

                    $oValue  = $option[$value];
                    $oString = $option[$string];
                } else {

                    $oValue  = $option;
                    $oString = $option;
                }
                $oSelected = ($oValue == $selected) ? ' checked="checked" ' : '';
                $return.= '<input type="radio" name="'.$name.'" value="'.$oValue.'" '.$oSelected.' >'.$oString.$separator;
            }
            return $return;
        } else {
            return false;
        }
    }

    public function unorderedList($items = array())
    {
        $return = '<ul>';

        foreach ($items as $item) {
            $return .= "<li>$item</li>";
        }

        $return .= '</ul>';

        return $return;
    }

    public function attributes($attributes = array())
    {
        $return = array();

        if ($attributes) {
            foreach ($attributes as $key => $value) {
                $return[] = "$key=\"$value\"";
            }
        }

        return " ".implode(" ", $return);
    }

    /**
     * Singleton
     *
     * @return Nip_Helper_View_HTML
     */
    static public function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }
}