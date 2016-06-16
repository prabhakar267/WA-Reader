<?php

	$static_instruction_strings = array(
		"txt"       => '<a href="https://en.wikipedia.org/wiki/TXT" target="_blank">.txt</a>',
		"prabhakar" => '<a href="http://www.prabhakargupta.com" target="_blank">Prabhakar Gupta</a>'
	);

?>
<!doctype html>
<html>
<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="author" content="prabhakar gupta">

	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<title>WhatsApp Reader | By Prabhakar Gupta</title>
	<link rel="shortcut icon" href="img/whatsapp.png">
</head>
<body>
<a href="https://github.com/prabhakar267/whatsapp-reader" target="_blank">
	<img src="img/right-dusk-blue%402x.png" class="github-image visible-lg">
</a>

<nav class="navbar navbar-fixed-top navbar-inverse">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="index.php"> Whatsapp Reader </a>
		</div>

		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav navbar-left">
				<li><a data-toggle="modal" data-target="#myModal">Instructions</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Whatsapp Reader</h4>
			</div>
			<div class="modal-body text-justify">
				<strong>WhatsApp Reader</strong> is a simple PHP based web-app to easily see your chats with your friends in a familiar user interface of WhatsApp, because reading from the backup text file is too boring.<br>It asks you to upload the "<?php echo $static_instruction_strings['txt'];?>" file of your chat. You can get the "<?php echo $static_instruction_strings['txt'];?>" file from the <b>Email Conversation</b> button in your WhatsApp.<br>
				<i><small>It may not be 100% correct, since I simply built this to read a piece of chat I fonud in my Inbox, and I hope it is useful for several others like me.</small></i>
				<hr>
				Here are some questions, you might ask before using this:
				<ul>
					<li>
						<strong>How do I export my chat history from WhatsApp?</strong><br>
						<pre><a href="http://www.whatsapp.com/faq/en/s60/21055276" target="_blank">Answer</a></pre>
					</li>
					<li>
						<strong>How do I save my chat history from WhatsApp?</strong><br>
						<pre><a href="http://www.whatsapp.com/faq/en/wp/22548236" target="_blank">Answer</a></pre>
					</li>
					<li>
						<strong>Is my chat getting saved?</strong><br>
						<pre>No. Your chat gets deleted as soon as you upload the File. You can feel safe on that front</pre>
					</li>
					<li>
						<strong>Can I preview my chat if I have uploaded it once?</strong><br>
						<pre>No. Since your chat is getting deleted as soon as you upload the File, you cannot preserve your chat history for you to visit later.</pre>
					</li>
					<li>
						<strong>How do I thank the person behind this awesome app?</strong><br>
						<pre>Simple. Find him here.<br><?php echo $static_instruction_strings['prabhakar'];?></pre>
					</li>
				</ul>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div class="container">
	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<div class="upload-popup">
				Upload Whatsapp Text (.txt) file and view chat in a readable format<br>
				<form enctype="multipart/form-data">
					<input type="file" name="file" required>
					<input type="submit" class="btn btn-warning" value="Get Conversation">
				</form>
			</div>
		</div>
	</div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>



<script type="text/javascript">

$('form').on('submit', uploadFiles);

var files;

$('input[type=file]').on('change', prepareUpload);

function prepareUpload(event){
	files = event.target.files;
}

// Catch the form submit and upload the files
function uploadFiles(event){
	event.stopPropagation(); // Stop stuff happening
	event.preventDefault(); // Totally stop stuff happening

	var data = new FormData();
	$.each(files, function(key, value)
	{
		data.append(key, value);
	});

	$.ajax({
		url: 'upload-file.php',
		type: 'POST',
		data: data,
		cache: false,
		dataType: 'json',
		processData: false, // Don't process the files
		contentType: false, // Set content type to false as jQuery will tell the server its a query string request
		
		success: function(response){
			console.log(response);
		},
		error: function(jqXHR, textStatus, errorThrown){
			console.log('ERRORS: ' + textStatus);
		}
	});
}
</script>

</body>
</html>
