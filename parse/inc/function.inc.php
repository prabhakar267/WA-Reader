<?php
require_once 'constants.inc.php';


/**
 * main functionality function
 * @param  [string] $filename 
 * @return [array]
 */
function parseChatFile($filename){
    $error_flag = false;
    $errors = [];
    $chat = [];
    $names_array = [];

    $chat_file_path = 'conversations/' . $filename;

    if(!file_exists($chat_file_path)){
        addErrorMessage($errors, $error_flag, 'File 404<br>File is like a unicorn to our servers, file was not uploaded properly');
    } else {
        $file_handle = fopen($chat_file_path, "r+");

        if(!$file_handle){
            addErrorMessage($errors, $error_flag, 'Oh Snap!<br>Some technical glitch, it\'ll be resolved soon!');
        } else {
            $index = 0;
            global $MESSAGE_REGEX;
            global $STATUS_REGEX;
            global $MEDIA_REGEX;

            $current_chat_block = null;

            while (($line = fgets($file_handle)) !== false){
                $line_attributes = array();

                // Try matching for a new message.
                preg_match($MESSAGE_REGEX, trim($line), $line_attributes);

                // Match succeeded. It's a new message.
                if ($line_attributes != []) {
                    // Ignore media messages.
                    if (preg_match($MEDIA_REGEX, $line_attributes['message']))
                        continue;

                    if ($current_chat_block != null)
                        array_push($chat, $current_chat_block);

                    // Create the chat block.
                    $converted_timestamp = getConvertedTimestamp($line_attributes['timestamp']);
                    $timestamp = $converted_timestamp == false ? $line_attributes['timestamp'] : $converted_timestamp;
                    $userID = getUserIndex($names_array, $line_attributes['username']);
                    $message = htmlspecialchars($line_attributes['message']);

                    $current_chat_block = createChatBlock($timestamp, $userID, $message);

                // Match failed. It's not a new message. Needs further checking.
                } else {
                    // Try matching for a status message.
                    preg_match($STATUS_REGEX, trim($line), $line_attributes);

                    // Match succeeded. It's a status message.
                    if ($line_attributes != []) {
                        if ($current_chat_block != null)
                            array_push($chat, $current_chat_block);

                        // Generate the chat block.
                        $converted_timestamp = getConvertedTimestamp($line_attributes['timestamp']);
                        $timestamp = $converted_timestamp == false ? $line_attributes['timestamp'] : $converted_timestamp;
                        $message = htmlspecialchars($line_attributes['status']);
                    
                        // User ID for status messages is -1.
                        $current_chat_block = createChatBlock($timestamp, -1, $message);

                    // Match failed. It's a multi-line message.
                    } else {
                        // Should always be true, but just in case the file format changes.
                        if ($current_chat_block != null)
                            $current_chat_block['p'] .= '<br>' . htmlspecialchars(trim($line));
                    }
                }
            }
            if (!$first_message)
                array_push($chat, $current_chat_block);

            // close file handle
            fclose($file_handle);

            // delete file
            // yes, i respect privary
            unlink($chat_file_path);
        }
    }

    if(sizeof($chat) == 0) {
        addErrorMessage($errors, $error_flag, 'It wasn\'t a valid text file or we were not able to convert it');
    }

    $final_response = array(
        'success'   => !$error_flag,
    );

    if($error_flag){
        $final_response['errors']   = $errors;
    } else {
        $final_response['chat']     = $chat;
        $final_response['users']    = $names_array;
    }

    return $final_response;
}


/**
 * function used to create a new chat block.
 * @param [string] $timestamp The message's timestamp.
 * @param [integer] $user_id The user's ID.
 * @param [string] $message The chat message.
 */
function createChatBlock($timestamp, $user_id, $message) {
    return array(
        't' => $timestamp,
        'i' => $user_id,
        'p' => $message
    );
}


/**
 * function to get the timestamp from the messages
 * and check if they are continuation of previous messages
 * @param  [string]     $chat_string
 * @return [boolean]                    false when no time pattern is found
 *         [timestamp]                  timestamp when time pattern is successfully found
 */
function getConvertedTimestamp($chat_string){
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
 * @param  [array]  $users_array 
 * @param  [string] $user_name 
 * @return [integer]                index of the user passed in parameters
 */
function getUserIndex(&$users_array, $user_name){
    if(!in_array($user_name, $users_array)){
        array_push($users_array, $user_name);
    }

    $user_index = array_search($user_name, $users_array);
    return $user_index;
}


/**
 * function to neatly add error messages to error messages array
 * @param [array]   $error_messages_array
 * @param [boolean] $error_flag
 * @param [string]  $error_message
 */
function addErrorMessage(&$error_messages_array, &$error_flag, $error_message){
    array_push($error_messages_array, $error_message);
    $error_flag = true;
}


/**
 * function to get the URL of any page
 * @param  [boolean]    $mode   if mode is true, it removes the part after last slash and returns with trailing slash
 *                              if mode is false, it returns complete URL
 * @return [string]
 */
function getCurrentURL($mode=false){
    $url_name = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if($mode){
        $url_array = explode('/', $url_name);
        $last_index = sizeof($url_array) - 1;

        unset($url_array[$last_index]);

        $url_name = implode('/', $url_array);
        return $url_name . '/';
    } else {
        return $url_name;
    }
}