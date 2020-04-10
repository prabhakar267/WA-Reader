# Troubleshooting
Please add if you faced some issue and got it fixed.

+ [Python path doesn't exist](#python-path-doesnt-exist)

### Python path doesn't exist
```
The path python3.7 (from --python=python3.7) does not exist
```
If you are getting the above error while setting up the virtual environment, you probably have some other version of Python installed than Python3.7. Follow the following steps to know the version of Python installed on your system.
+ `python3 --version` or `python --version`
If your output to both the commands is of form other than `Python 3.X.Y`, you probably don't have python 3 installed on your system. [Download Python 3](https://www.python.org/downloads/)
+ `virtualenv venv --python=python3.X`

## Issue still not fixed?
Open a [new issue](https://github.com/prabhakar267/WA-Reader/issues/new)
