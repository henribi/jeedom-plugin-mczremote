
<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';

/* error_reporting(-1); */

class mczremote extends eqLogic {
    /*     * *************************Attributs****************************** */
    public static $_encryptConfigKey = array('MQTTuser', 'MQTTpwd', 'DevSerial', 'DevMac');



    /*     * ***********************Methode static*************************** */

	public static function dependancy_info() {
        $return = array();
		$return['log'] = 'mczremote_update';
		$return['progress_file'] = jeedom::getTmpFolder('mczremote') . '/dependency';
                $return['state'] = 'ok';

		if (exec(system::getCmdSudo() . system::get('cmd_check') . '-E "|python3\-requests|python3\-pyudev" | wc -l') < 2) {
			$return['state'] = 'nok';
		}
		if (exec(system::getCmdSudo() . 'pip3 list | grep -E "pyudev|requests" | wc -l') < 2) {
			$return['state'] = 'nok';
		}
		if (exec(system::getCmdSudo() . 'pip3 list | grep -E "python-socketio[ ]*4.6.1|python-engineio[ ]*3.14.2" | wc -l') < 2) {
			$return['state'] = 'nok';
		}
		return $return;
    }

	public static function dependancy_install() {
		log::remove(__CLASS__ . '_update');
		return array('script' => dirname(__FILE__) . '/../../resources/install_#stype#.sh ' . jeedom::getTmpFolder('mczremote') . '/dependency', 'log' => log::getPathToLog(__CLASS__ . '_update'));
	}

	public static function installTemplate() {
		$found = 0;
		log::add('mczremote', 'debug',"installTemplate");
		$pluginList = plugin::listPlugin($_activateOnly = true, $_orderByCategory = false, $_translate = true, $_nameOnly = true);
		if (is_array($pluginList)) {
			foreach ($pluginList as $val) {
				if ($val == 'jMQTT'){
					$pathPlugin = plugin::getPluginPath($val);
					$found = 1;
					break;
				}
			}
		}
		if ($found == 0) {
			log::add('mczremote', 'error',"Le plugin jMQTT n'est pas install?? (I01)");
			throw new Exception("Le plugin jMQTT n'est pas install?? (I01)");
		}
		if (!method_exists('jMQTT', 'templateSplitJsonPathByFile')) {
			log::add('mczremote', 'error',"La version install??e de jMQTT n'est pas assez r??cente (I02)");
			throw new Exception("La version install??e de jMQTT n'est pas assez r??cente (I02)");
		}
		if (!method_exists('jMQTT', 'moveTopicToConfigurationByFile')) {
			log::add('mczremote', 'error',"La version install??e de jMQTT n'est pas assez r??cente (I03)");
			throw new Exception("La version install??e de jMQTT n'est pas assez r??cente (I03)");
		}
		$jMQTTPathTemplate = $pathPlugin . '/data/template' . '/MCZRemote.json';
		$MCZRemotePathTemplate = plugin::getPluginPath('mczremote') . '/data/template' . '/MCZRemote.json';
		//log::add('mczremote', 'debug', 'Path dest: ' . $jMQTTPathTemplate . '    Path source: ' . $MCZRemotePathTemplate);
		if (file_exists( $MCZRemotePathTemplate)) {
			$result = copy($MCZRemotePathTemplate, $jMQTTPathTemplate );
			if ($result) {
				// Adapt template for the new jsonPath field
				jMQTT::templateSplitJsonPathByFile('MCZRemote.json');
				// Adapt template for the topic in configuration
				jMQTT::moveTopicToConfigurationByFile('MCZRemote.json');
				log::add('mczremote', 'debug', 'Installation du template dans jMQTT  OK');
			} else {
				log::add('mczremote', 'warning', 'Installation du template dans jMQTT  NOK. (I04)');
				throw new Exception("Installation du template dans jMQTT  NOK. (I04)");
			}			
		}
	}


