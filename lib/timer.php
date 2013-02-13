<?php

class timer {

	static public $timer = array();

	static public function set($key='_global') {
		$time = explode(' ', microtime());
		self::$timer[$key] = (double)$time[1] + (double)$time[0];
	}

	static public function get($key='_global') {
		$time  = explode(' ', microtime());
		$time  = (double)$time[1] + (double)$time[0];
		$timer = @self::$timer[$key];
		return round(($time-$timer), 5);
	}

}

timer::set();

?>