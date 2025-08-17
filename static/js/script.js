var currentFile = {},
    currentMediaFiles = {},
    waParser = new WAParser(),
    file_extensions_img = ['jpg', 'png'],
    file_extensions_audio = ['opus'],
    file_extensions_video = ['mp4'];

function show_error_message(error_message) {
    error_div.html(error_message);
    error_div.show();
    error_div.fadeTo(5000, 500).slideUp(500);
}

function submitForm(event) {
    event.stopPropagation();
    event.preventDefault();
    if (typeof files != 'undefined' && files.length > 0) {
        processFiles(event);
    } else {
        show_error_message('Please upload a file to proceed.');
    }
}

function prepareUpload(event) {
    files = event.target.files;
    // Update the label to show selected file name
    if (files.length > 0) {
        $('.custom-file-label').text(files[0].name);
    }
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
    return _extensions.some(v => _filename.includes(v))
}

async function processFiles(event) {
    const submit_button = $('#submit_button');
    const file_input = $('#form_file_field');
    
    // Clean up previous media files
    if (currentMediaFiles) {
        waParser.cleanupMediaFiles(currentMediaFiles);
        currentMediaFiles = {};
    }

    try {
        // Update UI to show processing
        submit_button.val('Processing...');
        submit_button.attr('disabled', '');
        file_input.attr('disabled', '');

        // Parse the file using our client-side parser
        const file = files[0];
        const result = await waParser.parseFile(file);
        
        // Store the results
        setFile({ 'chat': result.chat, 'users': result.users });
        if (result.mediaFiles) {
            currentMediaFiles = result.mediaFiles;
        }

        // Hide intro and show chat
        intro_panels.hide();
        back_nav.show();
        download_link.show();

        console.log("Chat Block count:" + result.chat.length);
        console.log("Users count:" + result.users.length);
        console.log("Are attachments present:" + result.attachments);

        // Render the chat
        renderChat(result);

    } catch (error) {
        show_error_message(error.message);
    } finally {
        // Reset UI
        submit_button.val('Get Conversation');
        submit_button.removeAttr('disabled');
        file_input.removeAttr('disabled');
    }
}

function renderChat(response) {
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
            temp_str = response.chat[chat_index].p
            if (response.chat[chat_index].m){
                file_path = response.chat[chat_index].mp
                if (file_path) {
                    // Extract filename from the original message text to get the correct extension
                    var originalText = response.chat[chat_index].p;
                    var fileNameMatch = originalText.match(/([A-Z]{3}-\d{8}-WA\d{4}\.\w+)/);
                    var file_extension = '';
                    
                    if (fileNameMatch) {
                        file_extension = fileNameMatch[1].split('.').pop().toLowerCase();
                    } else {
                        // Fallback: try to get extension from blob URL (though this might not work)
                        file_extension = file_path.split('.').pop().toLowerCase();
                    }

                    if (file_type_checker(file_extension, file_extensions_video) == true) {
                        $("div.video_holder", "#" + chat_div_id).html("<video controls><source src='" + file_path +"' type='video/mp4'>Your browser does not support the video tag.</video>")
                    } else if (file_type_checker(file_extension, file_extensions_audio) == true) {
                        $("div.audio_holder", "#" + chat_div_id).html("<audio controls><source src='" + file_path +"' type='audio/aac'>Your browser does not support the audio tag.</audio>")
                    } else if (file_type_checker(file_extension, file_extensions_img) == true) {
                        $("div.image_holder", "#" + chat_div_id).html("<img src='" + file_path +"' />")
                    } else {
                        console.log("Unknown attachment type " + file_extension + " for file: " + (fileNameMatch ? fileNameMatch[1] : 'unknown'))
                        $("div.text", "#" + chat_div_id).text("Unable to handle this attachment yet");
                    }
                } else {
                    $("div.text", "#" + chat_div_id).text(response.chat[chat_index].p);
                }
            } else {
                $("div.text", "#" + chat_div_id).text(response.chat[chat_index].p);
            }
        } else {
            arr_links = linkify.find(response.chat[chat_index].p);
            if (arr_links.length > 0){
                textline = response.chat[chat_index].p;
                for (var i = 0, l = arr_links.length; i < l; i++) {
                    textline = textline.replace(arr_links[i].value, '<a href="'+arr_links[i].href+'" target="_blank">'+arr_links[i].value+'</a>')    
                }
                $("div.text", "#" + chat_div_id).html(textline);
            }
            else {
                $("div.text", "#" + chat_div_id).text(response.chat[chat_index].p);
            }
        }

        $("div.time", "#" + chat_div_id).text(new Date(response.chat[chat_index].t).toLocaleString());
        last_user_index = chat_user_index;
    }
    
    // Apply emoji rendering
    $('#chat').minEmoji();
}


function restoreForm(event) {
    event.preventDefault();
    
    // Clean up media files
    if (currentMediaFiles) {
        waParser.cleanupMediaFiles(currentMediaFiles);
        currentMediaFiles = {};
    }
    
    chat_div.empty();
    users_div.empty();
    back_nav.hide();
    download_link.hide()
    intro_panels.show();
    form_file_field[0].value = "";
    $('.custom-file-label').text('Choose file');
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
