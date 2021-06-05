var currentFile = {}

function show_error_message(error_message) {
    error_div.html(error_message);
    error_div.show();
    error_div.fadeTo(5000, 500).slideUp(500);
}

function submitForm(event) {
    event.stopPropagation();
    event.preventDefault();
    if (typeof files != 'undefined') {
        uploadFiles(event);
    } else {
        show_error_message('Please upload a file to proceed.');
    }
}

function prepareUpload(event) {
    files = event.target.files;
}

function download() {
    var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(currentFile));
    var dlAnchorElem = document.createElement('a')
    dlAnchorElem.setAttribute("href", dataStr);
    dlAnchorElem.setAttribute("download", "chat.json");
    dlAnchorElem.click();
}

function setFile(json) {
    currentFile = json;
}
function file_type_checker(_filename, _extensions){
    if (_extensions.some(v => _filename.includes(v))) {
        //console.log("Match using '" + _filename + "'");
        return true
    } else {
        //console.log("No match using '" + _filename + "'");
        return false
    }
}

function uploadFiles(event) {
    var data = new FormData(),
        submit_button = $('#submit_button')
    file_input = submit_button.parent('form').children('input[name="file"]');

    $.each(files, function(key, value) {
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

        success: function(response) {
            if (response.success) {
                intro_panels.hide();
                back_nav.show();
                setFile({ 'chat': response.chat, 'users': response.users })
                download_link.show();

                console.log("Chat Block count:" + response.chat.length);
                console.log("Users count:" + response.users.length);
                console.log("Attachments:" + response.attachments);
                var last_user_index = -1;
                for (var chat_index in response.chat) {
                    var chat_div_id = "chatBox" + chat_index,
                        chat_user_index = response.chat[chat_index].i,
                        chat_html = '<div class="aloo" id="' + chat_div_id + '"><div class="user"></div><div class="text"></div><div class="image_holder"></div><div class="video_holder"></div><div class="audio_holder"></div><div class="time"></div></div>';

                    chat_div.append(chat_html);
                    if (chat_user_index == 1)
                        $("#" + chat_div_id).addClass("alternate-user");

                    if (last_user_index != chat_user_index) {
                        $("div.user", "#" + chat_div_id).text(response.users[chat_user_index]);
                        $("#" + chat_div_id).addClass("new-user-block");
                    }
                    if (response.attachments == true){
                        temp_str= response.chat[chat_index].p
                        //Sample formats seen
                        // 28.05.21, 22:21 - xxxx: ‎IMG-20210528-WA0007.jpg (Datei angehängt)
                        // ‎[04.08.19, 17:56:01] yyyy zzzz: ‎<Anhang: 00000016-PHOTO-2019-08-04-17-56-01.jpg>
                        if (temp_str.search("<(.*):.([0-9]{8})-(.*)-([0-9]{4})-([0-9]{2})-([0-9]{2})-([0-9]{2})-([0-9]{2})-([0-9]{2})(.*)>") > -1 || temp_str.search("-([0-9]{8})-(WA.*)\.(.*)\((.*)\)") > -1){
                            filename_match = ''
                            try{
                                filename_match = temp_str.match(/([\w\d\-.]+\.[\w\d]{3,4})/)[1];
                            }
                            catch(err) {
                                console.log("Filename extract failed")
                              }
                            var file_extensions_img = ['.jpg', '.png'];
                            var file_extensions_audio = ['.opus'];
                            var file_extensions_video = ['.mp4'];

                            if (file_type_checker(filename_match, file_extensions_video) == true){
                                //console.log("video mp4")
                                $("div.video_holder", "#" + chat_div_id).html("<video controls><source src='/static/chat/" + filename_match +"' type='video/mp4'>Your browser does not support the video tag.</video>")
                            } 
                            else if (file_type_checker(filename_match, file_extensions_audio) == true){
                                //console.log("audio opus")
                                $("div.audio_holder", "#" + chat_div_id).html("<audio controls><source src='/static/chat/" + filename_match +"' type='audio/aac'>Your browser does not support the audio tag.</audio>")
                            } 
                            else if (file_type_checker(filename_match, file_extensions_img) == true){
                                //console.log("image")
                                $("div.image_holder", "#" + chat_div_id).html("<img src='/static/chat/" + filename_match +"' />")
                            }                         
                            else {        
                                console.log("unknown attachment")
                                $("div.text", "#" + chat_div_id).text("This attachemnt I can't handle yet:" +filename_match );
                            }
                        } else {
                            //console.log("text base")
                            $("div.text", "#" + chat_div_id).text(response.chat[chat_index].p);
                        }
                    } else {
                        //console.log("text")
                        $("div.text", "#" + chat_div_id).text(response.chat[chat_index].p);
                    }
                    
                    
                    $("div.time", "#" + chat_div_id).text(response.chat[chat_index].t);
                    last_user_index = chat_user_index;
                }
            } else {
                show_error_message(response.error_message);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            error_message = 'Some technical glitch! Please retry after reloading the page!';
            show_error_message(error_message);
        },
        beforeSend: function() {
            submit_button.val('Processing...');
            submit_button.attr('disabled', '');

            file_input.attr('disabled', '');
        },
        complete: function() {
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
    download_link.hide()
    intro_panels.show();
    form_file_field[0].value = "";
}


$(document).ready(function() {
    $('form').on('submit', submitForm);
    download_link.children('a').on('click', download);
    $('input[type=file]').on('change', prepareUpload);
    $('.nav-back').click(restoreForm);
})


var files,
    intro_panels = $('.intro-panels'),
    conversation_div = $('#whatsapp-conversation'),
    chat_div = conversation_div.find('#chat'),
    users_div = conversation_div.find('#users_list'),
    form_file_field = $('#form_file_field'),
    error_div = $('#error_message_box'),
    back_nav = $('li.nav-back'),
    download_link = $('li.download-link');