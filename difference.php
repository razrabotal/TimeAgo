	public function dateDifference($past, $now = "now") {
		$seconds = 0;
		$minutes = 0;
		$hours = 0;
		$days = 0;
		$months = 0;
		$years = 0;

		date_default_timezone_set($this->timezone);


		$past = strtotime($past);
		$now = strtotime($now);
		$timeDifference = $now - $past;

		if($timeDifference >= 0) {
		switch($timeDifference) {

		case ($timeDifference >= $this->secondsPerYear):
			$years = floor($timeDifference / $this->secondsPerYear);
			$timeDifference = $timeDifference-($years * $this->secondsPerYear);

			case ($timeDifference >= $this->secondsPerMonth && $timeDifference <= ($this->secondsPerYear-1)):
				$months = floor($timeDifference / $this->secondsPerMonth);
				$timeDifference = $timeDifference-($months * $this->secondsPerMonth);

			case ($timeDifference >= $this->secondsPerDay && $timeDifference <= ($this->secondsPerYear-1)):
				$days = floor($timeDifference / $this->secondsPerDay);
				$timeDifference = $timeDifference-($days * $this->secondsPerDay);

			case ($timeDifference >= $this->secondsPerHour && $timeDifference <= ($this->secondsPerDay-1)):
				$hours = floor($timeDifference / $this->secondsPerHour);
				$timeDifference = $timeDifference-($hours * $this->secondsPerHour);

			case ($timeDifference >= $this->secondsPerMinute && $timeDifference <= ($this->secondsPerHour-1)):
				$minutes = floor($timeDifference / $this->secondsPerMinute);
				$timeDifference = $timeDifference-($minutes * $this->secondsPerMinute);

			case ($timeDifference <= ($this->secondsPerMinute-1)):
				$seconds = $timeDifference;
			}
		}

		$difference = array(
		  "years" => $years,
		  "months" => $months,
		  "days" => $days,
		  "hours" => $hours,
		  "minutes" => $minutes,
		  "seconds" => $seconds
		);

		return $difference;
	}