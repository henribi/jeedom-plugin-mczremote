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

class PileFifo(object):
    def __init__(self, maxpile=None):
        self.pile = []
        self.maxpile = maxpile

    def empile(self, element, idx=0):
        if (self.maxpile != None) and (len(self.pile) == self.maxpile):
            raise ValueError("erreur: tentative d'empiler dans une pile pleine")
        self.pile.insert(idx, element)

    def depile(self, idx=-1):
        if len(self.pile) == 0:
            raise ValueError("erreur: tentative de depiler une pile vide")
        if idx < -len(self.pile) or idx >= len(self.pile):
            raise ValueError("erreur: element de pile à depiler n'existe pas")
        return self.pile.pop(idx)

    def element(self, idx=-1):
        if idx < -len(self.pile) or idx >= len(self.pile):
            raise ValueError("erreur: element de pile à lire n'existe pas")
        return self.pile[idx]

    def copiepile(self, imin=0, imax=None):
        if imax == None:
            imax = len(self.pile)
        if imin < 0 or imax > len(self.pile) or imin >= imax:
            raise ValueError("erreur: mauvais indice(s) pour l'extraction par copiepile")
        return list(self.pile[imin:imax])

    def pilevide(self):
        return len(self.pile) == 0

    def pilepleine(self):
        return self.maxpile != None and len(self.pile) == self.maxpile

    def taille(self):
        return len(self.pile)


# ----------------------------------------------------------------------------
Message_MQTT = PileFifo()
Message_WS = PileFifo()

# SIO CONNECT TO MCZ MAESTRO
sio = socketio.Client(logger=False, engineio_logger=False)

_INTERVALLE = 1
_TEMPS_SESSION = 60

MQTT_MAESTRO = {}

def send():
    #def run(*args):
        time.sleep(_INTERVALLE)
        if Message_MQTT.pilevide():
            Message_MQTT.empile("C|RecuperoInfo")
        cmd = Message_MQTT.depile()
        logging.info("Envoi de la commande : " + str(cmd))
        sio.emit(
            "chiedo",
            {
                "serialNumber": globals.MCZ_device_serial,
                "macAddress": globals.MCZ_device_MAC,
                "tipoChiamata": 1,
                "richiesta": cmd,
            },
        )

    #run()


def on_connect_mqtt(client, userdata, flags, rc):
    logging.info("Connecté au broker MQTT avec le code : " + str(rc))


def on_message_mqtt(client, userdata, message):
    logging.info('Message MQTT reçu : ' + str(message.payload.decode()))
    cmd = message.payload.decode().split(",")
    if (int(cmd[0])) < 9000:
        if cmd[0] == "42":
            cmd[1] = (int(cmd[1]))
        Message_MQTT.empile("C|WriteParametri|" + cmd[0] + "|" + str(cmd[1]))
        logging.info('Contenu Pile Message_MQTT : ' + str(Message_MQTT.copiepile()))
        send()
    else:
        if cmd[0] == "9001":
            order = "C|SalvaDataOra|"
        Message_MQTT.empile(str(order) + str(cmd[1]))
        logging.info('Contenu Pile Message_MQTT : ' + str(Message_MQTT.copiepile()))
        send()
    
    
    
def secTOdhms(nb_sec):
    qm, s = divmod(nb_sec, 60)
    qh, m = divmod(qm, 60)
    d, h = divmod(qh, 24)
    return "%d:%d:%d:%d" % (d, h, m, s)

@sio.event
def connect():
    logging.info("Connected")
    logging.debug("SID is : {}".format(sio.sid))
    sio.emit(
        "join",
        {
            "serialNumber": globals.MCZ_device_serial,
            "macAddress": globals.MCZ_device_MAC,
            "type": "Android-App",
        },
    )
    sio.emit(
        "chiedo",
        {
            "serialNumber": globals.MCZ_device_serial,
            "macAddress": globals.MCZ_device_MAC,
            "tipoChiamata": 0,
            "richiesta": "RecuperoParametri",
        },
    )
    sio.emit(
        "chiedo",
        {
            "serialNumber": globals.MCZ_device_serial,
            "macAddress": globals.MCZ_device_MAC,
            "tipoChiamata": 1,
            "richiesta": "C|RecuperoInfo",
        },
    )


@sio.event
def disconnect():
    logging.info("Disconnected")
    
    
