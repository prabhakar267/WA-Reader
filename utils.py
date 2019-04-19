import io
import os

from dateutil.parser import parse as parse_datetime

def _get_parsed_line(line, persons_list):
	items = line.split("-")
	time_string = items[0]
	line = "-".join(items[1:]).strip()
	items = line.split(":")
	user_name = items[0]

	if user_name and user_name not in persons_list:
		persons_list.append(user_name)
	text_string = ":".join(items[1:]).strip()

	obj = {
		"t": parse_datetime(time_string),
		"p": text_string,
		"i": persons_list.index(user_name),
	} 
	return obj, persons_list

def get_parsed_file(filepath):
	if not os.path.exists(filepath):
		raise Exception("File not uploaded properly. Try Again!")
	parsed_chats = []
	persons_list = []
	with io.open(filepath, "r", encoding='utf-8') as f:
		for line in f:
			try:
				parsed_line, persons_list = _get_parsed_line(line.strip(), persons_list)
				parsed_chats.append(parsed_line)
			except ValueError:
				if len(parsed_chats) == 0:
					raise Exception("It wasn't a valid text file or we were not able to convert it")
				else:
					# continution message from last message
					parsed_chats[-1]["p"] += "\n{}".format(line.strip())
	return parsed_chats, persons_list
