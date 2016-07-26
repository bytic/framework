<?php

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id$
 */
class Nip_I18n_Backend_File extends Nip_I18n_Backend_Abstract
{

    protected $variableName = 'lang';
    protected $dictionary;

    /**
     * Adds a language to the dictionary
     *
     * @param string $language
     * @param string $file Path to file containing translations
     * @return Nip_I18n
     */
    public function addLanguage($language, $file)
    {
        $this->languages[] = $language;

        if (is_dir($file)) {
            ob_start();
            $files = Nip_File_System::instance()->scanDirectory($file, true, true);
            if (is_array($files)) {
                foreach ($files as $file) {
                    if (Nip_File_System::instance()->getExtension($file) == 'php') {
                        $this->parseFile($file, $language);
                    }
                }
            }
            ob_end_clean();
        } elseif (is_file ($file)) {
            ob_start();
            $this->parseFile($file, $language);
            ob_end_clean();
        } else {
            trigger_error("Language file [".$file."][".$language."] does not exist", E_USER_ERROR);
        }

        return $this;
    }

    protected function parseFile($file, $language)
    {
        if (file_exists($file)) {
            include($file);
            if (${$this->variableName}) {
                foreach (${$this->variableName} as $slug => $translation) {
                    $this->dictionary[$language][$slug] = $translation;
                }
            }
        }

    }

    /**
     * Returns dictionary entry for $slug in $language
     * @param string $slug
     * @param string $language
     * @return string
     */
    protected function _translate($slug, $language = false)
    {
        if (isset($this->dictionary[$language][$slug])) {
            return $this->dictionary[$language][$slug];
        }
        return false;
    }

}