	public static function createEqptWithTemplate($eqptName = '') {
		$return = 0;
		$found = 0;
		log::add('mczremote', 'debug',"createEqptWithTemplate");
		$pluginList = plugin::listPlugin($_activateOnly = true, $_orderByCategory = false, $_translate = true, $_nameOnly = true);
		if (is_array($pluginList)) {
			foreach ($pluginList as $val) {
				if ($val == 'jMQTT'){
					$pathPlugin = plugin::getPluginPath($val);
					$found = 1;
					break;
				}
			}
		}
		if ($found == 0) {
			log::add('mczremote', 'error',"Le plugin jMQTT n'est pas install?? (C01)");
			throw new Exception("Le plugin jMQTT n'est pas install?? (C01)");
		}
		if (!method_exists('jMQTT', 'createEqWithTemplate')) {
			log::add('mczremote', 'error','La version install??e de jMQTT ne supporte pas cette fonction. (C02)');
			throw new Exception("La version install??e de jMQTT ne supporte pas cette fonction. (C02)");
			$return = 1;				
		}
		if (!method_exists('jMQTT', 'templateSplitJsonPathByFile')) {
			log::add('mczremote', 'error',"La version install??e de jMQTT n'est pas assez r??cente. (C03)");
			throw new Exception("La version install??e de jMQTT n'est pas assez r??cente. (C03)");
		}
		if (!method_exists('jMQTT', 'moveTopicToConfigurationByFile')) {
			log::add('mczremote', 'error',"La version install??e de jMQTT n'est pas assez r??cente. (C04)");
			throw new Exception("La version install??e de jMQTT n'est pas assez r??cente (C04)");
		}
		$jMQTTPathTemplate = $pathPlugin . '/data/template' . '/MCZRemote.json';
		$MCZRemotePathTemplate = plugin::getPluginPath('mczremote') . '/data/template' . '/MCZRemote.json';
		//log::add('mczremote', 'debug', 'Path dest: ' . $jMQTTPathTemplate . '    Path source: ' . $MCZRemotePathTemplate);
		if (file_exists( $MCZRemotePathTemplate)) {
			$result = copy($MCZRemotePathTemplate, $jMQTTPathTemplate );
			if ($result) {
				// Adapt template for the new jsonPath field
				jMQTT::templateSplitJsonPathByFile('MCZRemote.json');
				// Adapt template for the topic in configuration
				jMQTT::moveTopicToConfigurationByFile('MCZRemote.json');
			} else {
				log::add('mczremote', 'warning', 'Installation du template dans jMQTT  NOK. (C05)');
				throw new Exception("Installation du template dans jMQTT  NOK. (C05)");
			}				
		}
		// Recuperation des infos utiles pour appel jMQTT::createEqWithTemplate
		$brkAddr = config::byKey('MQTTip', __CLASS__); 		// IP ou Hostname du Broker cible (afin de le retrouver)
		$eqName = $eqptName;  								// Nom ?? donner ?? l'??quipement dans jMQTT
		$pathTemplate = $jMQTTPathTemplate;   				// '/var/www/........'; // Chemin vers la Template ?? appliquer
		$eqTopic = config::byKey('TopicPub', __CLASS__); 	// Topic de base ?? remplacer dans la Template
		$uuid = 'MCZR_eqpt'; 								// Pour retrouver l'eq lors d'un nouvel appel ?? la m??thode

		// Verification que l'on applique pas sur un ??quipement existant.  ==> doit d'abord etre supprim??
		$eq = null;
		// Search for a jMQTT Eq with $uuid, if found apply template to it
		$type = json_encode(array(jMQTT::CONF_KEY_TEMPLATE_UUID => $uuid));
		$eqpts = self::byTypeAndSearchConfiguration(jMQTT::class, substr($type, 1, -1));
		foreach ($eqpts as $eqpt) {
			log::add('mczremote', 'debug', 'createEqptWithTemplate ' . $eqName . ': Found matching Eq '.$eqpt->getHumanName());
			$eq = $eqpt;
			break;
		}

		if (!is_null($eq)) {
			log::add('mczremote', 'warning', 'Un ??quipement ' . $eqpt->getHumanName() . ' est d??j?? install?? dans jMQTT avec cette proc??dure.');
			log::add('mczremote', 'warning', 'R??installation non autoris??e.');
			throw new Exception('Un ??quipement ' . $eqpt->getHumanName() . ' est d??j?? install?? dans jMQTT avec cette proc??dure. Voir log');
		} else {
			$eq = jMQTT::createEqWithTemplate($brkAddr, $eqName, $pathTemplate, $eqTopic, $uuid);
	
			// Place le flag irremovable sur toutes les commandes
			$eqId = $eq->getId();
			$hbcmds = cmd::byEqLogicId($eqId);
			foreach ($hbcmds as $hbcmd) {
				$hbcmd->setConfiguration('irremovable', 1);
				$hbcmd->save();
			}
		}

		return ($return); 
	}


