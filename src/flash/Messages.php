<?php

class Nip_Flash_Messages extends Nip_Flash {
	
	protected $session_var	= 'flash-messages';

    public function add($var, $type, $value = false) {
        if (!is_array($this->next[$var][$type])) {
            $this->next[$var][$type] = array($value);
        } else {
            if (is_array($value)) {
                $this->next[$var][$type] = array($this->next[$var][$type] , $value);
            } else {
                $this->next[$var][$type][] = $value;
            }
        }
        
		$this->write();
        return $this;
    }

	/**
	 * Returns static instance
	 *
	 * @return self
	 */
	static public function &instance() {
		static $instance;
		if (!($instance instanceof self)) {
			$instance = new self();
		}
		return $instance;
	}
}