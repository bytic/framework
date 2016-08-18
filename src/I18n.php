<?php

/**
 * Class Nip_I18n
 */
class Nip_I18n
{

    protected $_languageCodes = array(
        'en' => 'en_US',
    );

    /**
     * @var Nip_I18n_Backend_Abstract
     */
    protected $_backend;

    protected $_request;

    public $defaultLanguage = false;
    public $selectedLanguage = false;

    /**
     * Sets the translation backend
     * @param Nip_I18n_Backend_Abstract $backend
     * @return Nip_I18n
     */
    public function setBackend(Nip_I18n_Backend_Abstract $backend)
    {
        $this->_backend = $backend;
        $this->_backend->setI18n($this);

        return $this;
    }

    /**
     * @return Nip_I18n_Backend_Abstract
     */
    public function getBackend()
    {
        return $this->_backend;
    }

    /**
     * Selects a language to be used when translating
     *
     * @param string $language
     * @return Nip_I18n
     */
    public function setLanguage($language)
    {
        $this->selectedLanguage = $language;
        $_SESSION['language'] = $language;

        $code = $this->_languageCodes[$language] ? $this->_languageCodes[$language] : $language."_".strtoupper($language);

        putenv('LC_ALL='.$code);
        setlocale(LC_ALL, $code);
        setlocale(LC_NUMERIC, 'en_US');

        return $this;
    }

    /**
     * Sets the default language to be used when translating
     *
     * @param string $language
     * @return Nip_I18n
     */
    public function setDefaultLanguage($language)
    {
        $this->defaultLanguage = $language;

        return $this;
    }

    /**
     * gets the default language to be used when translating
     * @return string $language
     */
    public function getDefaultLanguage()
    {
        if (!$this->defaultLanguage) {
            $this->setDefaultLanguage(substr(setlocale(LC_ALL, 0), 0, 2));
        }

        return $this->defaultLanguage;
    }


    public function getLanguages()
    {
        return $this->_backend->getLanguages();
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

    public function changeLangURL($lang)
    {
        $newURL = str_replace('language='.$this->getLanguage(), '', CURRENT_URL);
        $newURL = $newURL.(strpos($newURL, '?') == false ? '?' : '&').'language='.$lang;

        return $newURL;
    }

    /**
     * Returns translation of $slug in given or selected $language
     *
     * @param string|boolean $slug
     * @param array $params
     * @param string|boolean $language
     * @return string
     */
    public function translate($slug = false, $params = array(), $language = false)
    {

        if (!$language) {
            $language = $this->getLanguage();
        }

        $return = $this->_backend->translate($slug, $language);

        if ($return) {
            if ($params) {
                foreach ($params as $key => $value) {
                    $return = str_replace("#{".$key."}", $value, $return);
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
     * @return string
     */
    public function hasTranslation($slug = false, $language = false)
    {
        if (!$language) {
            $language = $this->getLanguage();
        }

        return $this->_backend->hasTranslation($slug, $language);
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request)
    {
        $this->_request = $request;
    }

    /**
     * Singleton pattern
     *
     * @return self
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

if (!function_exists("__")) {
    function __($slug, $params = array(), $language = false)
    {
        return Nip_I18n::instance()->translate($slug, $params, $language);
    }
}

function nip__($slug, $params = array(), $language = false)
{
    return Nip_I18n::instance()->translate($slug, $params, $language);
}