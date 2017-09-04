<?php

namespace Nip\I18n\Translator\Backend;

use Nip\I18n\Translator;

/**
 * Class AbstractBackend
 * @package Nip\I18n\Translator\Backend
 */
abstract class AbstractBackend
{

    /**
     * @var Translator
     */
    protected $translator = null;

    /**
     * @var array
     */
    protected $languages = [];

    /**
     * @return Translator
     */
    public function getTranslator()
    {
        if (!$this->translator) {
            $this->initTranslator();
        }

        return $this->translator;
    }

    /**
     * @param Translator $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    protected function initTranslator()
    {
    }

    public function initI18n()
    {
        $this->setTranslator(Translator::instance());
    }

    /**
     * @return array
     */
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
     * @param boolean|string $slug
     * @param bool $language
     * @return bool
     */
    public function hasTranslation($slug, $language = false)
    {
        $return = $this->doTranslation($slug, $language);

        return (bool) $return;
    }
}
