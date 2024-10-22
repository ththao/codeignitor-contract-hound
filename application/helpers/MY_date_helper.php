<?php defined('BASEPATH') OR exit('No direct script access allowed.');

if ( ! function_exists('time_ago') ) {
	function time_ago($date,$granularity=1) {
		// cleanup date
		$date = strtotime($date);
		$difference = time() - $date;
		$bUseUntil = false;

		if ($difference < 0) {
			$bUseUntil = true;
			$difference = abs($difference);
		}

		$periods = array(
			 'decade' => 315360000
			,'year'   => 31536000
			,'month'  => 2628000
			,'week'   => 604800
			,'day'    => 86400
			,'hour'   => 3600
			,'min'    => 60
			,'second' => 1
		);

		// less than 5 seconds ago, let's say "just now"
		if ($difference < 5) {
			$retval = "Just Now";
			return $retval;

		// less than 60 seconds ago, let's say "less than a minute"
		} elseif ($difference < 60) {
			$retval = "Less Than A Minute Ago";
			return $retval;

		// more accurate
		} else {
			$retval = '';
			foreach ($periods as $key => $value) {
				if ($difference >= $value) {
					$time = floor($difference/$value);
					$difference %= $value;
					$retval .= ($retval ? ' ' : '').$time.' ';
					$retval .= (($time > 1 && $key != 'min') ? $key.'s' : $key);
					$granularity--;
				}
				if ($granularity == '0') { break; }
			}

			if ($bUseUntil) {
				return ucwords('in '.$retval);
			} else {
				return ucwords($retval.' ago');
			}
		}
	}
}

if ( ! function_exists('convert_datetime') ) {
	function convert_datetime($date,$locale_format='',$convert_timezone='UTC',$current_timezone='America/New_York') {
		$date = new DateTime($date,new DateTimeZone($current_timezone));
		$date->setTimezone(new DateTimeZone($convert_timezone));

		if($locale_format == ''){
			return $date->format('Y-m-d H:i:s');
	   }
	   else{
		    return strftime($locale_format,strtotime($date->format('Y-m-d H:i:s')));
	   }

	}
}

if(!function_exists('convertto_local_datetime')){
	function convertto_local_datetime($datetime,$timezone = 'America/New_York',$format = '%x',$isTimestamp = false){
        $timezone = ($timezone != '' && $timezone != null) ? $timezone : 'America/New_York';
        //$datetime = str_replace('/', '-', $datetime);
        if ($isTimestamp) {
            $date = new DateTime('@' . $datetime, new DateTimeZone('UTC'));
        } else {
            $date = new DateTime($datetime, new DateTimeZone('UTC'));
        }
        $date->setTimezone(new DateTimeZone($timezone));
        //return strftime($format, strtotime($date->format('Y-m-d H:i:s')));
        $format = $format == '%x' ? LOCAL_DATE_FORMAT : ($format == '%x %X' ? LOCAL_DATE_FORMAT.' h:i a' : 'm/d/Y');
        return $date->format($format);
    }
}


if(!function_exists('convertto_utc_datetime')){
	function convert_utc_datetime($datetime,$timezone = 'America/New_York'){
        $timezone = ($timezone != '' && $timezone != null) ? $timezone : 'America/New_York';
        //$datetime = str_replace('/', '-', $datetime);
        $date = new DateTime($datetime, new DateTimeZone($timezone));
        $date->setTimezone(new DateTimeZone('UTC'));
        return $date->format("Y-m-d H:i:s");
	}
}
