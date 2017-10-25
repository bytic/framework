<?php

namespace Nip\Helpers\View;

class HTML extends AbstractHelper
{
    public function booleanOptions($selected = "")
    {
        $return = "";

        $return .= '<option value="0"' . ($selected !== "" && $selected == '0' ? ' selected="selected"'
                : '') . '>' . translator()->translate("NO") . '</option>';
        $return .= '<option value="1"' . ($selected == '1' ? ' selected="selected"'
                : '') . '>' . translator()->translate("YES") . '</option>';

        return $return;
    }

    /**
     * @param $value
     * @param $string
     * @param $tree
     * @param bool $selected
     * @return string|false
     */
    public function treeOptions($value, $string, $tree, $selected = false)
    {
        $optionsArray = $this->optionTree($tree, $value, $string);

        return $this->options('value', 'string', $optionsArray, $selected);
    }

    /**
     * @param $tree
     * @param $value
     * @param $string
     * @param array $array
     * @param int $level
     * @return boolean
     */
    public function optionTree(
        $tree,
        $value,
        $string,
        $array = array(),
        $level = 0
    ) {
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
                    'disabled' => ($page->has_children == '1') ? false : true,
                );


                if (is_array($page->children) && count($page->children) > 0) {
                    $array = $this->optionTree($page->children, $value, $string,
                        $array, ($level + 1));
                }
            }
        }

        return $array;
    }

    /**
     * @param string $options
     * @param bool $value
     * @param bool $string
     * @param bool $selected
     * @return string|false
     */
    public function options(
        $options,
        $value = false,
        $string = false,
        $selected = false
    ) {
        if (count($options)) {
            $return = '';
            foreach ($options as $key => $option) {
                if (is_string($key) && is_array($option) && !isset($option[$value])) {
                    $return .= '<optgroup label="' . $key . '">';
                    $return .= $this->options($option, $value, $string,
                        $selected);
                    $return .= '</optgroup>';
                } else {
                    if (is_object($option)) {
                        $oValue = $option->$value;
                        $oString = $option->$string;
                        $oDisabled = $option->disabled;
                    } elseif (is_array($option)) {
                        $oValue = $option[$value];
                        $oString = $option[$string];
                        $oDisabled = $option['disabled'];
                    } elseif ($value == true) {
                        $oValue = $key;
                        $oString = $option;
                    } else {
                        $oValue = $option;
                        $oString = $option;
                    }

                    $oSelected = ($oValue == $selected) ? ' selected="selected" '
                        : '';
                    $oDisabled = ($oDisabled === true) ? ' disabled="disabled" '
                        : '';

                    $return .= '<option value="' . $oValue . '"' . $oSelected . '' . $oDisabled . '>' . $oString . '</option>';
                }
            }

            return $return;
        } else {
            return false;
        }
    }

    public function radios(
        $name,
        $value,
        $string,
        $options,
        $separator = '&nbsp;',
        $selected = false
    ) {
        if (is_array($options)) {
            $return = '';
            foreach ($options as $option) {
                if (is_object($option)) {
                    $oValue = $option->$value;
                    $oString = $option->$string;
                } elseif (is_array($option)) {
                    $oValue = $option[$value];
                    $oString = $option[$string];
                } else {
                    $oValue = $option;
                    $oString = $option;
                }
                $oSelected = ($oValue == $selected) ? ' checked="checked" ' : '';
                $return .= '<input type="radio" name="' . $name . '" value="' . $oValue . '" ' . $oSelected . ' >' . $oString . $separator;
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

    /**
     * @param array $attributes
     * @return string
     */
    public function attributes($attributes = array())
    {
        $return = [];

        if ($attributes) {
            foreach ($attributes as $key => $value) {
                $return[] = "$key=\"$value\"";
            }
        }

        return " " . implode(" ", $return);
    }

    /**
     * @param $buffer
     * @return mixed
     */
    public function compress($buffer)
    {
        // remove comments, tabs, spaces, newlines, etc.
        $search = array(
            "/ +/" => " ",
            "/<!--(.*?)-->|[\t\r\n]|<!--|-->|\/\/ <!--|\/\/ -->|<!--\[CDATA\[|\/\/ \]\]-->|\]\]>|\/\/\]\]>|\/\/<!--\[CDATA\[/" => "",
        );

        return preg_replace(array_keys($search), array_values($search), $buffer);
    }
}
