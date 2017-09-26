<?php

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
