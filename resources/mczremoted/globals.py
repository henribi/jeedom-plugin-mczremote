import time
DAEMON_VERSION = '0.2'
JEEDOM_COM = ''
START_TIME = int(time.time())
log_level = 'error'
socketport= 55520
sockethost= '127.0.0.1'
callback = ''
apikey = ''
pidfile = ''
log_file = '/var/www/html/log/mczremote'

MQTT_ip='127.0.0.1'            #Adresse IP du broker mqtt
MQTT_port=1883                     #Port du broker mqtt
MQTT_authentication=False           #Mqtt use authentication
MQTT_user=''                       #Mqtt User name
MQTT_pass=''                       #Mqtt password
MQTT_TOPIC_SUB='SUBmcz'            #Topic général de souscription
MQTT_TOPIC_PUB='PUBmcz'            #Topic général de publication
MCZ_device_serial = ""
MCZ_device_MAC = ""
MCZ_App_URL = "http://app.mcz.it:9000"

