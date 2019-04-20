function show_error_messages(errors_array){
    var errors_div = $('#errors');

    for(var i=0; i<errors_array.length; i++){
        var message = errors_array[i],
            error_html = '<div class="alert alert-danger alert-dismissible"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button><strong>' + message + '</strong></div>';
        errors_div.html(error_html);
    }
    errors_div.removeClass('hidden');
}

function submitForm (event) {
    event.stopPropagation();
    event.preventDefault();

    $('#errors').html('');
    
    if (typeof files != 'undefined') {
        uploadFiles(event);
    } else {
        show_error_messages(['Please upload a file to proceed.']);
    }
}

function prepareUpload(event){
    files = event.target.files;
}

function uploadFiles(event){
    var data = new FormData(),
        submit_button = $('#submit_button')
        file_input = submit_button.parent('form').children('input[name="file"]');

    $.each(files, function(key, value){
        data.append(key, value);
    });

    $.ajax({
        url: '/parse-file',
        type: 'POST',
        data: data,
        cache: false,
        dataType: 'json',
        processData: false,
        contentType: false,
        
        success: function(response){
            if(response.success){
                upload_prompt_div.hide();
                back_nav.show();

                console.log("Chat Block count:" + response.chat.length);
                console.log("Users count:" + response.users.length);
                for(var chat_index in response.chat){
                    var chat_div_id = "chatBox" + chat_index,
                        chat_user_index = response.chat[chat_index].i;
                    var chat_html = '<div class="aloo" id="'+ chat_div_id +'"><div class="user"></div><div class="text"></div><div class="time"></div></div>';

                    chat_div.append(chat_html);
                    if (chat_user_index == 1)
                        $("#" + chat_div_id).addClass("alternate-user");
                    $("div.user", "#" + chat_div_id).text(response.users[chat_user_index]);
                    $("div.text", "#" + chat_div_id).text(response.chat[chat_index].p);
                    $("div.time", "#" + chat_div_id).text(response.chat[chat_index].t);
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
            submit_button.val('Getting Conversation...');
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


function restoreForm(event) {
    event.preventDefault();
    
    chat_div.empty();
    users_div.empty();
    back_nav.hide();
    upload_prompt_div.show();
}


$(document).ready(function(){    
    $('form').on('submit', submitForm);
    $('input[type=file]').on('change', prepareUpload);
    $('.nav-back').click(restoreForm);
})


var files,
    upload_prompt_div = $('#upload-prompt'),
    conversation_div = $('#whatsapp-conversation'),
    chat_div = conversation_div.find('#chat'),
    users_div = conversation_div.find('#users_list'),
    logo_nav = $('.navbar-brand'),
    back_nav = $('li.nav-back');