<?php

class Nip_Cache_Manager
{

	protected $_active = false;
	protected $_ttl = 180;
	protected $_data;

	public function exists($cacheId)
	{
		return file_exists($this->filePath($cacheId));
	}

	public function valid($cacheId)
	{
		if ($this->isActive() && $this->exists($cacheId)) {
			$fmtime = filemtime($this->filePath($cacheId));
			if (($fmtime + $this->_ttl) > time()) {
				return true;
			}
		}
		return false;
	}

	public function get($cacheId)
	{
		if (!$this->valid($cacheId)) {
			return;
		}

		return $this->getData($cacheId);
	}

	public function reload($cacheId)
	{
	}

	public function getData($cacheId)
	{
		if (!$this->_data[$cacheId]) {
			$this->_data[$cacheId] = $this->loadData($cacheId);
		}

		return $this->_data[$cacheId];
	}

	public function loadData($cacheId, $retry = true)
	{
		$file = $this->filePath($cacheId);
		$content = file_get_contents($file);
		$data = unserialize($content);

		if (!$data) {
			if ($retry === false) {
				return false;
			}
			$this->reload($cacheId);
			return $this->loadData($cacheId, false);
		}

		return $data;
	}

	public function saveData($cacheId, $data)
	{
		$file = $this->filePath($cacheId);
		$content = serialize($data);
		return $this->save($file, $content);
	}

	public function save($file, $content)
	{
		$dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

		if (file_put_contents($file, $content)) {
			chmod($file, 0777);
			return true;
		} else {
			$message = "Cannot open cache file for writing: ";
			if (!Nip_Staging::instance()->isPublic()) {
				$message .= " [ ".$this->cache_file." ] ";
			}
			die($message);
		}
	}

	public function delete($cacheId)
	{
		$file = $this->filePath($cacheId);
		if (is_file($file)) {
			unlink($file);
		}
	}

	public function filePath($cacheId)
	{
		return $this->cachePath().$cacheId.'.php';
	}

	public function cachePath()
	{
		return CACHE_PATH;
	}

	public function isActive()
	{
		return $this->_active;
	}

	public function setActive(bool $active)
	{
		$this->_active = $active;
		return $this;
	}

}