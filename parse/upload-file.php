<?php

header('Content-Type: application/json');
http_response_code(200);
/**
reference : http://php.net/manual/en/function.error-reporting.php

Set $error_reporting_flag to "0" when no error reporting is required
Set it to "E_ERROR | E_WARNING | E_PARSE" for runtime errors
Set it to "E_ALL" for all errors (including Notices)
**/

$error_reporting_flag = 0;
// $error_reporting_flag = E_ERROR | E_WARNING | E_PARSE;
// $error_reporting_flag = E_ALL;

error_reporting($error_reporting_flag);


require_once 'inc/function.inc.php';


$final_response = array();
$error_flag = false;
$errors = [];


if(isset($_FILES[0]['type'])){
    if($_FILES[0]['type'] != 'text/plain'){
        addErrorMessage($errors, $error_flag, 'Invalid file type uploaded!');
    } else {
        $file_name = $_FILES[0]['name'];
        $file_tmp = $_FILES[0]['tmp_name'];

        move_uploaded_file($file_tmp, "conversations/" . $file_name);
        $response = parseChatFile($file_name);
    }
} else {
    addErrorMessage($errors, $error_flag, 'Invalid request!');
}

if(isset($response)){
    $final_response = $response;
} else {
    $final_response['success'] = !$error_flag;
    $final_response['errors']   = $errors;
}


echo json_encode($final_response, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
