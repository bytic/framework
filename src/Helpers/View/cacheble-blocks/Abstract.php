<?php

class Nip_Helper_View_CachebleBlocks_Block_Abstract {

	protected $_name;
    
    /** $_model Nip_Record */
	protected $_model;
	protected $_manager;
	protected $_viewPath;

	public function setName($name) {
        $this->_name = $name;
        return $this;
	}

	public function setModel($model) {
        $this->_model = $model;
        return $this;
	}

	public function setManager($manager) {
        $this->_manager = $manager;
        return $this;
	}

	public function setViewPath($path) {
        $this->_viewPath = $path;
        return $this;
	}
       
	public function render() {        
        $file = $this->filePath();
//        echo gzuncompress(file_get_contents($file));
        echo readfile ($file);
	}
    
    public function valid($ttl)
    {
        $ttl = $ttl !== null ? $ttl : $this->_ttl;
		if ($this->exists()) {
			if (!is_int($ttl)) {
				return true;
			}
			$fmtime = filemtime($this->filePath());
			if (($fmtime + $ttl) > time()) {
				return true;
			}
		}
		return false;        
    }


    public function regenerate() {
        $file = $this->filePath();
        $content = $this->_manager->getView()->load($this->_viewPath, array(), true);
        
		$dir = dirname($file);
        if (!is_dir($dir)) {
            Nip_File_System::instance()->createDirectory($dir, 0777);            
        }

        $content = $this->_manager->getView()->HTML()->compress($content);
//        $content = gzcompress($content);
		if (file_put_contents($file, $content)) {
			chmod($file, 0777);
			return true;
		} else {
			$message = "Cannot open CachebleBlocks file for writing: ";
			if (Nip_Staging::instance()->getStage()->inTesting()) {
				$message .= " [ ".$file." ] ";
			}
			die($message);
		}        
	}

	public function exists()
	{
		return file_exists($this->filePath());
	}
       
	public function filePath()
	{
        $fileName = str_replace('/','+',$this->_viewPath); 
		return $this->cachePath().$fileName.'.html';
	}

	public function cachePath()
	{
		return $this->_model->getCacheBlocksPath();
	}
}