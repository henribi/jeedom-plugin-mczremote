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
#

import time
import logging
import threading
import _thread as thread
import requests
import datetime
import collections
import os
from os.path import join
import socket
from queue import Queue
import socketserver
from socketserver import (TCPServer, StreamRequestHandler)
import signal
import unicodedata
import pyudev
from logging.handlers import  WatchedFileHandler
import globals

# ------------------------------------------------------------------------------
## Code removed by author due to non respone by Jeedom
