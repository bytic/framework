<?php
require_once 'Adapter.php';
require_once 'Event.php';

class Logger
{
	const EVENT_ERROR = 'error';
	const EVENT_WARNING = 'warning';
	const EVENT_NOTICE = 'notice';
	const EVENT_INFO = 'info';

	protected $_adapter;
	protected $_eventTypes = array(self::EVENT_ERROR, self::EVENT_WARNING, self::EVENT_NOTICE, self::EVENT_INFO);

	public function error($data)
	{
		$this->logEvent(self::EVENT_ERROR, $data);
	}

	public function info($data)
	{
		$this->logEvent(self::EVENT_INFO, $data);
	}

	public function logEvent($type = self::EVENT_INFO, $data = null)
	{
		$event = Logger_Event::getNew($type);
		$event->setData($data);
		$event->setBacktrace(debug_backtrace());

		$this->getAdapter()->addEvent($event);
	}

	public function errorHandler($code, $string, $file, $line)
	{
		if (error_reporting() == 0) {
			return;
		}

		$string .= " in $file on $line";
		switch ($code) {
			case E_WARNING:
			case E_USER_WARNING:
				$this->error($string);
				break;

			case E_NOTICE:
			case E_USER_NOTICE:
				$this->info($string);
				break;

			case E_ERROR:
			case E_USER_ERROR:
				restore_error_handler();
				trigger_error($string, E_USER_ERROR);
				break;
			default:
				$this->error($string);
				break;
		}
	}

	public function output()
	{
		return $this->getAdapter()->output($this->_events);
	}

	public function addEventType($type)
	{
		if (!in_array($type, $this->getEventTypes())) {
			$this->_eventTypes[] = $type;
		}
	}

	public function getEventTypes()
	{
		return $this->_eventTypes;
	}

	public function setAdapter(Logger_Adapter $adapter)
	{
		$this->_adapter = $adapter;
	}

	public function getAdapter()
	{
		return $this->_adapter;
	}

	/**
	 * Singleton
	 * @return Logger
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