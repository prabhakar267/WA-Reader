import os
import uuid

from flask import Flask, request, render_template, jsonify, redirect, url_for

from utils import get_parsed_file

app = Flask(__name__)
IS_PROD = os.environ.get("IS_PROD", False)


def allowed_file(filename):
    allowed_filetypes = ['txt', 'json']
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in allowed_filetypes


@app.route('/parse-file', methods=['POST'])
def parse_file():
    file = request.files['0']
    if not allowed_file(file.filename):
        response = {
            "success": False,
            "error_message": "Please upload a valid file!",
        }
    else:
        filename, file_extension = os.path.splitext(file.filename)
        filename = str(uuid.uuid4())
        tmp_filepath = os.path.join("conversations", filename + file_extension)
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
                "error_message": str(e)
            }

        os.remove(tmp_filepath)
    return jsonify(response), 200


@app.route('/', methods=['GET'])
def main():
    ctx = {
        'is_prod': IS_PROD
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
