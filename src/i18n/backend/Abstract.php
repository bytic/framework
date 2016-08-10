<?php

abstract class Nip_I18n_Backend_Abstract
{

    protected $_i18n;
    protected $languages;

    public function setI18n($i18n)
    {
        $this->_i18n = $i18n;
    }

    public function getI18n()
    {
        if (!$this->_i18n) {
            $this->setI18n(Nip_I18n::instance());
        }
        return $this->_i18n;
    }


    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Returns dictionary entry for $slug in $language
     * @param string $slug
     * @param string $language
     * @return string
     */
    public function translate($slug, $language = false)
    {
        $return = $this->_translate($slug, $language);
        if ($return) {
            return $return;
        }

        trigger_error("Dictionary entry for [" . $slug . "][" . $language . "] does not exist.", E_USER_WARNING);
        return $slug;
    }

    public function hasTranslation($slug, $language = false)
    {
        $return = $this->_translate($slug, $language);
        return (bool)$return;
    }
}
