<?php

/**
 * Nip Framework
 *
    CREATE TABLE `i18n` (
    `slug` VARCHAR( 255 ) NOT NULL ,
    `language` VARCHAR( 10 ) NOT NULL ,
    `translation` TEXT NOT NULL ,
    PRIMARY KEY ( `slug` , `language` )
    ) ENGINE = MYISAM COMMENT = 'I18n data'
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id$
 */

class Nip_I18n_Backend_Database extends Nip_I18n_Backend_Abstract {

    protected $db;
    protected $table      = 'i18n';
    protected $dictionary = array();


    /**
     * Sets DB wrapper
     * @param Nip_DB_Wrapper $db
     */
    public function setDb($db) {
        $this->db = $db;        
    }


    /**
     * Adds a language to the dictionary
     *
     * @param string $language
     * @return Nip_I18n
     */
    public function addLanguage($language) {
        $this->languages[] = $language;

        /* @var $results Nip_DB_Result */
        $results = $this->db->select()->from($this->table)->where(array("language", $language))->go();
        
        if ($results->numRows()) {
            while ($row = $results->fetchResult()) {
                $this->dictionary[$language][$row['slug']] = $row['translation'];
            }
        }

        return $this;
    }


    /**
     * Returns dictionary entry for $slug in $language
     * @param string $slug
     * @param string $language
     * @return string
     */
    protected function _translate($slug, $language = false) {
        if (isset($this->dictionary[$language][$slug])) {
            return $this->dictionary[$language][$slug];
        }
        return false;
    }

}
