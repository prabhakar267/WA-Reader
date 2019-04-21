<p align="center"><img src ="static/img/logo.png" width=300/></p>

# WA Reader
[![Build Status](https://travis-ci.org/prabhakar267/WA-Reader.svg?branch=master)](https://travis-ci.org/prabhakar267/WA-Reader)

WA Reader is a platform to read WhatsApp conversations from text backups in a easy-to-read UI. Built on `python-flask` server using `dateutil - powerful extension to datetime`.

## How to use WA Reader
 + Create a backup text file (`.txt file`)of your chat (if you don't have one already)
 + Generate backup file on: [Android](https://www.whatsapp.com/faq/en/android/23756533) | [iPhone](https://faq.whatsapp.com/en/iphone/20888066) | [Windows Phone](https://faq.whatsapp.com/en/wp/23607796) | [Nokia S40](https://faq.whatsapp.com/en/s40/21055286) | [BlackBerry](https://faq.whatsapp.com/en/bb/23574121) | [BlackBerry 10](https://faq.whatsapp.com/en/bb10/27571777)
 + Open [WA Reader](https://whatsapp-reader.herokuapp.com/) and follow the instructions

![](.github/screenshots/screencapture-whatsapp-reader-herokuapp-2019-04-21-20_31_51.png)


## Contribute
+ For reporting bug about an incorrect file not being processed, open a [new issue](https://github.com/prabhakar267/WA-Reader/issues).
+ PRs are always welcome to improve WA-Reader.


## Run WA Reader locally
Requires Python3 to run locally. See [instructions](https://www.python.org/downloads) to setup Python3.
+ Clone the project from source
```shell
git clone https://github.com/prabhakar267/WA-Reader && cd WA-Reader
```
+ Setup virtual environment
```shell
pip install virtualenv
virtualenv venv --python=python3.6
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

## Stargazers over time

[![Stargazers over time](https://starchart.cc/prabhakar267/WA-Reader.svg)](https://starchart.cc/prabhakar267/WA-Reader)


## Other "WhatsApp" and "WhatsApp Web" Hacks
 + [WhatsApp Emoticons](https://github.com/prabhakar267/whatsapp-emoticons)