@sio.event
def rispondo(response):
    #logging.debug("Received 'rispondo' message")
    datas = response["stringaRicevuta"].split("|")
    from _data_ import RecuperoInfo
    for i in range(0, len(datas)):
        for j in range(0, len(RecuperoInfo)):
            if i == RecuperoInfo[j][0]:
                if len(RecuperoInfo[j]) > 2:
                    for k in range(0, len(RecuperoInfo[j][2])):
                        if int(datas[i], 16) == RecuperoInfo[j][2][k][0]:
                            MQTT_MAESTRO[RecuperoInfo[j][1]] = RecuperoInfo[j][2][k][1]
                            break
                        else:
                            MQTT_MAESTRO[RecuperoInfo[j][1]] = ('Code inconnu :', str(int(datas[i], 16)))
                else:
                    if i == 5 or i == 6 or i == 7 or i == 8 or i == 9 or i == 26 or i == 27 or i == 28 or i == 46 or i == 52 or i == 53 or i == 54 or i == 59:
                    ###if i == 6 or i == 26 or i == 28:
                        MQTT_MAESTRO[RecuperoInfo[j][1]] = float(int(datas[i], 16)) / 2

                    elif i >= 37 and i <= 42:
                        MQTT_MAESTRO[RecuperoInfo[j][1]] = secTOdhms(int(datas[i], 16))
                    else:
                        MQTT_MAESTRO[RecuperoInfo[j][1]] = int(datas[i], 16)
    logging.info('Publication sur MQTT ' + str(globals.MQTT_TOPIC_PUB) + ': ' + str(json.dumps(MQTT_MAESTRO)))
    client.publish(globals.MQTT_TOPIC_PUB, json.dumps(MQTT_MAESTRO), 1)
    
def receiveMcz(*args):
    while True:
        time.sleep(30)
        #logging.debug("Envoi de la commande pour rafraichir les donnees")
        sio.emit(
            "chiedo",
            {
                "serialNumber": globals.MCZ_device_serial,
                "macAddress": globals.MCZ_device_MAC,
                "tipoChiamata": 1,
                "richiesta": "C|RecuperoInfo",
            },
        )
    time.sleep(15)


# ----------------------------------------------------------------------------
def listen():
    try:
        jeedom_socket.open()
        logging.info("Start listening on: [" + str(globals.sockethost) + ":" + str(globals.socketport) + "]" )
        threading.Thread( target=read_socket, args=('socket',)).start()
        logging.debug("Read socket thread launched on: [" + str(globals.sockethost) + ":" + str(globals.socketport) + "]" )
    except Exception as e:
        logging.error("Problem starting listening Jeedom")

    time.sleep(5)   
    sio.connect(globals.MCZ_App_URL)
    
    try:
        logging.info("Starting  link to mcz maestro" )
        threading.Thread( target=receiveMcz, args=('maestro',)).start()
        logging.debug("Link started to mcz maestro OK" )
    except Exception as e:
        logging.error("Problem starting link to mcz maestro")



# ----------------------------------------------------------------------------
def read_socket(name):
    logging.debug("start while read_socket")
    while 1:
        try:
            global JEEDOM_SOCKET_MESSAGE
            if not JEEDOM_SOCKET_MESSAGE.empty():
                logging.debug("Message received in socket JEEDOM_SOCKET_MESSAGE")
                message = JEEDOM_SOCKET_MESSAGE.get().decode('utf-8')
                message =json.loads(message)
                if message['apikey'] != globals.apikey:
                    logging.error("Invalid apikey from socket : " + str(message))
                    return
        except Exception as e:
            logging.error("Exception on socket : %s" % str(e))
        time.sleep(0.3)


# ----------------------------------------------------------------------------
def handler(signum=None, frame=None):
        logging.debug("Signal %i caught, exiting..." % int(signum))
        shutdown()


# ----------------------------------------------------------------------------
def shutdown():
        logging.debug("Shutdown in progress...")
        logging.debug("Removing PID file " + str(globals.pidfile))
        try:
                os.remove(globals.pidfile)
        except:
                pass
        try:
                jeedom_socket.close()
        except:
                pass
        logging.debug("Exit 0")
        sys.stdout.flush()
        os._exit(0)

# ----------------------------------------------------------------------------
def maskinfo(info):
        if len(info) > 5:
            debut = info[:2]
            fin = info[-2:]
            result = debut + "***" + fin
        else:
            result = "***"
        return result

