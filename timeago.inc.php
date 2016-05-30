<?php
function timeAgoInWords($timestring, $timezone = NULL) {
	$timeAgo = new TimeAgo($timezone);
	return $timeAgo->inWords($timestring, "now");
}

class TimeAgo {
	private $secondsPerMinute = 60;
	private $secondsPerHour = 3600;
	private $secondsPerDay = 86400;
	private $secondsPerMonth = 2592000;
	private $secondsPerYear = 31104000;

	private static $timeAgoStrings = array(
		'lessThanAMinute' =>	"меньше минуты назад",
		'oneMinute' => 			"минуту назад",
		'lessThanOneHour' => 	"%s назад",
		'halfHour' => 			"полчаса назад",
		'aboutOneHour' => 		"час назад",
		'hours' => 				"%s назад",	
		'aboutOneDay' => 		"вчера",
		'days' => 				"%s назад",		
		'aboutOneMonth' => 		"около месяца назад",
		'months' => 			"%s назад",
		'aboutOneYear' => 		"год назад",
		'years' => 				"более %s назад"
	);

	private $timezone;
	public function __construct($timezone = NULL) {
		if($timezone == NULL) {
			$timezone = 'etc/localtime';
		}
		$this->timezone = $timezone;
	}
	
	private function _translate($label, $time = '') {
		return sprintf(self::$timeAgoStrings[$label], $time);
	}

	public function inWords($past, $now = "now") {		
		date_default_timezone_set($this->timezone);

		$past = strtotime($past);
		$now = strtotime($now);
		$timeDifference = $now - $past;
		
		$timeAgo = "";

		// Правило 1	40s < &
		if($timeDifference <= 40) {
			$timeAgo = $this->_translate('lessThanAMinute');
		}
		
		// Правило 2	41s < & < 1m 29s
		else if($timeDifference >= 41 && $timeDifference <= 89) {
			$timeAgo = $this->_translate('oneMinute');
		}
		
		// Правило 3	1m 29s < & < 59m 29s
		else if($timeDifference >= 90 && $timeDifference <= (($this->secondsPerMinute * 59) + 29)) {
			$minutes = round($timeDifference / $this->secondsPerMinute);
			switch($this->curretText($minutes)) {
				case (1): $minutes .= " минут"; break;
				case (0): $minutes .= " минуты"; break;
				case (-1): $minutes .= " минуту"; break;
			}
			if($minutes >= 27 && $minutes <= 33) {
				$timeAgo = $this->_translate('halfHour');
			} else {
				$timeAgo = $this->_translate('lessThanOneHour', $minutes);
			}
		}
		
		// Правило 4	59m 29s < & < 1h 59m 59s
		else if($timeDifference >= (($this->secondsPerMinute * 59) + 30) && 
				$timeDifference <= ($this->secondsPerHour + ($this->secondsPerMinute * 59) + 59)) {
			$timeAgo = $this->_translate('aboutOneHour');
		}
		
		// Правило 5	1h 59m 59s < & < 23h 59m 59s
		else if($timeDifference >= ($this->secondsPerHour * 2) &&
				$timeDifference <= (($this->secondsPerHour * 23) + ($this->secondsPerMinute * 59) + 59)) {
			$hours = round($timeDifference / $this->secondsPerHour);
			switch($this->curretText($hours)) {
				case (1): $hours .= " часов"; break;
				case (0): $hours .= " часа"; break;
				case (-1): $hours .= " час"; break;
			}
			$timeAgo = $this->_translate('hours', $hours);
		}
		
		// Правило 6	24h < & < 47h 59m 59s
		else if($timeDifference >= ($this->secondsPerHour * 24) &&
				$timeDifference <= (($this->secondsPerHour * 47) + ($this->secondsPerMinute * 59) +	59)) {
			$timeAgo = $this->_translate('aboutOneDay');
		}
		
		// Правило 7	48h < & < 29d 23h 59m 59s
		else if($timeDifference >= ($this->secondsPerHour * 48) &&
				$timeDifference <= (($this->secondsPerDay * 29) + ($this->secondsPerHour * 23) + ($this->secondsPerMinute * 59) + 59)) {
			$days = round($timeDifference / $this->secondsPerDay);
			switch($this->curretText($days)) {
				case (1): $days .= " дней"; break;
				case (0): $days .= " дня"; break;
				case (-1): $days .= " день";; break;
			}	
			$timeAgo = $this->_translate('days', $days);
		}
		
		// Правило 8	30d < & < 59d 23h 59m 59s
		else if($timeDifference >= ($this->secondsPerDay * 30) &&
				$timeDifference <= (($this->secondsPerDay * 59) + ($this->secondsPerHour * 23) + ($this->secondsPerMinute * 59) + 59)) {
			$timeAgo = $this->_translate('aboutOneMonth');
		}
		
		// Правило 9	60d < & < 1y
		else if($timeDifference >= ($this->secondsPerDay * 60) &&
				$timeDifference < $this->secondsPerYear ) {
			$months = round($timeDifference / $this->secondsPerMonth);	  
			if($months == 1) { $months = 2; }// потому что 1 месяц прошел
			switch($this->curretText($months)) {
				case (1): $months .= " месяцев"; break;
				case (0): $months .= " месяца"; break;
				case (-1): $months .= " месяц"; break;
			}			
			$timeAgo = $this->_translate('months', $months);
		}
		
		// Правило 10	1y < & < 2y
		else if( $timeDifference >= $this->secondsPerYear && $timeDifference < ($this->secondsPerYear * 2)) {
			$timeAgo = $this->_translate('aboutOneYear');
		}
		
		// Правило 10	2y < &
		else {
			$years = floor($timeDifference / $this->secondsPerYear);
			switch($this->curretText($years)) {
				case (1): $years .= " лет"; break;
				case (0): $years .= " года"; break;
				case (-1): $years .= " год"; break;
			}	
			$timeAgo = $this->_translate('years', $years);
		}
		return $timeAgo;
	}

	public function curretText($number) {
		if (($number%10) >= 5 || ($number%10) == 0 || ($number >= 10 && $number <= 20)) {
			return 1;
		} else if(($number%10) >= 2 ) {
			return 0;
		} else {
			return -1;
		}	
	}
}