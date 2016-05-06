<?php

class Nip_Staging
{

	protected $_stage;
	protected $_stages;
    protected $_config;
    protected $_publicStages = array('production', 'staging', 'demo');

    /**
     * @return Nip_Staging_Stage
     */
	public function getStage()
	{
		if (!$this->_stage) {
			$_stage = false;
			if (isset($_SERVER['SERVER_NAME'])) {
				foreach ($this->getStages() as $stage) {
                    if($stage->isCurrent()) {
                        $_stage = $stage;
                        break;
                    }
				}
			}
            if ($_stage == false) {
                debug_print_backtrace();
                trigger_error('ERROR DETECTING STAGE', E_USER_ERROR);
            }
            
            $this->updateStage($_stage);
		}

		return $this->_stage;
	}

    public function updateStage($stage)
    {        
        $this->_stage = is_object($stage) ? $stage : $this->newStage($stage);
        return $this;
    }

    public function newStage($name)
    {
        if (!class_exists('Nip_Staging_Stage')) {
            require NIP_PATH . 'staging/Stage.php';
        }
        $stage = new Nip_Staging_Stage();
        $stage->setName($name);        

        return $stage;
    }

    public function getStages()
	{
		if (!$this->_stages) {
            if (!class_exists('Nip_File_System')) {
                require NIP_PATH . 'file/System.php';
            }
            $files = Nip_File_System::instance()->scanDirectory(CONFIG_STAGING_PATH);
            
            foreach ($files as $file) {
                $stageName = str_replace('.ini', '', $file);
                $this->_stages[$stageName] = $this->newStage($stageName);
                $this->_stages[$stageName]->init();
            }
		}
		return $this->_stages;
	}

    public function getConfig()
    {
        if (!$this->_config) {
            $this->_config = new Nip_Config();
            $this->_config->parse($this->_getConfigPath());

        }
        return $this->_config;
    }

    protected function _getConfigPath()
    {
        return CONFIG_PATH . 'staging.ini';
    }

	public function inProduction()
	{
		return $this->getStage()->getType() == 'production';
	}


    public function isPublic()
    {
        return !isset ($_SESSION['authorized']) && in_array($this->getStage()->getType(), $this->_publicStages);
    }

    /**
	 * Singleton
	 *
	 * @return Nip_Staging
	 */
	static public function instance()
	{
		static $instance;
		if (!($instance instanceof self)) {
			$instance = new self();
		}
		return $instance;
	}
}