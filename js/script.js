function show_error_messages(errors_array){
    var errors_div = $('#errors');

    for(var i=0; i<errors_array.length; i++){
        var message = errors_array[i],
            error_html = '<div class="alert alert-danger alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button><strong>' + message + '</strong></div>';
        errors_div.html(error_html);
    }
    errors_div.removeClass('hidden');
}


function prepareUpload(event){
    files = event.target.files;
}


function uploadFiles(){
    event.stopPropagation();
    event.preventDefault();

    var data = new FormData(),
        submit_button = $('#submit_button')
        file_input = submit_button.parent('form').children('input[name="file"]');

    $.each(files, function(key, value){
        data.append(key, value);
    });

    $.ajax({
        url: 'upload-file.php',
        type: 'POST',
        data: data,
        cache: false,
        dataType: 'json',
        processData: false,
        contentType: false,
        
        success: function(response){
            if(response.success){
                var upload_prompt_div = $('#upload-prompt'),
                    conversation_div = $('#whatsapp-conversation'),
                    chat_div = conversation_div.find('#chat'),
                    users_div = conversation_div.find('#users_list');

                upload_prompt_div.hide();

                for(var chat in response.chat){
                    chat_index = response.chat[chat].i;
                    chat_line = response.chat[chat].p;
                    chat_time = response.chat[chat].t;

                    if(chat_line != null){
                        chat_line.replace(/(?:\r\n|\r|\n)/g, '<br>');   
                    } else {
                        chat_line = "*MEDIA HERE*";
                    }

                    if(chat_index % 2 == 0)
                        var chat_html = '<div class="aloo person' + chat_index + '"><div class="text">' + chat_line + '</div><div class="time">' + chat_time + '</div></div>';
                    else
                        var chat_html = '<div class="aloo person' + chat_index + ' left-margin-20"><div class="text">' + chat_line + '</div><div class="time">' + chat_time + '</div></div>';

                    chat_div.append(chat_html);
                }

                for(var user in response.users){
                    var user_html = '<span class="person' + user + '"><img src="img/default-user-image.png">' + response.users[user] + '</span>';
                    users_div.append(user_html);
                }
            } else {
                show_error_messages(response.errors);
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            errors = ['Some technical glitch! Please retry after reloading the page!'];
            show_error_messages(errors);

        }, 
        beforeSend: function(){
            submit_button.val('Getting Conversation');
            submit_button.attr('disabled', '');

            file_input.attr('disabled', '');
        },
        complete: function(){
            submit_button.val('Get Conversation');
            submit_button.removeAttr('disabled');

            file_input.removeAttr('disabled');
            $('#chat').minEmoji();
        }
    });
}


$(document).ready(function(){
    var files;
    
    $('form').on('submit', uploadFiles);
    $('input[type=file]').on('change', prepareUpload);

})

