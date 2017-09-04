<?php

namespace Nip\I18n\Translator\Backend;

use Nip\Database\Connection;

/**
 * Nip Framework
 *
 * CREATE TABLE `i18n` (
 * `slug` VARCHAR( 255 ) NOT NULL ,
 * `language` VARCHAR( 10 ) NOT NULL ,
 * `translation` TEXT NOT NULL ,
 * PRIMARY KEY ( `slug` , `language` )
 * ) ENGINE = MYISAM COMMENT = 'I18n data'
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id$
 */
class Database extends AbstractBackend
{

    /**
     * @var Connection
     */
    protected $db;
    protected $table = 'i18n';
    protected $dictionary = [];

    /**
     * Adds a language to the dictionary
     *
     * @param string $language
     * @return Database
     */
    public function addLanguage($language)
    {
        $this->languages[] = $language;

        /* @var $results \Nip\Database\Result */
        $results = $this->getDb()->newSelect()
            ->from($this->table)
            ->where(["language", $language])
            ->execute();

        if ($results->numRows()) {
            while ($row = $results->fetchResult()) {
                $this->dictionary[$language][$row['slug']] = $row['translation'];
            }
        }

        return $this;
    }

    /**
     * @return Connection
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * Sets DB wrapper
     * @param Connection $db
     */
    public function setDb($db)
    {
        $this->db = $db;
    }

    /**
     * Returns dictionary entry for $slug in $language
     * @param string $slug
     * @param string|bool $language
     * @return string
     */
    protected function doTranslation($slug, $language = false)
    {
        if (isset($this->dictionary[$language][$slug])) {
            return $this->dictionary[$language][$slug];
        }

        return false;
    }
}
