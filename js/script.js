/*
* @Author: prabhakar
* @Date:   2016-06-16 23:43:25
* @Last Modified by:   Prabhakar Gupta
* @Last Modified time: 2016-06-16 23:56:06
*/


function show_error_messages(errors_array){
	var errors_div = $('#errors');

	for(var i=0; i<errors_array.length; i++){
		var message = errors_array[i],
			error_html = '<div class="alert alert-danger alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button><strong>' + message + '</strong></div>';
		errors_div.append(error_html);
		console.log(error_html);
	}
	errors_div.removeClass('hidden');
}


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
			console.log(response.errors);
			// response = jQuery.parseJSON(response);
			// type
			show_error_messages(response.errors);
		},
		error: function(jqXHR, textStatus, errorThrown){
			console.log('ERRORS: ' + textStatus);
		}
	});
}