import os
import uuid

from flask import Flask, request, render_template, jsonify

from utils import get_parsed_file

app = Flask(__name__)
IS_PROD = os.environ.get("IS_PROD", False)


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
            "error_message": str(e)
        }

    os.remove(tmp_filepath)
    return jsonify(response), 200


@app.route('/', methods=['GET'])
def main():
    ctx = {
        'is_prod': IS_PROD
    }
    return render_template("index.html", data=ctx)


@app.errorhandler(404)
def not_found(e):
    message = "404 We couldn't find the page"
    return render_template("index.html", error_message=message)


if __name__ == "__main__":
    app.run(debug=not IS_PROD, host="0.0.0.0", threaded=True)
