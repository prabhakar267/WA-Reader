<?php

ob_start();

require_once 'inc/function.inc.php';
require_once 'inc/constants.inc.php';


if(!isset($_GET['filename'])){
	header("Location: index.php?error=1");
}

$filename = 'conversations/' . $_GET['filename'];

header('Content-type: text/html; charset=UTF-8');

	// require 'inc/header.inc.php';
	// require 'inc/navbar.inc.php';


$chat = [];

if(!fopen($filename, "r")){
	header('Location: index.php');
} else {
	$handle = fopen($filename, "r");
}
$names_array = array();

if($handle){
	$index = 0;
	while (($line = fgets($handle)) !== false){
		$line = explode('-', ($line));
		$timestamp = $line[0];

		$timestamp = returntimestamp($timestamp);

		if($timestamp == false){
			$line = implode('-', $line);
			$last_element_index = sizeof($chat) - 1;

			$chat[$last_element_index]['line'] .= '\n' . $line; 
		} else {
			unset($line[0]);
			$line = implode('-', $line);

			$line = explode(':', trim($line));
			$name = trim($line[0]);
			unset($line[0]);
			$line = implode(':', $line);

			if(in_array($name, $names_array) == false){
				array_push($names_array, $name);
			}
			$index = array_search($name, $names_array);

			if(strtolower(trim($line)) == MEDIA_STRING)
				$final_string_to_be_printed = null;
			else
				$final_string_to_be_printed = htmlspecialchars($line);

			$temp_element = [
				'index'	=> $index,
				'line'	=> $final_string_to_be_printed,
				'time'	=> $timestamp
			];

			array_push($chat, $temp_element);
		}
	}
	fclose($handle);
}

echo json_encode($chat, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);

