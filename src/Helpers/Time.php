<?php
class Nip_Helper_Time extends Nip\Helpers\AbstractHelper {


    public function parseMinutes($time = false)
    {
        $minutes = false;
        if (strpos($time, ':')) {            
            list($hours, $minutes) = explode(':', $time);
            $minutes += $hours*60;
        }
        return $minutes;
    }
	
	public function secondsInStringTime($seconds)
	{
		if ($days = intval((floor($seconds / 86400)))) {
            $seconds = $seconds - $days*86400;
			$return .= ($return ? ' ' : '') . str_pad($days, 2, 0,STR_PAD_LEFT).'d';
		}
		if ($hours = intval((floor($seconds / 3600))) OR $return) {
            $seconds = $seconds - $hours*3600;
			$return .= ($return ? ' ' : '') . str_pad($hours, 2, 0,STR_PAD_LEFT).'h';
		}
		if ($minutes = intval((floor($seconds / 60))) OR $return) {
			$seconds = $seconds - $minutes*60;
			$return .= ($return ? ' ' : '') . str_pad($minutes, 2, 0, STR_PAD_LEFT).'m';
		}
        
		$seconds = round($seconds, 2);  
        if ($seconds) {
            $return .= ($return ? ' ' : '') . str_pad($seconds, 2, 0, STR_PAD_LEFT).'s';
        }
			
		return $return;
	}


	/**
	 * Singleton
	 *
	 * @return Nip_Helper_Date
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
