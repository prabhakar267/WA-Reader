<?php
/**
 * @Author: prabhakar
 * @Date:   2016-04-26 13:58:12
 * @Last Modified by:   Prabhakar Gupta
 * @Last Modified time: 2016-06-14 22:59:52
 */

if(isset($_FILES['file'])){
	$errors = "";
	$error_trigger = 0;
	$file_name = $_FILES['file']['name'];
	$file_tmp = $_FILES['file']['tmp_name'];

	if($_FILES["file"]["type"] !== "text/plain"){
		$errors ="<p style=\"width:100%;height:30px;text-align:center\">Upload onother file, extension not allowed.</p>";
	}

	if(empty($errors)){
		move_uploaded_file($file_tmp, "conversations/" . $file_name);
		header('Location: sample-read.php?filename=' . $file_name);
	} else {
		$error_trigger = 1;
	}
}

require 'inc/header.inc.php';
require 'inc/navbar.inc.php';

if(isset($_GET["error"]) && $_GET["error"] == 1){
	echo "<p style=\"width:100%;height:30px;text-align:center\">No alterations with code Plz!</p>";
}

if(isset($error_trigger) && $error_trigger == 1){
	echo($errors);
}

?>

<div class="container">
	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<div class="upload-popup">
				Upload Whatsapp Text (.txt) file and view chat in a readable format<br>
				<form action="" method="POST" enctype="multipart/form-data">
					<input type="file" name="file">
					<input type="submit" class="btn btn-warning" value="Get Conversation">
				</form>
			</div>
		</div>
	</div>
</div>

<?php

require 'inc/footer.inc.php';

?>
