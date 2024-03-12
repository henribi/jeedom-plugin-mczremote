#!/bin/bash
######################### INCLUSION LIB ##########################
BASEDIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
#wget https://raw.githubusercontent.com/NebzHB/dependance.lib/master/dependance.lib -O $BASEDIR/dependance.lib &>/dev/null
PROGRESS_FILENAME=dependancy
PLUGIN=$(basename "$(realpath $BASEDIR/..)")
LANG_DEP=en
. ${BASEDIR}/dependance.lib
##################################################################

pre
step 0 "Checking parameters"

LOCAL_VERSION="????"
if [ -n $2 ]; then
	LOCAL_VERSION=$2
fi

echo "== System: "`uname -a`
echo "== Jeedom version: "`cat ${BASEDIR}/../../../core/config/version`
echo "== MczRemote version: "${LOCAL_VERSION}

step 10 "Synchronize the package index"
try sudo apt-get update

step 20 "Install python3 venv and pip debian packages"
try sudo DEBIAN_FRONTEND=noninteractive apt-get install -y python3-venv python3-pip

step 30 "Create a python3 Virtual Environment"
try sudo -u www-data python3 -m venv $BASEDIR/mczremoted/venv

step 40 "Install required python3 libraries in venv"
try sudo -u www-data $BASEDIR/mczremoted/venv/bin/pip3 install --no-cache-dir -r $BASEDIR/python-requirements/requirements.txt

post
