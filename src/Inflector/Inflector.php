<?php

namespace Nip\Inflector;

use Nip\Utility\Traits\SingletonTrait;

/**
 * Class Inflector
 * @package Nip\Inflector
 */
class Inflector
{
    use SingletonTrait;

    protected $plural = [
        '/(quiz)$/i' => '\1zes',
        '/^(ox)$/i' => '\1en',
        '/([m|l])ouse$/i' => '\1ice',
        '/(matr|vert|ind)ix|ex$/i' => '\1ices',
        '/(x|ch|ss|sh)$/i' => '\1es',
        '/([^aeiouy]|qu)ies$/i' => '\1y',
        '/([^aeiouy]|qu)y$/i' => '\1ies',
        '/(hive)$/i' => '\1s',
        '/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
        '/sis$/i' => 'ses',
        '/([ti])um$/i' => '\1a',
        '/(buffal|tomat)o$/i' => '\1oes',
        '/(bu)s$/i' => '\1ses',
        '/(alias|status)/i' => '\1es',
        '/(octop|vir)us$/i' => '\1i',
        '/(ax|test)is$/i' => '\1es',
        '/s$/i' => 's',
        '/$/' => 's',
    ];
    protected $singular = [
        '/(quiz)zes$/i' => '\1',
        '/(matr)ices$/i' => '\1ix',
        '/(vert|ind)ices$/i' => '\1ex',
        '/^(ox)en/i' => '\1',
        '/(alias|status)es$/i' => '\1',
        '/([octop|vir])i$/i' => '\1us',
        '/(cris|ax|test)es$/i' => '\1is',
        '/(shoe)s$/i' => '\1',
        '/(o)es$/i' => '\1',
        '/(bus)es$/i' => '\1',
        '/([m|l])ice$/i' => '\1ouse',
        '/(x|ch|ss|sh)es$/i' => '\1',
        '/(m)ovies$/i' => '\1ovie',
        '/(s)eries$/i' => '\1eries',
        '/([^aeiouy]|qu)ies$/i' => '\1y',
        '/([lr])ves$/i' => '\1f',
        '/(tive)s$/i' => '\1',
        '/(hive)s$/i' => '\1',
        '/([^f])ves$/i' => '\1fe',
        '/(^analy)ses$/i' => '\1sis',
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
        '/([ti])a$/i' => '\1um',
        '/(n)ews$/i' => '\1ews',
        '/s$/i' => '',
    ];
    protected $uncountable = ['equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep'];
    protected $irregular = [
        'person' => 'people',
        'man' => 'men',
        'child' => 'children',
        'sex' => 'sexes',
        'move' => 'moves',
    ];
    protected $dictionary;
    protected $cacheFile = null;
    protected $toCache = false;

    /**
     * Inflector constructor.
     */
    public function __construct()
    {
        if (defined('CACHE_PATH')) {
            $this->cacheFile = CACHE_PATH.'inflector.php';
        }
        $this->readCache();
    }