	public static function deamon_info() {
		$return = array();
		$return['log'] = __CLASS__;
		$return['state'] = 'nok';

		$pid_file = jeedom::getTmpFolder(__CLASS__) . '/deamon.pid';
		if (file_exists($pid_file)) {
			if (@posix_getsid(trim(file_get_contents($pid_file)))) {
				$return['state'] = 'ok';
            } else {
                shell_exec(system::getCmdSudo() . 'rm -rf ' . $pid_file . ' 2>&1 > /dev/null');
			}
		}
		$return['launchable'] = 'ok';
		$return['launchable_message'] = '';
		$mqttip = config::byKey('MQTTip', __CLASS__);
		$port = config::byKey('MQTTport', __CLASS__);
		$devserial = config::byKey('DevSerial', __CLASS__);
		$devmac = config::byKey('DevMac', __CLASS__);

		if ($mqttip == '') {
			$return['launchable'] = 'nok';
			$return['launchable_message'] =  __('L\'adresse IP du serveur MQTT n\'est pas configur??', __FILE__);
		} elseif ($port == '') {
			$return['launchable'] = 'nok';
			$return['launchable_message'] = __('Le port MQTT n\'est pas configur??', __FILE__);
		} elseif ($devserial == '') {
			$return['launchable'] = 'nok';
			$return['launchable_message'] = __('L\'information Device Serial n\'est pas configur??e', __FILE__);
		} elseif ($devmac == '') {
			$return['launchable'] = 'nok';
			$return['launchable_message'] = __('L\'information Device Serial n\'est pas configur??e', __FILE__);
		}
		if (exec(system::getCmdSudo() . 'pip3 list | grep -E "python-socketio[ ]*4.6.1|python-engineio[ ]*3.14.2" | wc -l') < 2) {
			$return['launchable'] = 'nok';
			$return['launchable_message'] = __('Relancer la mise ?? jour des d??pendances', __FILE__);
		}

		return $return;
	}

