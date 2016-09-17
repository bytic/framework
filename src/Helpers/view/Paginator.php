<?php

namespace Nip\Helpers\View;

class Paginator extends AbstractHelper
{

	protected $_url;
    protected $_interval = [];
	protected $_paginator;
	protected $_viewPath = "/pagination";

	public function __call($name, $arguments)
	{
		return call_user_func_array(array($this->getPaginator(), $name), $arguments);
	}

    /**
     * @return \Nip_Record_Paginator
     */
    public function getPaginator()
    {
        return $this->_paginator;
    }

    public function setPaginator($paginator)
    {
        $this->_paginator = $paginator;

        return $this;
    }

	public function url($page)
	{
		$return = $this->_url;
        $return = str_replace('&amp;page=' . $this->getPaginator()->getPage(), '', $return);
        $return = str_replace('&page=' . $this->getPaginator()->getPage(), '', $return);
        $return = str_replace('page=' . $this->getPaginator()->getPage(), '', $return);

		if ($page > 1) {
			$return = rtrim($return, "/");

            if (strpos($return, '?') === false) {
				$return .= '?page=' . $page;
			} else {
				$return .= '&page=' . $page;
			}
        }
		return $return;
	}

	public function render()
	{
		$return = '';

		if ($this->getPaginator() && $this->getPaginator()->getPages() > 1) {
			$this->getView()->paginator = $this;
			$this->getView()->pages = $this->getPaginator()->getPages();
			$this->getView()->page = $this->getPaginator()->getPage();
			$this->getView()->interval = $this->getInterval();

			$return = $this->getView()->load($this->_viewPath, array(), true);
		}

		return $return;
	}

	public function getInterval()
	{
		$this->interval['min'] = 1;
		$this->interval['max'] = $this->getPaginator()->getPages();

		$pages = $this->interval['max'];
		$page = $this->getPaginator()->getPage();

		if ($pages > 7) {
			if ($page <= 6) {
				$this->interval['min'] = 1;
				$this->interval['max'] = 7;
			} elseif ($pages - $page <= 5) {
				$this->interval['min'] = $pages - 6;
				$this->interval['max'] = $pages;
			} else {
				$this->interval['min'] = $page - 3;
				$this->interval['max'] = $page + 3;
			}
		}

		return $this->interval;
	}

	public function setViewPath($view)
	{
		$this->_viewPath = $view;
		return $this;
	}

	public function setURL($url)
	{
		$this->_url = $url;
		return $this;
	}

}
