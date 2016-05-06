<?php

class Nip_I18n
{

    protected $_languageCodes = array(
        'en' => 'en_US'
        );

	protected $_backend;

	public $defaultLanguage = false;
	public $selectedLanguage = false;

	/**
	 * Sets the translation backend
	 * @param Nip_I18n_Abstract $backend
	 * @return Nip_I18n
	 */
	public function setBackend(Nip_I18n_Backend_Abstract $backend)
	{
		$this->_backend = $backend;
        $this->_backend->setI18n($this);
		return $this;
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

		$code = $this->_languageCodes[$language] ? $this->_languageCodes[$language] : $language . "_" . strtoupper($language);

		putenv('LC_ALL=' . $language);
		setlocale(LC_ALL, $language);

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
            if (isset($_SESSION['language'])) {
                $language = $_SESSION['language'];
            }

            if (isset($_GET['language'])) {
                $language = $_GET['language'];
            }

            if (Nip_Request::instance()->language) {
                $language = Nip_Request::instance()->language;
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
        $newURL = $newURL . (strpos($newURL, '?') == false ? '?' : '&') .'language='.$lang;
		return $newURL;
	}  
    
	/**
	 * Returns translation of $slug in given or selected $language
	 *
	 * @param string $slug
	 * @param string $language
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
					$return = str_replace("#{" . $key . "}", $value, $return);
				}
			}
		}

		return $return;
	}

	/**
	 * Returns translation of $slug in given or selected $language
	 *
	 * @param string $slug
	 * @param string $language
	 * @return string
	 */
	public function hasTranslation($slug = false, $params = array(), $language = false)
	{
		if (!$language) {
			$language = $this->getLanguage();
		}

		return $this->_backend->hasTranslation($slug, $language);;
	}

	/**
	 * Singleton pattern
	 *
	 * @return Nip_I18n
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