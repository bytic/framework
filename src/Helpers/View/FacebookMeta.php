<?php

namespace Nip\Helpers\View;

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id: Meta.php 60 2009-04-28 14:50:04Z victor.stanciu $
 */
class FacebookMeta extends AbstractHelper
{
    public $title;
    public $site_name;
    public $url;
    public $description;
    public $image;
    public $app_id;
    public $type;
    public $locale;
    public $author;
    public $publisher;

    protected $_tags = array(
        'title' => array('prefix' => 'og'),
        'site_name' => array('prefix' => 'og'),
        'url' => array('prefix' => 'og'),
        'description' => array('prefix' => 'og'),
        'image' => array('prefix' => 'og'),
        'app_id' => array('prefix' => 'fb'),
        'type' => array('prefix' => 'og', 'default' => 'website'),
        'locale' => array('prefix' => 'og'),
        'author' => array('prefix' => 'og'),
        'publisher' => array('prefix' => 'article', 'default' => 'https://www.facebook.com/Galantom'),
    );

    public function __toString()
    {
        return $this->generateMetas();
    }

    public function generateMetas()
    {
        $return = [];
        foreach ($this->_tags as $field => $options) {
            $return[] = $this->generateMeta($field);
        }

        return implode("\n", $return);
    }

    public function generateMeta($field)
    {
        $value = $this->getValue($field);
        if ($value) {
            $options = $this->getFieldOptions($field);
            return '<meta property="' . $options['prefix'] . ':' . $field . '" content="' . $value . '" />';
        }
        return null;
    }

    public function getValue($field)
    {
        return $this->$field ? $this->$field : $this->getDefaultValue($field);
    }

    public function getDefaultValue($field)
    {
        $options = $this->getFieldOptions($field);
        return isset($options['default']) ? $options['default'] : null;
    }

    public function getFieldOptions($field)
    {
        return $this->_tags[$field];
    }
}
