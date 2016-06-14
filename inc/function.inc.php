<?php
/**
 * @Author: prabhakar
 * @Date:   2016-06-14 22:54:37
 * @Last Modified by:   Prabhakar Gupta
 * @Last Modified time: 2016-06-15 00:02:06
 */

/**
 * function to get the timestamp from the messages
 * and check if they are continuation of previous messages
 * @param  [string]		$chat_string
 * @return [boolean]					false when no time pattern is found
 *         [timestamp]					timestamp when time pattern is successfully found
 */
function returntimestamp($chat_string){
	$pattern = "(?<time_hour>(\d)*)"
			. "(:)+(?<time_minute>(\d)*)"
			. "(?<time_type>AM|PM)?"
			. "(,)+( )*(?<date_day>(\d)*)"
			. "( )*(?<date_month>(\w)*)";
	$matches = array();

	if(preg_match("/^" . $pattern . "$/i", trim($chat_string), $matches) > 0) {
		$time_hour = floatval($matches['time_hour']);
		$time_minute = $matches['time_minute'];
		$time_type = $matches['time_type'];
		$date_day = $matches['date_day'];
		$date_month = $matches['date_month'];
	} else {
		$pattern = "(?<date_year>(\d)*)"
			. "(\/)+(?<date_month>(\d)*)"
			. "(\/)+(?<date_day>(\d)*)"
			. "(,)+( )*(?<time_hour>(\d)*)"
			. "(:)*(?<time_minute>(\d)*)"
			. "( )*(?<time_type>AM|PM)*";
		$matches = array();
		if(preg_match("/^" . $pattern . "$/i", trim($chat_string), $matches) > 0) {
			$time_hour = intval($matches['time_hour']);

			if(!isset($matches['time_type'])){
				if($time_hour >= 12)
					$time_type = "PM";
				else
					$time_type = "AM";
			} else {
				$time_type = $matches['time_type'];
			}

			if($time_hour > 12)
				$time_hour = $time_hour - 12;

			$time_minute = $matches['time_minute'];
			$date_year = intval($matches['date_year']);
			$date_day = $matches['date_day'];
			$date_month = $matches['date_month'];
		} else {
			// case where time pattern was not found
			return false;
		}
	}

	if(isset($date_year))
		$timestamp = $date_year;
	else
		$timestamp = date('Y', time());

	$timestamp .= '-' . $date_month . '-' . $date_day . " " . $time_hour . ':' . $time_minute . ' ' . $time_type;
	return strtotime($timestamp);
}


/**
 * function to track all the users involved in the chat
 * @param  [array]	$users_array 
 * @param  [string]	$user_name 
 * @return [integer]				index of the user passed in parameters
 */
function get_user_index(&$users_array, $user_name){
	if(!in_array($user_name, $users_array)){
		array_push($users_array, $user_name);
	}

	$user_index = array_search($user_name, $users_array);
	return $user_index;
}


/**
 * function to neatly add error messages to error messages array
 * @param [array]	$error_messages_array
 * @param [boolean]	$error_flag
 * @param [string]	$error_message
 */
function add_error_message(&$error_messages_array, &$error_flag, $error_message){
	array_push($error_messages_array, $error_message);
	$error_flag = true;
}

