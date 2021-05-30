## Run WA Reader locally
Requires Python3 to run locally. See [instructions](https://www.python.org/downloads) to setup Python3.
+ Clone the project from source
```shell
git clone https://github.com/prabhakar267/WA-Reader && cd WA-Reader
```
+ Setup virtual environment
```shell
pip install virtualenv
virtualenv venv --python=python3.7
source venv/bin/activate
```
+ Install all dependencies
```shell
pip install -r requirements.txt
```
+ Run server
```
python app.py
```
Open [localhost:5000](http://localhost:5000)
