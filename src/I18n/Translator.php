<?php

namespace Nip\I18n;

use Nip\I18n\Translator\Backend\AbstractBackend;
use Nip\Request;
use function Nip\url;

/**
 * Class Translator
 * @package Nip\I18n
 */
class Translator
{
    /**
     * @var bool
     */
    public $defaultLanguage = false;

    /**
     * @var bool|string
     */
    public $selectedLanguage = false;

    /**
     * @var array
     */
    protected $languageCodes = [
        'en' => 'en_US',
    ];

    /**
     * @var AbstractBackend
     */
    protected $backend;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Translator constructor.
     * @param AbstractBackend $backend
     */
    public function __construct(AbstractBackend $backend)
    {
        $this->setBackend($backend);
        $this->setRequest(app('request'));
    }

    /**
     * @return AbstractBackend
     */
    public function getBackend()
    {
        return $this->backend;
    }

    /**
     * Sets the translation backend
     * @param AbstractBackend $backend
     * @return $this
     */
    public function setBackend($backend)
    {
        $this->backend = $backend;
        $this->backend->setTranslator($this);

        return $this;
    }

    /**
     * @param $lang
     * @return string
     */
    public function changeLangURL($lang)
    {
        $newURL = str_replace('language=' . $this->getLanguage(), '', url()->current());
        $newURL = $newURL . (strpos($newURL, '?') == false ? '?' : '&') . 'language=' . $lang;

        return $newURL;
    }

    /**
     * Checks SESSION, GET and Nip_Request and selects requested language
     * If language not requested, falls back to default
     *
     * @return string
     */
    public function getLanguage()
    {
        if (!$this->selectedLanguage) {
            $language = false;

            if (isset($_SESSION['language']) && $this->isValidLanguage($_SESSION['language'])) {
                $language = $_SESSION['language'];
            }

            $requestLanguage = $this->getRequest()->get('language');
            if ($requestLanguage && $this->isValidLanguage($requestLanguage)) {
                $language = $requestLanguage;
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
     * @param $lang
     * @return bool
     */
    public function isValidLanguage($lang)
    {
        return in_array($lang, $this->getLanguages());
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        return $this->backend->getLanguages();
    }

    /**
     * @return Request
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
     * Selects a language to be used when translating
     *
     * @param string $language
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->selectedLanguage = $language;
        $_SESSION['language'] = $language;

        $code = $this->getLanguageCode($language);

        putenv('LC_ALL=' . $code);
        setlocale(LC_ALL, $code);
        setlocale(LC_NUMERIC, 'en_US');

        return $this;
    }

    /**
     * @param string $lang
     * @return string
     */
    public function getLanguageCode($lang)
    {
        if (isset($this->languageCodes[$lang])) {
            return $this->languageCodes[$lang];
        }

        return $lang . "_" . strtoupper($lang);
    }

    /**
     * gets the default language to be used when translating
     * @return boolean $language
     */
    public function getDefaultLanguage()
    {
        if (!$this->defaultLanguage) {
            $language = substr(setlocale(LC_ALL, 0), 0, 2);
            $languages = $this->getLanguages();
            $languageDefault = reset($languages);
            $language = $this->isValidLanguage($language) ? $language : $languageDefault;
            $this->setDefaultLanguage($language);
        }

        return $this->defaultLanguage;
    }

    /**
     * Sets the default language to be used when translating
     *
     * @param string $language
     * @return $this
     */
    public function setDefaultLanguage($language)
    {
        $this->defaultLanguage = $language;

        return $this;
    }

    /**
     * Returns translation of $slug in given or selected $language
     *
     * @param string|boolean $slug
     * @param array $params
     * @param string|boolean $language
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
                    $return = str_replace("#{" . $key . "}", $value, $return);
                }
            }
        }

        return $return;
    }

    /**
     * Returns translation of $slug in given or selected $language
     *
     * @param bool|string $slug
     * @param bool|string $language
     * @return boolean
     */
    public function hasTranslation($slug = false, $language = false)
    {
        if (!$language) {
            $language = $this->getLanguage();
        }

        return $this->backend->hasTranslation($slug, $language);
    }
}