    public function readCache()
    {
        if ($this->isCached()) {
            /** @noinspection PhpIncludeInspection */
            include($this->cacheFile);

            /** @noinspection PhpUndefinedVariableInspection */
            if ($inflector) {
                foreach ($inflector as $type => $words) {
                    if ($words) {
                        foreach ($words as $word => $inflection) {
                            $this->dictionary[$type][$word] = $inflection;
                        }
                    }
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function isCached()
    {
        if ($this->hasCacheFile()) {
            if (filemtime($this->cacheFile) + $this->getCacheTTL() > time()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasCacheFile()
    {
        return ($this->cacheFile && file_exists($this->cacheFile));
    }

    /**
     * @return int
     */
    public function getCacheTTL()
    {
        if (app()->has('config')) {
            $config = app()->get('config');
            if ($config->has('MISC.inflector_cache')) {
                return $config->get('MISC.inflector_cache');
            }
        }

        return 86400;
    }

    public function __destruct()
    {
        if ($this->toCache) {
            $this->writeCache();
        }
    }

    public function writeCache()
    {
        if ($this->dictionary && $this->cacheFile) {
            $file = new \Nip_File_Handler(["path" => $this->cacheFile]);
            $data = '<?php $inflector = '.var_export($this->dictionary, true).";";
            $file->rewrite($data);
        }
    }

    /**
     * @param $word
     * @return mixed
     */
    public function unclassify($word)
    {
        return $this->doInflection('unclassify', $word);
    }

    /**
     * @param $name
     * @param $word
     * @return mixed
     */
    public function doInflection($name, $word)
    {
        if (!isset($this->dictionary[$name][$word])) {
            $this->toCache = true;
            $method = "do".ucfirst($name);
            $this->dictionary[$name][$word] = $this->$method($word);
        }

        return $this->dictionary[$name][$word];
    }

    /**
     * @param $word
     * @return mixed
     */
    public function singularize($word)
    {
        return $this->doInflection('singularize', $word);
    }

    /**
     * @param $word
     * @return mixed
     */
    public function camelize($word)
    {
        return $this->doInflection('camelize', $word);
    }

    /**
     * @param $word
     * @return mixed
     */
    public function classify($word)
    {
        return $this->doInflection('classify', $word);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $word = $arguments[0];

        return $this->doInflection($name, $word);
    }

    /**
     * @param $word
     * @return bool|mixed
     */
    protected function doPluralize($word)
    {
        $lowerCased_word = strtolower($word);

        foreach ($this->uncountable as $_uncountable) {
            if (substr($lowerCased_word, (-1 * strlen($_uncountable))) == $_uncountable) {
                return $word;
            }
        }

        foreach ($this->irregular as $_plural => $_singular) {
            if (preg_match('/('.$_plural.')$/i', $word, $arr)) {
                return preg_replace('/('.$_plural.')$/i', substr($arr[0], 0, 1).substr($_singular, 1), $word);
            }
        }

        foreach ($this->plural as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return preg_replace($rule, $replacement, $word);
            }
        }

        return false;
    }

    /**
     * @param $word
     * @return mixed
     */
    protected function doSingularize($word)
    {
        $lowercased_word = strtolower($word);
        foreach ($this->uncountable as $_uncountable) {
            if (substr($lowercased_word, (-1 * strlen($_uncountable))) == $_uncountable) {
                return $word;
            }
        }

        foreach ($this->irregular as $_plural => $_singular) {
            if (preg_match('/('.$_singular.')$/i', $word, $arr)) {
                return preg_replace('/('.$_singular.')$/i', substr($arr[0], 0, 1).substr($_plural, 1), $word);
            }
        }

        foreach ($this->singular as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return preg_replace($rule, $replacement, $word);
            }
        }

        return $word;
    }

    /**
     * @param $word
     * @return mixed
     */
    protected function doCamelize($word)
    {
        return str_replace(' ', '', ucwords(preg_replace('/[^A-Z^a-z^0-9]+/', ' ', $word)));
    }

    /**
     * @param $word
     * @return mixed
     */
    protected function doHyphenize($word)
    {
        $word = $this->doUnderscore($word);

        return str_replace('_', '-', $word);
    }

    /**
     * @param $word
     * @return string
     */
    protected function doUnderscore($word)
    {
        return strtolower(preg_replace('/[^A-Z^a-z^0-9]+/', '_',
            preg_replace('/([a-zd])([A-Z])/', '\1_\2', preg_replace('/([A-Z]+)([A-Z][a-z])/', '\1_\2', $word))));
    }

    /**
     * Converts a class name to its table name according to rails
     * naming conventions.
     *
     * Converts "Person" to "people"
     *
     * @param string $class_name Class name for getting related table_name.
     * @return string plural_table_name
     */
    protected function doTableize($class_name)
    {
        return $this->pluralize($this->underscore($class_name));
    }

    /**
     * @param $word
     * @return mixed
     */
    public function pluralize($word)
    {
        return $this->doInflection('pluralize', $word);
    }

    /**
     * @param $word
     * @return mixed
     */
    public function underscore($word)
    {
        return $this->doInflection('underscore', $word);
    }

    /**
     * Converts lowercase string to underscored camelize class format
     *
     * @param string $string
     * @return string
     */
    protected function doClassify($string)
    {
        $parts = explode("-", $string);
        $parts = array_map([$this, "camelize"], $parts);

        return implode("_", $parts);
    }

    /**
     * Reverses classify()
     *
     * @param string $string
     * @return string
     */
    protected function doUnclassify($string)
    {
        $string = str_replace('\\', '_', $string);
        $parts = explode("_", $string);
        $parts = array_map([$this, "underscore"], $parts);

        return implode("-", $parts);
    }

    /**
     * @param $number
     * @return string
     */
    protected function doOrdinalize($number)
    {
        if (in_array(($number % 100), range(11, 13))) {
            return $number.'th';
        } else {
            switch (($number % 10)) {
                case 1:
                    return $number.'st';
                    break;
                case 2:
                    return $number.'nd';
                    break;
                case 3:
                    return $number.'rd';
                default:
                    return $number.'th';
                    break;
            }
        }
    }
}
