import io
import os
import uuid

from flask import Flask, request, render_template, jsonify
from werkzeug import secure_filename

from utils import get_parsed_file

app = Flask(__name__)

@app.route('/parse-file', methods=['POST'])
def parse_file():
	file = request.files['0']
	filename = str(uuid.uuid4())
	tmp_filepath = os.path.join("conversations", filename)
	file.save(tmp_filepath)
	
	try:
		parsed_items, persons_list = get_parsed_file(tmp_filepath)
		response = {
			"success": True,
			"chat": parsed_items,
			"users": persons_list
		}
	except Exception as e:
		response = {
			"success": False,
			"errors": [str(e)]
		}

	os.remove(tmp_filepath)	
	return jsonify(response), 200

@app.route('/', methods=['GET'])
def main():
	return render_template("index.html")


if __name__ == "__main__":
	app.run(debug=True, host="0.0.0.0", threaded=True)