<?php
class Nip_Helper_String extends Nip\Helpers\AbstractHelper {

    /**
     * Limits a string to a certain number of words
     *
     * @param string $string
     * @param int $limit
     * @param string $end
     * @return string
     */
    public function limitWords($string, $limit = false, $end = '...') {
        $words = explode(" ", $string);
        
        if (count($words) <= $limit) {
            return $string;
        }

        $return = array();
        for ($i = 0; $i < $limit; $i++) {
            $return[] = $words[$i];
        }

        $return[] = $end;

        return implode(" ", $return);
    }


    /**
     * Injects GET params in links
     * 
     * @param string $string
     * @param array $params
     * @return string
     */
    public function injectParams($string, $params = array()) {
        $links = preg_split('#(<a\b[^>]+>)#', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
        $old   = $links;

        foreach ($links as &$match) {
            if (preg_match('/<a\b/', $match) && !preg_match('/(?:#|mailto)/', $match)) {
                preg_match('/^([^"]+")([^"]+)/', $match, $matches);
                if ($matches) {
                    $link = html_entity_decode($matches[2]);
                    if (strpos($link, "?") === false) {
                        $link .= "?";
                    } else {
                        $link .= "&";
                    }

                    $link .= http_build_query($params);

                    $match = str_replace($matches[2], $link, $match);
                }
            }
        }

        $string = str_replace($old, $links, $string);
        return $string;
    }


    /**
     * Converts all relative hrefs and image srcs to absolute
     *
     * @param string $string
     * @param string $base
     * @return string
     */
    public function relativeToAbsolute($string, $base) {
        $matches = preg_split('#(<(a|img)\b[^>]+>)#', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
        $old     = $matches;

        foreach ($matches as &$match) {
            if (preg_match('/<(a|img)\b/', $match) && !preg_match('/(?:http|#|mailto)/', $match)) {
                $match = preg_replace('/^([^"]+")([^"]+)/', '$1'.$base.'$2', $match);
            }
        }

        $string = str_replace($old, $matches, $string);
        return $string;
    }

	public function moneyFormat($number)
	{
		return money_format('%n', $number);
	}
	
	public function cronoTimeInSeconds($time)
	{
		$parts = explode(':', $time);		
		$seconds = array_pop($parts);
		$minutes = array_pop($parts);
		$hours = array_pop($parts);
		$days = array_pop($parts);

		return (($days*24 + $hours)*60 + $minutes)*60 + $seconds;		
	}
	
	public function secondsInCronoTime($seconds)
	{
		if ($days = intval((floor($seconds / 86400)))) {
            $seconds = $seconds - $days*86400;
			$return .= ($return ? ':' : '') . str_pad($days, 2, 0,STR_PAD_LEFT);
		}
		if ($hours = intval((floor($seconds / 3600))) OR $return) {
            $seconds = $seconds - $hours*3600;
			$return .= ($return ? ':' : '') . str_pad($hours, 2, 0,STR_PAD_LEFT);
		}
		if ($minutes = intval((floor($seconds / 60))) OR $return) {
			$seconds = $seconds - $minutes*60;
			$return .= ($return ? ':' : '') . str_pad($minutes, 2, 0, STR_PAD_LEFT);
		}
		$seconds = round($seconds, 2);  
		$return .= ($return ? ':' : '') . str_pad($seconds, 2, 0, STR_PAD_LEFT);
			
		return $return;
	}
    
	/**
     * Singleton
     *
     * @return Nip_Helper_String
     */
    static public function instance() {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new self();
        }
        return $instance;
    }
}