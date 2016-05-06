<?php

class Nip_Service_Google_Charts_Chart
{

	protected $_service;
	protected $_params = array();

	public function __toString()
	{
		return $this->render();
	}

	public function render()
	{
		return '<img src="'.$this->getService()->getURL().'?'.http_build_query($this->getParams()).'" alt="" />';
	}

	public function getParams()
	{
		return $this->_params;
	}

	/**
	 * @return Nip_Service_Google_Charts
	 */
	public function getService()
	{
		return $this->_service;
	}


	public function setService($service)
	{
		$this->_service = $service;
		return $this;
	}

	public function setSize($size)
	{
		$this->setParam("chs", $size);
		return $this;
	}

	public function setParam($name, $value)
	{
		$this->_params[$name] = $value;
		return $this;
	}

	public function getParam($name)
	{
		return $this->_params[$name];
	}

	public function setParams($params = array())
	{
		if (count($params)) {
			foreach ($params as $name => $value) {
				$this->setParam($name, $value);
			}
		}
		return $this;
	}
}