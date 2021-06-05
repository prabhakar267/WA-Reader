import json
import os
import shutil

from dateutil.parser import parse as parse_datetime

from constants import DEFAULT_ERROR_MESSAGE, ATTACHMENT_MESSAGES

TIMESTAMP_SPLITTERS = ["-", "]", ": "]
REMOVE_CHARACTERS = ["[", "]", "(", ")", "{", "}", '\u200e', '\ufeff']


def empty_directory(directory_path):
    """
    Removes all the files in the given directory path
    """
    for filename in os.listdir(directory_path):
        file_path = os.path.join(directory_path, filename)
        try:
            if os.path.isfile(file_path) or os.path.islink(file_path):
                os.unlink(file_path)
            elif os.path.isdir(file_path):
                shutil.rmtree(file_path)
        except Exception as e:
            print('Failed to delete %s. Reason: %s' % (file_path, e))


def is_media_string(chat_string):
    for media_message in ATTACHMENT_MESSAGES:
        if media_message.lower() in chat_string.lower():
            return True, media_message
    return False, None


def get_media_path(text_string, media_flag):
    if media_flag:
        for text_segment in text_string.split():
            tmp_filepath = os.path.join("./static/chat", text_segment.strip().replace("\u200e", ""))
            if os.path.exists(tmp_filepath):
                return tmp_filepath[1:]


def _get_parsed_line(input_line, persons_list, is_media_available=False):
    timestamp_string = None
    for timestamp_splitter in TIMESTAMP_SPLITTERS:
        items = input_line.split(timestamp_splitter)

        dirty_timestamp_string = items[0]
        for remove_character in REMOVE_CHARACTERS:
            dirty_timestamp_string = dirty_timestamp_string.replace(remove_character, "")

        try:
            timestamp_string = parse_datetime(dirty_timestamp_string, dayfirst=True)
            line = timestamp_splitter.join(items[1:]).strip()
            break
        except (ValueError, OverflowError):
            continue

    if not timestamp_string:
        raise IndexError
    items = line.split(":")
    text_string = ":".join(items[1:]).strip()
    if not text_string:
        return None, persons_list

    user_name = items[0]
    if user_name and user_name not in persons_list:
        persons_list.append(user_name)

    media_path = None
    is_media_string_flag = False

    if is_media_available:
        # For .zip files
        is_media_string_flag, media_message = is_media_string(text_string)
        media_path = get_media_path(text_string, is_media_string_flag)
        if media_path:
            text_string = media_message

    chat_string_object = {
        "t": timestamp_string,
        "p": text_string,
        "i": persons_list.index(user_name),
        'm': is_media_string_flag,
        'mp': media_path,
    }

    return chat_string_object, persons_list


def get_parsed_file(filepath, is_media_available=False):
    if not os.path.exists(filepath):
        raise Exception("File not uploaded properly. Try Again!")
    filename, file_extension = os.path.splitext(filepath)
    if file_extension == '.json':
        with open(filepath, 'r') as f:
            try:
                chat_archive = json.load(f)
            except Exception:
                raise Exception(DEFAULT_ERROR_MESSAGE)
            if 'users' in chat_archive and 'chat' in chat_archive:
                return chat_archive['chat'], chat_archive['users']
            else:
                raise Exception(DEFAULT_ERROR_MESSAGE)

    parsed_chats = []
    persons_list = []
    with open(filepath, "r", encoding='utf-8') as f:
        for line in f:
            try:
                parsed_line, persons_list = _get_parsed_line(line.strip(), persons_list, is_media_available=is_media_available)
                if parsed_line:
                    parsed_chats.append(parsed_line)
            except IndexError:
                if len(parsed_chats) == 0:
                    raise Exception(DEFAULT_ERROR_MESSAGE)
                else:
                    # continuation message from last message
                    parsed_chats[-1]["p"] += "\n{}".format(line.strip())
    return parsed_chats, persons_list