# ----------------------------------------------------------------------------
parser = argparse.ArgumentParser(description='MCZ Remote Daemon for Jeedom plugin')
parser.add_argument("--mqttip", help="MQTT IP server", type=str)
parser.add_argument("--mqttport", help="Port MQTT", type=int)
parser.add_argument("--mqttauth", help="MQTT Authentication", type=int)
parser.add_argument("--mqttuser", help="MQTT user", type=str)
parser.add_argument("--mqttpwd", help="MQTT password", type=str)
parser.add_argument("--topicpub", help="MQTT Topic PUB", type=str)
parser.add_argument("--topicsub", help="MQTT Topic SUB", type=str)
parser.add_argument("--devserial", help="MCZ Device Serial", type=str)
parser.add_argument("--devmac", help="MCZ Device MAC", type=str)
parser.add_argument("--urlmcz", help="MCZ Url", type=str)
parser.add_argument("--loglevel", help="Log Level for the daemon", type=str)
parser.add_argument("--pidfile", help="Value to write", type=str)
parser.add_argument("--callback", help="Value to write", type=str)
parser.add_argument("--apikey", help="Value to write", type=str)
parser.add_argument("--socketport", help="Socket Port", type=int)
parser.add_argument("--sockethost", help="Socket Host", type=str)
args = parser.parse_args()

if args.mqttip:
        globals.MQTT_ip = args.mqttip
if args.mqttport:
        globals.MQTT_port = args.mqttport
if args.mqttauth:
        globals.MQTT_authentication = args.mqttauth
if args.mqttuser:
        globals.MQTT_user = args.mqttuser
if args.mqttpwd:
        globals.MQTT_pass = args.mqttpwd
if args.topicpub:
        globals.MQTT_TOPIC_PUB = args.topicpub
if args.topicsub:
        globals.MQTT_TOPIC_SUB = args.topicsub
if args.devserial:
        globals.MCZ_device_serial = args.devserial
if args.devmac:
        globals.MCZ_device_MAC = args.devmac
if args.urlmcz:
        globals.MCZ_App_URL = args.urlmcz
if args.loglevel:
        globals.log_level = args.loglevel
if args.pidfile:
        globals.pidfile = args.pidfile
if args.callback:
        globals.callback = args.callback
if args.apikey:
        globals.apikey = args.apikey
if args.socketport:
        globals.socketport = args.socketport
if args.sockethost:
        globals.sockethost = args.sockethost

jeedom_utils.set_log_level(globals.log_level)

logging.info('Starting MCZ Remote Daemon (Version '+str(globals.DAEMON_VERSION)+')')
logging.info('Log level: '+str(globals.log_level))
logging.debug('Socket port: '+str(globals.socketport))
logging.debug('Socket host: '+str(globals.sockethost))
logging.debug('MQTT IP: '+str(globals.MQTT_ip))
logging.debug('MQTT port: '+str(globals.MQTT_port))
logging.debug('MQTT Authentication: '+str(globals.MQTT_authentication))
logging.debug('MQTT User: '+ maskinfo(str(globals.MQTT_user)) )
logging.debug('MQTT Password: '+ maskinfo(str(globals.MQTT_pass)) )
logging.debug('MQTT Topic PUB: '+str(globals.MQTT_TOPIC_PUB))
logging.debug('MQTT Topic SUB: '+str(globals.MQTT_TOPIC_SUB))
logging.debug('MCZ Device Serial: '+ maskinfo(str(globals.MCZ_device_serial)) )
logging.debug('MCZ Device MAC: '+ maskinfo(str(globals.MCZ_device_MAC)) )
logging.debug('MCZ Url: '+str(globals.MCZ_App_URL))
logging.debug('PID file: '+str(globals.pidfile))
logging.debug('Apikey: '+str(globals.apikey))
logging.debug('Callback: '+str(globals.callback))

signal.signal(signal.SIGINT, handler)
signal.signal(signal.SIGTERM, handler)  

logging.info('Connection en cours au broker MQTT (IP:' + globals.MQTT_ip + ' PORT:' + str(globals.MQTT_port) + ')')
client = mqtt.Client()
if globals.MQTT_authentication == True:
    client.username_pw_set(username=globals.MQTT_user, password=globals.MQTT_pass)
client.on_connect = on_connect_mqtt
client.on_message = on_message_mqtt
client.connect(globals.MQTT_ip, globals.MQTT_port)
client.loop_start()
logging.info('Souscription au topic ' + str(globals.MQTT_TOPIC_SUB) + ' avec un Qos=1')
client.subscribe(globals.MQTT_TOPIC_SUB, qos=1)


try:
    jeedom_utils.write_pid(str(globals.pidfile))
    globals.JEEDOM_COM = jeedom_com(apikey = globals.apikey,url = globals.callback)
    if not globals.JEEDOM_COM.test():
        logging.error('Network communication issues. Please fix your Jeedom network configuration.')
        shutdown()
    jeedom_socket = jeedom_socket(port=globals.socketport,address=globals.sockethost)
    listen()
except Exception as e:
    logging.error('Fatal error : '+str(e))
    logging.debug(traceback.format_exc())
    shutdown()
