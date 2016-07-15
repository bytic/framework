<?php

class Nip_Helper_Array extends Nip\Helpers\AbstractHelper
{

    public function toXLS($array, $filename, $labels = array())
    {
        $xls = new Spreadsheet_Excel_Writer();
        $xls->setVersion(8);

        $sheet = $xls->addWorksheet();
        $sheet->setInputEncoding("UTF-8");

        $heading = $xls->addFormat(array('bold' => '1', 'align' => 'center'));

        if ($array && !$labels) {
            $labels = array_keys(reset($array));
        }

        $i = 0;
        foreach ($labels as $label) {
            $sheet->write(0, $i, $label, $heading);
            $i++;
        }

        if (count($array)) {
            $line = 1;
            foreach ($array as $item) {
                $column = 0;
                foreach ($labels as $label) {
                    $sheet->write($line, $column, html_entity_decode($item[$label], ENT_QUOTES, 'UTF-8'));
                    $column++;
                }
                $line++;
            }
        }

        header("Cache-Control: private, max-age=1, pre-check=1", true);
        header("Pragma: none", true);

        $xls->send($filename);
        $xls->close();
        exit();
    }

    /**
     * Produces a new version of the array that does not contain any of the specified values
     *
     * @param array $array
     * @return array
     */
    public function without($array)
    {
        $values = func_get_args();
        unset($values[0]);

        if ($values) {
            foreach ($values as $value) {
                unset($array[array_search($value, $array)]);
            }
        }

        return $array;
    }

    /**
     * Produces a new version of the array that does not contain any of the specified keys
     *
     * @param array $array
     * @return array
     */
    public function withoutKeys($array)
    {
        $values = func_get_args();
        unset($values[0]);

        if ($values) {
            foreach ($values as $value) {
                unset($array[$value]);
            }
        }

        return $array;
    }

    /**
     * Fetch the same property for all the elements.
     *
     * @param array $array
     * @param string $property
     * @return array The property values
     */
    public function changeKey($array, $property)
    {
        $return = array();

        if (count($array) > 0) {
            foreach ($array as $item) {
                $return[$item->$property] = $item;
            }
        }

        return $return;
    }

    /**
     * Fetch the same property for all the elements.
     *
     * @param array|Nip_RecordCollection $array
     * @param string $property
     * @param bool|string $return
     * @return array The property values
     */
    public function pluck($array, $property, &$return = false)
    {
        $return = array();

        if (count($array) > 0) {
            foreach ($array as $item) {
                if (is_array($item)) {
                    $this->pluck($array, $property, $return);
                }

                $return[] = $item->$property;
            }
        }

        return $return;
    }

    /**
     * Fetch the same property for all the elements.
     *
     * @param array $array
     * @param string $property
     */
    public function pluckFromArray($array, $property)
    {
        if (is_array($array)) {
            foreach ($array as $item) {
                $return[] = $item[$property];
            }
        }

        return $return;
    }

    /**
     * Finds array item that matches $params
     *
     * @param ArrayAccess $array
     * @param array $params
     * @return mixed
     */
    public function find($array, $params)
    {
        if (count($array)) {
            foreach ($array as $item) {
                $found = true;
                foreach ($params as $key => $value) {

                    if ($item->$key != $value) {
                        $found = false;
                    }
                }
                if ($found) {
                    return $item;
                }
            }
        }

        return null;
    }

    /**
     * Finds all array items that match $params
     *
     * @param array $array
     * @param array $params
     * @param string $key
     * @return array
     */
    public function findAll($array, $params, $returnKey = false)
    {
        $return = array();

        if (count($array)) {
            foreach ($array as $item) {
                $found = true;
                foreach ($params as $key => $value) {
                    if ($item->$key != $value) {
                        $found = false;
                    }
                }
                if ($found) {
                    if ($returnKey) {
                        $return[$item->$returnKey] = $item;
                    } else {
                        $return[] = $item;
                    }
                }
            }
        }

        return $return;
    }

    /**
     * Transposes a bidimensional array (matrix)
     *
     * @param array $array
     * @return array
     */
    public function transpose($array)
    {
        $return = array();

        if (count($array)) {
            foreach ($array as $key => $values) {
                foreach ($values as $subkey => $value) {
                    $return[$subkey][$key] = $value;
                }
            }
        }

        return $return;
    }

    /**
     * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
     *
     * @param array $data
     * @param string $rootNodeName - what you want the root node to be - defaults to data
     * @param SimpleXMLElement $xml - should only be used recursively
     * @return string XML
     */
    public function toXML($data, $rootNodeName = 'ResultSet', &$xml = null)
    {
        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        if (ini_get('zend.ze1_compatibility_mode') == 1)
            ini_set('zend.ze1_compatibility_mode', 0);

        if (is_null($xml)) {
            $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
        }

        // loop through the data passed in.
        foreach ($data as $key => $value) {
            // no numeric keys in our xml please!
            if (is_numeric($key)) {
                $numeric = 1;
                $key = $rootNodeName;
            }

            // delete any char not allowed in XML element names
            $key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);

            // if there is another array found recrusively call this function
            if (is_array($value)) {
                $node = $this->isAssoc($value) || $numeric ? $xml->addChild($key) : $xml;

                // recursive call
                if ($numeric)
                    $key = 'anon';
                $this->toXML($value, $key, $node);
            } else {
                // add single node.
                $value = htmlentities($value);
//                $xml->addChild($key, $value);
                $xml->addAttribute($key, $value);
            }
        }

        // pass back as XML
        // return $xml->asXML();
        // if you want the XML to be formatted, use the below instead to return the XML
        $doc = new DOMDocument('1.0');
        $doc->preserveWhiteSpace = false;
        $doc->loadXML($xml->asXML());
        $doc->formatOutput = true;
        return $doc->saveXML();
    }

    /**
     * Determine if a variable is an associative array
     *
     * @param array $array
     * @return bool
     */
    public static function isAssoc($array)
    {
        return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
    }

    function merge_distinct(array &$array1, array &$array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged [$key]) && is_array($merged [$key])) {
                $merged [$key] = $this->merge_distinct($merged [$key], $value);
            } else {
                $merged [$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Singleton
     *
     * @return Nip_Helper_Array
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
