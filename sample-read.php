<?php
/**
 * @Author: prabhakar
 * @Date:   2016-06-14 23:36:37
 * @Last Modified by:   Prabhakar Gupta
 * @Last Modified time: 2016-06-14 23:58:57
 */

require_once 'inc/function.inc.php';
require_once 'inc/constants.inc.php';

$error_flag = false;
$errors = [];
$chat = [];
$names_array = [];

if(!isset($_GET['filename'])){
	add_error_message($errors, $error_flag, 'Invalid Request');
} else {
	$chat_file_path = 'conversations/' . $_GET['filename'];
	
	if(!file_exists($chat_file_path)){
		add_error_message($errors, $error_flag, 'File 404<br>File is like a unicorn to our servers, file was not uploaded properly');
	} else {
		$file_handle = fopen($chat_file_path, "r");
		
		if(!$file_handle){
			add_error_message($errors, $error_flag, 'Oh Snap!<br>Some technical glitch, it\'ll be resolved soon!');
		} else {
			$index = 0;

			while (($line = fgets($file_handle)) !== false){
				$line = explode('-', ($line));
				$timestamp = $line[0];

				$timestamp = returntimestamp($timestamp);

				if(!$timestamp){
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

					$final_chat_string = trim($line);

					$user_index = get_user_index($names_array, $name);
					if(strtolower($final_chat_string) == MEDIA_STRING)
						$final_string_to_be_printed = null;
					else
						$final_string_to_be_printed = htmlspecialchars($final_chat_string);

					$temp_element = [
						'index'	=> $user_index,
						'line'	=> $final_string_to_be_printed,
						'time'	=> $timestamp
					];

					array_push($chat, $temp_element);
				}
			}
			// close file handle
			fclose($file_handle);

			// delete file
			// yes, i respect privary
			unlink($chat_file_path);
		}
	}
}


$final_response = array(
	'success' 	=> !$error_flag,
);

if($error_flag){
	$final_response['errors'] 	= $errors;
} else {
	$final_response['chat'] 	= $chat;
	$final_response['users']	= $names_array;
}

echo json_encode($final_response, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);


