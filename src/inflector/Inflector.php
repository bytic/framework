<?php

class Nip_Inflector
{
	protected $plural = array(
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
		'/$/' => 's'
	);
	protected $singular = array(
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
		'/s$/i' => ''
	);
	protected $uncountable = array('equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep');
	protected $irregular = array(
		'person' => 'people',
		'man' => 'men',
		'child' => 'children',
		'sex' => 'sexes',
		'move' => 'moves'
	);
	protected $dictionary;
	protected $cacheFile;
	protected $toCache = false;

	public function __construct()
	{
		$this->cacheFile = CACHE_PATH . 'inflector.php';
		$this->readCache();
	}

	public function __destruct()
	{
		if ($this->toCache) {
			$this->writeCache();
		}
	}

	public function __call($name, $arguments)
	{
		$word = $arguments[0];

		if (!isset($this->dictionary[$name][$word])) {
			$this->toCache = true;
			$this->dictionary[$name][$word] = call_user_func_array(array($this, "_" . $name), $arguments);
		}

		return $this->dictionary[$name][$word];
	}

	public function readCache()
	{
		if ($this->isCached()) {
			include($this->cacheFile);

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

	public function writeCache()
	{
		if ($this->dictionary) {
			$file = new Nip_File_Handler(array("path" => $this->cacheFile));
			$data = '<?php $inflector = ' . var_export($this->dictionary, true) . ";";
			$file->rewrite($data);
		}
	}

	public function isCached()
	{
		if (!file_exists($this->cacheFile)) {
			return false;
		}

		if (filemtime($this->cacheFile) + Nip_Config::instance()->MISC->inflector_cache < time()) {
			return false;
		}

		return true;
	}

	protected function _pluralize($word)
	{
		$lowercased_word = strtolower($word);

		foreach ($this->uncountable as $_uncountable) {
			if (substr($lowercased_word, (-1 * strlen($_uncountable))) == $_uncountable) {
				return $word;
			}
		}

		foreach ($this->irregular as $_plural => $_singular) {
			if (preg_match('/(' . $_plural . ')$/i', $word, $arr)) {
				return preg_replace('/(' . $_plural . ')$/i', substr($arr[0], 0, 1) . substr($_singular, 1), $word);
			}
		}

		foreach ($this->plural as $rule => $replacement) {
			if (preg_match($rule, $word)) {
				return preg_replace($rule, $replacement, $word);
			}
		}
		return false;
	}

	protected function _singularize($word)
	{
		$lowercased_word = strtolower($word);
		foreach ($this->uncountable as $_uncountable) {
			if (substr($lowercased_word, (-1 * strlen($_uncountable))) == $_uncountable) {
				return $word;
			}
		}

		foreach ($this->irregular as $_plural => $_singular) {
			if (preg_match('/(' . $_singular . ')$/i', $word, $arr)) {
				return preg_replace('/(' . $_singular . ')$/i', substr($arr[0], 0, 1) . substr($_plural, 1), $word);
			}
		}

		foreach ($this->singular as $rule => $replacement) {
			if (preg_match($rule, $word)) {
				return preg_replace($rule, $replacement, $word);
			}
		}

		return $word;
	}

	protected function _camelize($word)
	{
		return str_replace(' ', '', ucwords(preg_replace('/[^A-Z^a-z^0-9]+/', ' ', $word)));
	}

	protected function _underscore($word)
	{
		return strtolower(preg_replace('/[^A-Z^a-z^0-9]+/', '_', preg_replace('/([a-zd])([A-Z])/', '\1_\2', preg_replace('/([A-Z]+)([A-Z][a-z])/', '\1_\2', $word))));
	}

	protected function _hyphenize($word)
	{
		$word = $this->_underscore($word);
		return str_replace('_', '-', $word);
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
	protected function _tableize($class_name)
	{
		return $this->pluralize($this->underscore($class_name));
	}

	/**
	 * Converts lowercase string to underscored camelize class format
	 * 
	 * @param string $string
	 * @return string
	 */
	protected function _classify($string)
	{
		$parts = explode("-", $string);
		$parts = array_map(array($this, "camelize"), $parts);
		return implode("_", $parts);
	}

	/**
	 * Reverses classify()
	 * 
	 * @param string $string
	 * @return string
	 */
	protected function _unclassify($string)
	{
		$parts = explode("_", $string);
		$parts = array_map(array($this, "underscore"), $parts);
		return implode("-", $parts);
	}

	protected function _ordinalize($number)
	{
		if (in_array(($number % 100), range(11, 13))) {
			return $number . 'th';
		} else {
			switch (($number % 10)) {
				case 1:
					return $number . 'st';
					break;
				case 2:
					return $number . 'nd';
					break;
				case 3:
					return $number . 'rd';
				default:
					return $number . 'th';
					break;
			}
		}
	}

	public static function instance()
	{
		static $instance;
		if (!($instance instanceof self)) {
			$instance = new self();
		}
		return $instance;
	}

}