<?php
require_once dirname(__FILE__) . '/Plugin.php';
require_once dirname(__FILE__) . '/plugin/default/Default.php';
require_once dirname(__FILE__) . '/plugin/ajax/AJAX.php';

class Console
{

	protected $_plugins = array();
	protected $_enabled = true;

	const DEFAULT_PLUGIN = 'default';

	public function __construct()
	{
		$plugin = new Console_Plugin_Default("Console");
		$this->plugIn($plugin, self::DEFAULT_PLUGIN);
	}

	public function plugIn(Console_Plugin $plugin, $key = false)
	{
		if ($key) {
			$this->_plugins[$key] = $plugin;
		} else {
			$this->_plugins[] = $plugin;
		}
	}

	public function getPlugins()
	{
		return $this->_plugins;
	}

	public function getPlugin($key)
	{
		return $this->_plugins[$key];
	}

	public function output()
	{
		if ($this->isEnabled()) {
			$plugins = $this->getPlugins();
			$activePlugin = $_COOKIE['console-plugin'];

			ob_start();
			include dirname(__FILE__) . '/index.php';
			$output = ob_get_contents();
			ob_clean();

			return $output;
		}

		return false;
	}

	static public function log($data)
	{
		$_this = self::instance();
		return $_this->getPlugin(self::DEFAULT_PLUGIN)->log($data);
	}

	static public function enable()
	{
		$_this = self::instance();
		$_this->setEnabled(true);
	}

	static public function disable()
	{
		$_this = self::instance();
		$_this->setEnabled(false);
	}

	public function setEnabled($enabled)
	{
		$this->_enabled = $enabled;

		$plugins = $this->getPlugins();
		if ($plugins) {
			foreach ($plugins as $plugin) {
				$plugin->setEnabled($enabled);
			}
		}
	}

	public function isEnabled()
	{
		return $this->_enabled;
	}

	/**
	 * Singleton
	 *
	 * @return Console
	 */
	public static function instance()
	{
		static $instance;
		if (!($instance instanceof self)) {
			$instance = new self();
		}
		return $instance;
	}

}

if (!function_exists("console")) {
	function console($input)
	{
		Console::log($input);
	}
}
