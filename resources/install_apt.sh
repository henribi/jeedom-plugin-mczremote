#!/bin/bash

# This file is part of Plugin mczremote for jeedom.

#set -x  # make sure each command is printed in the terminal
PROGRESS_FILE=/tmp/dependency_mczremote_in_progress
if [ ! -z "$1" ]; then
    PROGRESS_FILE=$1
fi
touch "${PROGRESS_FILE}"

echo 0 > "${PROGRESS_FILE}"
echo "****************    install_apt.sh      ****************"
echo "********************************************************"
echo "*             Installation des dépendances             *"
echo "********************************************************"

echo 5 > "${PROGRESS_FILE}"
echo "********************************************************"
echo "*        Update package lists from repositories        *"
echo "********************************************************"
case $( uname -s ) in
Linux)
  echo "Kernel name: Linux"
  case $( uname -m ) in
  armv6l|armv7l)
    echo "Machine Hardware name: $(uname -m)";;
  x86_64)
    echo "Machine Hardware name: x86_64";;
  aarch64)
    echo "Machine Hardware name: aarch64";;
  i686)
    echo "Machine Hardware name: i686";;
  *)
    echo other;;
  esac;;
*)
  echo other;;
esac
sudo apt-get update

echo 20 > "${PROGRESS_FILE}"
echo "********************************************************"
echo "*         Install Python3 and dependencies             *"
echo "********************************************************"
sudo apt-get install -y python3 python3-pip

echo 40 > "${PROGRESS_FILE}"
echo "********************************************************"
echo "*             Python3 'requests' module                *"
echo "********************************************************"
pip3 install requests

echo 50 > "${PROGRESS_FILE}"
echo "********************************************************"
echo "*              Python3 'pyudev' module                 *"
echo "********************************************************"
pip3 install pyudev

echo 55 > "${PROGRESS_FILE}"
echo "********************************************************"
echo "*              Python3 'paho-mqtt' module              *"
echo "********************************************************"
pip3 install paho-mqtt

echo 60 > "${PROGRESS_FILE}"
echo "********************************************************"
echo "*              Python3 'websocket-client' mo           *"
echo "********************************************************"
pip3 install websocket-client

echo 70 > "${PROGRESS_FILE}"
echo "********************************************************"
echo "*              Python3 'python-socketio' module        *"
echo "********************************************************"
pip3 install python-socketio==4.6.1

echo 80 > "${PROGRESS_FILE}"
echo "********************************************************"
echo "*              Python3 'pytho-engineio' mod            *"
echo "********************************************************"
pip3 install python-engineio==3.14.2


echo 90 > "${PROGRESS_FILE}"
echo "********************************************************"
echo "*            Post-Installation cleaning                *"
echo "********************************************************"

echo 100 > "${PROGRESS_FILE}"
echo "********************************************************"
echo "*             Installation terminée                    *"
echo "********************************************************"
rm "${PROGRESS_FILE}"
