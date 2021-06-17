import os
import uuid
import zipfile

from flask import Flask, request, render_template, jsonify, redirect, url_for

from constants import CONTRIBUTION_LINK, DEFAULT_ERROR_MESSAGE
from utils import get_parsed_file, empty_directory

app = Flask(__name__)
IS_PROD = os.environ.get("IS_PROD", False)


def allowed_file(filename):
    allowed_filetypes = ['txt', 'json', 'zip']
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in allowed_filetypes


@app.route('/parse-file', methods=['POST'])
def parse_file():
    empty_directory("static/chat")

    file_req = request.files
    if len(file_req) == 0:
        response = {
            "success": False,
            "error_message": "Please upload a file to proceed.",
        }
        return jsonify(response), 200
    file = file_req['0']
    if not allowed_file(file.filename):
        response = {
            "success": False,
            "error_message": "Please upload a valid file!",
        }
    else:
        attachment_flag = False
        filename, file_extension = os.path.splitext(file.filename)
        filename = str(uuid.uuid4())
        tmp_filepath = os.path.join("conversations", filename + file_extension)
        file.save(tmp_filepath)

        if '.zip' == file_extension:
            with zipfile.ZipFile(tmp_filepath, 'r') as zip_ref:
                zip_ref.extractall("static/chat")
            os.remove(tmp_filepath)

            # Assumption that needs to be proven
            filename = '_chat'
            file_extension = '.txt'
            tmp_filepath = os.path.join("static/chat", filename + file_extension)
            attachment_flag = True

        try:
            parsed_items, persons_list = get_parsed_file(tmp_filepath, is_media_available=attachment_flag)
            response = {
                "success": True,
                "chat": parsed_items,
                "users": persons_list,
                "attachments": attachment_flag
            }
        except Exception as e:
            response = {
                "success": False,
                "error_message": str(e)
            }

        # clears out attachments and conversations
        empty_directory("conversations")
    return jsonify(response), 200


@app.route('/', methods=['GET'])
def main():
    empty_directory("static/chat")
    ctx = {
        'is_prod': IS_PROD,
        'contribution_link': CONTRIBUTION_LINK,
        'default_error_message': DEFAULT_ERROR_MESSAGE,
    }
    if request.args.get('redirect'):
        message = "Sorry, we couldn't find the page"
        return render_template("index.html", data=ctx, error_message=message)
    else:
        return render_template("index.html", data=ctx)


@app.errorhandler(404)
def not_found(e):
    return redirect(url_for('main', redirect='home'))


if __name__ == "__main__":
    app.run(debug=not IS_PROD, host="0.0.0.0", threaded=True)
