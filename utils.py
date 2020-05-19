import json
import os

from dateutil.parser import parse as parse_datetime

TIMESTAMP_SPLITTERS = ["-", "]", ": "]
REMOVE_CHARACTERS = ["[", "]", "(", ")", "{", "}", '\u200e', '\ufeff']


def _get_parsed_line(input_line, persons_list):
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
        except ValueError:
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
    obj = {
        "t": timestamp_string,
        "p": text_string,
        "i": persons_list.index(user_name),
    }
    return obj, persons_list


def get_parsed_file(filepath):
    if not os.path.exists(filepath):
        raise Exception("File not uploaded properly. Try Again!")
    filename, file_extension = os.path.splitext(filepath)
    if file_extension == '.json':
        with open(filepath, 'r') as f:
            try:
                chat_archive = json.load(f)
            except Exception:
                raise Exception("It wasn't a valid text file or we were not able to convert it")
            if 'users' in chat_archive and 'chat' in chat_archive:
                return chat_archive['chat'], chat_archive['users']
            else:
                raise Exception("It wasn't a valid text file or we were not able to convert it")
    parsed_chats = []
    persons_list = []
    with open(filepath, "r", encoding='utf-8') as f:
        for line in f:
            try:
                parsed_line, persons_list = _get_parsed_line(line.strip(), persons_list)
                if parsed_line:
                    parsed_chats.append(parsed_line)
            except IndexError:
                if len(parsed_chats) == 0:
                    raise Exception("It wasn't a valid text file or we were not able to convert it")
                else:
                    # continuation message from last message
                    parsed_chats[-1]["p"] += "\n{}".format(line.strip())
    return parsed_chats, persons_list
