<?php
/**
 * @Author: prabhakar
 * @Date:   2016-06-15 00:15:08
 * @Last Modified by:   Prabhakar Gupta
 * @Last Modified time: 2016-06-15 01:07:57
 */

require_once 'inc/function.inc.php';

$api_url = "whatsapp-reader.php?filename=";

// $errors = "";
// $error_trigger = 0;

$file_name = $_FILES[0]['name'];
$file_tmp = $_FILES[0]['tmp_name'];

if($_FILES[0]["type"] !== "text/plain"){
	$is_filetype_error = true;
	// echo 'somethin';
	// "<p style=\"width:100%;height:30px;text-align:center\">Upload onother file, extension not allowed.</p>";
} else {
	move_uploaded_file($file_tmp, "conversations/" . $file_name);
	
	$url = get_current_url(true) . $api_url . $file_name;
	$response = whatsapp_reader($file_name);
	echo json_encode($response, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
}
