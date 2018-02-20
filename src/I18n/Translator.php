<?php

namespace Nip\I18n;

use Nip\I18n\Translator\Backend\AbstractBackend;

/**
 * Class Translator.
 */
class Translator
{
    public $defaultLanguage = false;
    public $selectedLanguage = false;

    protected $languageCodes = [
        'en' => 'en_US',
    ];

    /**
     * @var AbstractBackend
     */
    protected $backend;

    protected $request;

    /**
     * Singleton pattern.
     *
     * @return self
     */
    public static function instance()
    {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * @return AbstractBackend
     */
    public function getBackend()
    {
        return $this->backend;
    }

    /**
     * Sets the translation backend.
     *
     * @param AbstractBackend $backend
     *
     * @return $this
     */
    public function setBackend($backend)
    {
        $this->backend = $backend;
        $this->backend->setTranslator($this);

        return $this;
    }

    public function getLanguages()
    {
        return $this->backend->getLanguages();
    }

    /**
     * @param $lang
     *
     * @return mixed|string
     */
    public function changeLangURL($lang)
    {
        $newURL = str_replace('language='.$this->getLanguage(), '', CURRENT_URL);
        $newURL = $newURL.(strpos($newURL, '?') == false ? '?' : '&').'language='.$lang;

        return $newURL;
    }

    /**
     * Checks SESSION, GET and Nip_Request and selects requested language
     * If language not requested, falls back to default.
     *
     * @return string
     */
    public function getLanguage()
    {
        if (!$this->selectedLanguage) {
            $language = false;

            if (isset($_SESSION['language'])) {
                $language = $_SESSION['language'];
            }

            if (isset($_GET['language'])) {
                $language = $_GET['language'];
            }

            if ($this->getRequest()->language) {
                $language = $this->getRequest()->language;
            }

            if ($language) {
                $this->setLanguage($language);
            } else {
                $this->setLanguage($this->getDefaultLanguage());
            }
        }

        return $this->selectedLanguage;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * Selects a language to be used when translating.
     *
     * @param string $language
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->selectedLanguage = $language;
        $_SESSION['language'] = $language;

        $code = isset($this->languageCodes[$language]) ? $this->languageCodes[$language] : $language.'_'.strtoupper($language);

        putenv('LC_ALL='.$code);
        setlocale(LC_ALL, $code);
        setlocale(LC_NUMERIC, 'en_US');

        return $this;
    }

    /**
     * gets the default language to be used when translating.
     *
     * @return string $language
     */
    public function getDefaultLanguage()
    {
        if (!$this->defaultLanguage) {
            $this->setDefaultLanguage(substr(setlocale(LC_ALL, 0), 0, 2));
        }

        return $this->defaultLanguage;
    }

    /**
     * Sets the default language to be used when translating.
     *
     * @param string $language
     *
     * @return $this
     */
    public function setDefaultLanguage($language)
    {
        $this->defaultLanguage = $language;

        return $this;
    }

    /**
     * Returns translation of $slug in given or selected $language.
     *
     * @param string|bool $slug
     * @param array       $params
     * @param string|bool $language
     *
     * @return string
     */
    public function translate($slug = false, $params = [], $language = false)
    {
        if (!$language) {
            $language = $this->getLanguage();
        }

        $return = $this->backend->translate($slug, $language);

        if ($return) {
            if ($params) {
                foreach ($params as $key => $value) {
                    $return = str_replace('#{'.$key.'}', $value, $return);
                }
            }
        }

        return $return;
    }

    /**
     * Returns translation of $slug in given or selected $language.
     *
     * @param bool|string $slug
     * @param bool|string $language
     *
     * @return string
     */
    public function hasTranslation($slug = false, $language = false)
    {
        if (!$language) {
            $language = $this->getLanguage();
        }

        return $this->backend->hasTranslation($slug, $language);
    }
}
