<?php

class Nip_View_XLS extends Nip_View
{

	public function __construct()
	{
		$this->setBasePath(MODULES_PATH . Nip_Request::instance()->module . '/views/');
	}

	public function output($view, $name)
	{
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=\"$name\"");
		header("Cache-Control: private, max-age=1, pre-check=1", true);
		header("Pragma: none", true);

		echo $this->load($view);
		exit();
	}

	/**
	 * Singleton
	 *
	 * @return Nip_View_XLS
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
