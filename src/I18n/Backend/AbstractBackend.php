<?php

namespace Nip\I18n\Backend;

use Nip\I18n;

/**
 * Class AbstractBackend
 * @package Nip\I18n\Backend
 */
abstract class AbstractBackend
{

    /**
     * @var I18n
     */
    protected $i18n = null;

    protected $languages;

    /**
     * @return I18n
     */
    public function getI18n()
    {
        if (!$this->i18n) {
            $this->initI18n();
        }

        return $this->i18n;
    }

    /**
     * @param I18n $i18n
     */
    public function setI18n($i18n)
    {
        $this->i18n = $i18n;
    }

    public function initI18n()
    {
        $this->setI18n(I18n::instance());
    }

    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Returns dictionary entry for $slug in $language
     * @param string $slug
     * @param string|bool $language
     * @return string
     */
    public function translate($slug, $language = false)
    {
        $return = $this->doTranslation($slug, $language);
        if ($return) {
            return $return;
        }

        trigger_error("Dictionary entry for [" . $slug . "][" . $language . "] does not exist.", E_USER_WARNING);
        return $slug;
    }

    /**
     * @param $slug
     * @param bool $language
     * @return mixed
     */
    abstract protected function doTranslation($slug, $language = false);

    /**
     * @param $slug
     * @param bool $language
     * @return bool
     */
    public function hasTranslation($slug, $language = false)
    {
        $return = $this->doTranslation($slug, $language);

        return (bool)$return;
    }
}