	public static function deamon_start() {
		self::deamon_stop();
		$deamon_info = self::deamon_info();
		if ($deamon_info['launchable'] != 'ok') {
			throw new Exception(__('Veuillez v??rifier la configuration', __FILE__));
        }

		$mczremote_path = realpath(dirname(__FILE__) . '/../../resources/mczremoted');
		log::add('mczremote', 'debug', 'path:' . $mczremote_path);
		$cmd = '/usr/bin/python3 ' . $mczremote_path . '/mczremoted.py';
		$cmd .= ' --loglevel ' . log::convertLogLevel(log::getLogLevel('mczremote'));
		$cmd .= ' --mqttip ' . config::byKey('MQTTip', __CLASS__);
		$cmd .= ' --mqttport ' . config::byKey('MQTTport', __CLASS__);
		if (config::byKey('MQTTauth', __CLASS__) == 1 ) {
			$cmd .= ' --mqttauth ' . config::byKey('MQTTauth', __CLASS__);
			$cmd .= ' --mqttuser ' . config::byKey('MQTTuser', __CLASS__);
			$cmd .= ' --mqttpwd ' . config::byKey('MQTTpwd', __CLASS__);
		}
		$cmd .= ' --topicpub ' . config::byKey('TopicPub', __CLASS__);
		$cmd .= ' --topicsub ' . config::byKey('TopicSub', __CLASS__);
		$cmd .= ' --devserial ' . config::byKey('DevSerial', __CLASS__);
		$cmd .= ' --devmac ' . config::byKey('DevMac', __CLASS__);
		$cmd .= ' --urlmcz ' . config::byKey('UrlMCZ', __CLASS__);
		$cmd .= ' --socketport ' . config::byKey('socketport', __CLASS__);
		$cmd .= ' --sockethost 127.0.0.1';
		$cmd .= ' --callback ' . network::getNetworkAccess('internal', 'proto:127.0.0.1:port:comp') . '/plugins/mczremote/core/php/jeeMCZRemote.php';
		$cmd .= ' --apikey ' . jeedom::getApiKey(__CLASS__);
		$cmd .= ' --pidfile ' . jeedom::getTmpFolder(__CLASS__) . '/deamon.pid';
		log::add('mczremote', 'debug', 'Lancement d??mon MCZremote : ' . $cmd);
		$result = exec($cmd . ' >> ' . log::getPathToLog(__CLASS__) . 'd' . ' 2>&1 &');
		$i = 0;
		while ($i < 30) {
			$deamon_info = self::deamon_info();
			if ($deamon_info['state'] == 'ok') {
				break;
			}
			sleep(1);
			$i++;
		}
		if ($i >= 30) {
			log::add('mczremote', 'error', __('Impossible de lancer le d??mon MCZ, v??rifiez le log',__FILE__), 'unableStartDeamon');
			return false;
		}
		message::removeAll('mczremote', 'unableStartDeamon');
		return true;
	}

	public static function deamon_stop() {
		$pid_file = jeedom::getTmpFolder('mczremote') . '/deamon.pid';
		if (file_exists($pid_file)) {
			$pid = intval(trim(file_get_contents($pid_file)));
			system::kill($pid);
		}
		system::kill('mczremoted.py');
		system::fuserk(config::byKey('socketport', 'mczremote'));
		sleep(1);
    }

	public static function daemon_send($params) {
		$deamon_info = self::deamon_info();
		if ($deamon_info['state'] != 'ok') {
			throw new Exception("Le d??mon n'est pas d??marr??");
                }

		$params['apikey'] = jeedom::getApiKey('mczremote');
		$payload = json_encode($params);
		$socket = socket_create(AF_INET, SOCK_STREAM, 0);
		socket_connect($socket, '127.0.0.1', config::byKey('socketport', 'mczremote'));
		socket_write($socket, $payload, strlen($payload));
		socket_close($socket);
	}

    /*
     * Fonction ex??cut??e automatiquement toutes les minutes par Jeedom
      public static function cron() {

      }
     */


    /*
     * Fonction ex??cut??e automatiquement toutes les heures par Jeedom
      public static function cronHourly() {

      }
     */

    /*
     * Fonction ex??cut??e automatiquement tous les jours par Jeedom
      public static function cronDaily() {

      }
     */



    /*     * *********************M??thodes d'instance************************* */

    public function preInsert() {
        
    }

    public function postInsert() {
        
    }

    public function preSave() {
        
    }

    public function postSave() {
		log::add('mczremote','debug','Ex??cution de la fonction postSave');
    }

    public function preUpdate() {
        
    }

    public function postUpdate() {
        
    }

    public function preRemove() {
        
    }

    public function postRemove() {
        
    }

    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*
     * Non obligatoire mais ca permet de d??clencher une action apr??s modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire mais ca permet de d??clencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

    /*     * **********************Getteur Setteur*************************** */
}

class mczremoteCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes m??me si elles ne sont pas dans la nouvelle configuration de l'??quipement envoy?? en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

    public function execute($_options = array()) {
        
    }

    /*     * **********************Getteur Setteur*************************** */
}


