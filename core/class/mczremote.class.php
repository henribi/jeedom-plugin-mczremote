
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

	const CLIENT_OK                     = 'ok';
	const CLIENT_POK                    = 'pok';
	const CLIENT_NOK                    = 'nok';
	
    /*     * *************************Attributs****************************** */
    public static $_encryptConfigKey = array('MQTTuser', 'MQTTpwd', 'DevSerial', 'DevMac');



    /*     * ***********************Methode static*************************** */

	public static function dependancy_info() {
		$depLogFile = __CLASS__ . '_dep';
		$depProgressFile = jeedom::getTmpFolder(__CLASS__) . '/dependancy';

        $return = array();
		$return['log'] = log::getPathToLog($depLogFile);
		$return['progress_file'] = $depProgressFile;
                $return['state'] = self::CLIENT_OK;

		if (file_exists($depProgressFile)) {
			log::add(__CLASS__,'debug', sprintf(__("Dépendances en cours d'installation... (%s%%)", __FILE__), trim(file_get_contents($depProgressFile))));
			$return['state'] = self::CLIENT_NOK;
			return $return;
		}

		if (!file_exists(__DIR__ . '/../../resources/mczremoted/venv/bin/pip3') || !file_exists(__DIR__ . '/../../resources/mczremoted/venv/bin/python3')) {
			log::add(__CLASS__,'debug', __("Relancez les dépendances, le venv Python n'a pas encore été créé", __FILE__));
			$return['state'] = self::CLIENT_NOK;
		} else {
			exec(__DIR__ . '/../../resources/mczremoted/venv/bin/pip3 freeze --no-cache-dir -r '.__DIR__ . '/../../resources/python-requirements/requirements.txt 2>&1 >/dev/null', $output);
			if (count($output) > 0) {
				log::add(__CLASS__,'error', __('Relancez les dépendances, au moins une bibliothèque Python requise est manquante dans le venv :', __FILE__).' <br />'.implode('<br />', $output));
				$return['state'] = self::CLIENT_NOK;
			}
		}
		if ($return['state'] == self::CLIENT_OK)
			log::add(__CLASS__,'debug', sprintf(__('Dépendances installées.', __FILE__)));
		return $return;
    }

	public static function dependancy_install() {
		$depLogFile = __CLASS__ . '_dep';
		$depProgressFile = jeedom::getTmpFolder(__CLASS__) . '/dependancy';

		log::add(__CLASS__,'info', sprintf(__('Installation des dépendances, voir log dédié (%s)', __FILE__), $depLogFile));
		log::remove($depLogFile);
		return array(
			'script' => __DIR__ . '/../../resources/install_#stype#.sh ' . $depProgressFile,
			'log' => log::getPathToLog($depLogFile)
		);

	}

	public static function copyTemplateMQTT2() {
		$found = 0;
		log::add('mczremote', 'debug',"copyTemplateMQTT2");
		$pluginList = plugin::listPlugin($_activateOnly = true, $_orderByCategory = false, $_translate = true, $_nameOnly = true);
		if (is_array($pluginList)) {
			foreach ($pluginList as $val) {
				if ($val == 'mqtt2'){
					$pathPlugin = plugin::getPluginPath($val);
					$found = 1;
					break;
				}
			}
		}
		if ($found == 0) {
			log::add('mczremote', 'error',"Le plugin MQTT manager n'est pas installé (I01)");
			throw new Exception("Le plugin MQTT manager n'est pas installé (I01)");
		}
		// png file
		$MQTT2PathTemplate = $pathPlugin . '/core/config/devices/other' . '/MCZRemoteMQTT2.png';
		$MCZRemotePathTemplate = plugin::getPluginPath('mczremote') . '/data/template' . '/MCZRemoteMQTT2.png';
		//log::add('mczremote', 'debug', 'Path dest: ' . $MQTT2PathTemplate . '    Path source: ' . $MCZRemotePathTemplate);
		if (file_exists( $MCZRemotePathTemplate)) {
			$result = copy($MCZRemotePathTemplate, $MQTT2PathTemplate );
			//if ($result) {
			//	log::add('mczremote', 'debug', 'Copie du template png dans MQTT manager  OK');
			//} else {
			//	log::add('mczremote', 'warning', 'Copie du template png dans MQTT manager  NOK. (I05)');
			//	throw new Exception("Copie du template png dans MQTT manager  NOK. (I04)");
			//}			
		}
		// json file
		$MQTT2PathTemplate = $pathPlugin . '/core/config/devices/other' . '/MCZRemoteMQTT2.json';
		$MCZRemotePathTemplate = plugin::getPluginPath('mczremote') . '/data/template' . '/MCZRemoteMQTT2.json';
		//log::add('mczremote', 'debug', 'Path dest: ' . $MQTT2PathTemplate . '    Path source: ' . $MCZRemotePathTemplate);
		if (file_exists( $MCZRemotePathTemplate)) {
			$result = copy($MCZRemotePathTemplate, $MQTT2PathTemplate );
			if ($result) {
				log::add('mczremote', 'debug', 'Copie du template dans MQTT manager  OK');
			} else {
				log::add('mczremote', 'warning', 'Copie du template dans MQTT manager  NOK. (I04)');
				throw new Exception("Copie du template dans MQTT manager  NOK. (I04)");
			}			
		}
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
			log::add('mczremote', 'error',"Le plugin jMQTT n'est pas installé (I01)");
			throw new Exception("Le plugin jMQTT n'est pas installé (I01)");
		}
		//if (!method_exists('jMQTT', 'templateSplitJsonPathByFile')) {
		//	log::add('mczremote', 'error',"La version installée de jMQTT n'est pas assez récente (I02)");
		//	throw new Exception("La version installée de jMQTT n'est pas compatible (I02)");
		//}
		if (!method_exists('jMQTT', 'moveTopicToConfigurationByFile')) {
			log::add('mczremote', 'error',"La version installée de jMQTT n'est pas assez récente (I03)");
			throw new Exception("La version installée de jMQTT n'est pas compatible (I03)");
		}
		$jMQTTPathTemplate = $pathPlugin . '/data/template' . '/MCZRemote.json';
		$MCZRemotePathTemplate = plugin::getPluginPath('mczremote') . '/data/template' . '/MCZRemote.json';
		//log::add('mczremote', 'debug', 'Path dest: ' . $jMQTTPathTemplate . '    Path source: ' . $MCZRemotePathTemplate);
		if (file_exists( $MCZRemotePathTemplate)) {
			$result = copy($MCZRemotePathTemplate, $jMQTTPathTemplate );
			if ($result) {
				// Adapt template for the new jsonPath field
				//////jMQTT::templateSplitJsonPathByFile('MCZRemote.json');
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
			log::add('mczremote', 'error',"Le plugin jMQTT n'est pas installé (C01)");
			throw new Exception("Le plugin jMQTT n'est pas installé (C01)");
		}
		if (!method_exists('jMQTT', 'createEqWithTemplate')) {
			log::add('mczremote', 'error','La version installée de jMQTT ne supporte pas cette fonction. (C02)');
			throw new Exception("La version installée de jMQTT ne supporte pas cette fonction. (C02)");
			$return = 1;				
		}
		//if (!method_exists('jMQTT', 'templateSplitJsonPathByFile')) {
		//	log::add('mczremote', 'error',"La version installée de jMQTT n'est pas assez récente. (C03)");
		//	throw new Exception("La version installée de jMQTT n'est pas compatible. (C03)");
		//}
		if (!method_exists('jMQTT', 'moveTopicToConfigurationByFile')) {
			log::add('mczremote', 'error',"La version installée de jMQTT n'est pas assez récente. (C04)");
			throw new Exception("La version installée de jMQTT n'est pas compatible (C04)");
		}
		$jMQTTPathTemplate = $pathPlugin . '/data/template' . '/MCZRemote.json';
		$MCZRemotePathTemplate = plugin::getPluginPath('mczremote') . '/data/template' . '/MCZRemote.json';
		//log::add('mczremote', 'debug', 'Path dest: ' . $jMQTTPathTemplate . '    Path source: ' . $MCZRemotePathTemplate);
		if (file_exists( $MCZRemotePathTemplate)) {
			$result = copy($MCZRemotePathTemplate, $jMQTTPathTemplate );
			if ($result) {
				// Adapt template for the new jsonPath field
				/////jMQTT::templateSplitJsonPathByFile('MCZRemote.json');
				// Adapt template for the topic in configuration
				jMQTT::moveTopicToConfigurationByFile('MCZRemote.json');
			} else {
				log::add('mczremote', 'warning', 'Installation du template dans jMQTT  NOK. (C05)');
				throw new Exception("Installation du template dans jMQTT  NOK. (C05)");
			}				
		}
		// Recuperation des infos utiles pour appel jMQTT::createEqWithTemplate
		$brkAddr = config::byKey('MQTTip', __CLASS__); 		// IP ou Hostname du Broker cible (afin de le retrouver)
		$eqName = $eqptName;  								// Nom à donner à l'équipement dans jMQTT
		$pathTemplate = $jMQTTPathTemplate;   				// '/var/www/........'; // Chemin vers la Template à appliquer
		$eqTopic = config::byKey('TopicPub', __CLASS__); 	// Topic de base à remplacer dans la Template
		$uuid = 'MCZR_eqpt'; 								// Pour retrouver l'eq lors d'un nouvel appel à la méthode

		// Verification que l'on applique pas sur un équipement existant.  ==> doit d'abord etre supprimé
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
			log::add('mczremote', 'warning', 'Un équipement ' . $eqpt->getHumanName() . ' est déjà installé dans jMQTT avec cette procédure.');
			log::add('mczremote', 'warning', 'Réinstallation non autorisée.');
			throw new Exception('Un équipement ' . $eqpt->getHumanName() . ' est déjà installé dans jMQTT avec cette procédure. Voir log');
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
			$return['launchable_message'] =  __('L\'adresse IP du serveur MQTT n\'est pas configuré', __FILE__);
		} elseif ($port == '') {
			$return['launchable'] = 'nok';
			$return['launchable_message'] = __('Le port MQTT n\'est pas configuré', __FILE__);
		} elseif ($devserial == '') {
			$return['launchable'] = 'nok';
			$return['launchable_message'] = __('L\'information Device Serial n\'est pas configurée', __FILE__);
		} elseif ($devmac == '') {
			$return['launchable'] = 'nok';
			$return['launchable_message'] = __('L\'information Device Serial n\'est pas configurée', __FILE__);
		}
		//if (exec(system::getCmdSudo() . 'pip3 list | grep -E "python-socketio[ ]*4.6.1|python-engineio[ ]*3.14.2" | wc -l') < 2) {
		//	$return['launchable'] = 'nok';
		//	$return['launchable_message'] = __('Relancer la mise à jour des dépendances', __FILE__);
		//}

		return $return;
	}

	public static function deamon_start() {
		self::deamon_stop();
		$deamon_info = self::deamon_info();
		if ($deamon_info['launchable'] != 'ok') {
			throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
        }

		$mczremote_path = realpath(dirname(__FILE__) . '/../../resources/mczremoted');
		log::add('mczremote', 'debug', 'path:' . $mczremote_path);
		$cmd = $mczremote_path.'/venv/bin/python3 ' . $mczremote_path . '/mczremoted.py';
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
		log::add('mczremote', 'debug', 'Lancement démon MCZremote : ' . $cmd);
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
			log::add('mczremote', 'error', __('Impossible de lancer le démon MCZ, vérifiez le log',__FILE__), 'unableStartDeamon');
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
			throw new Exception("Le démon n'est pas démarré");
                }

		$params['apikey'] = jeedom::getApiKey('mczremote');
		$payload = json_encode($params);
		$socket = socket_create(AF_INET, SOCK_STREAM, 0);
		socket_connect($socket, '127.0.0.1', config::byKey('socketport', 'mczremote'));
		socket_write($socket, $payload, strlen($payload));
		socket_close($socket);
	}

    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
      public static function cron() {

      }
     */


    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {

      }
     */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDaily() {

      }
     */

	public static function backupExclude() {
		return [
			'resources/mczremoted/venv'
		];
	}

    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {
        
    }

    public function postInsert() {
        
    }

    public function preSave() {
        
    }

    public function postSave() {
		log::add('mczremote','debug','Exécution de la fonction postSave');
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
     * Non obligatoire mais ca permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action avant modification de variable de configuration
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
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

    public function execute($_options = array()) {
        
    }

    /*     * **********************Getteur Setteur*************************** */
}


