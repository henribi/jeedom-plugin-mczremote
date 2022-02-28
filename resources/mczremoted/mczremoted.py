# This file is part of Jeedom.
#
# Jeedom is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
# 
# Jeedom is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with Jeedom. If not, see <http://www.gnu.org/licenses/>.

# Code pour dialogue avec Jeedom et lancement de MczServer
# ! Utilise fichier globals.py pour transmisssion des parametres à MczServer



import os,re
import logging
#import threading
import sys
import argparse
import time
import datetime
import signal
import json
import traceback
import paho.mqtt.client as mqtt
#from logging.handlers import RotatingFileHandler, TimedRotatingFileHandler, WatchedFileHandler
import websocket

from pprint import pprint
import socketio

import globals

try:
    from jeedom.jeedom import *
except ImportError:
    print("Error: importing module from jeedom folder")
    sys.exit(1)

## Code removed by author due to non respone by Jeedom
