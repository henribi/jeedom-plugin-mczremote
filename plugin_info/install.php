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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

const SOCKETPORT = 55520;
const MQTTPORT = 1883;
const TOPICPUB = 'PUBmcz';
const TOPICSUB = 'SUBmcz';
const URLMCZ = 'http://app.mcz.it:9000';

function mczremote_install() {
    config::save('UrlMCZ', URLMCZ, 'mczremote');
    config::save('socketport', SOCKETPORT, 'mczremote');
    config::save('MQTTport', MQTTPORT, 'mczremote');
    config::save('TopicPub', TOPICPUB, 'mczremote');
    config::save('TopicSub', TOPICSUB, 'mczremote');
}

function mczremote_update() {
    // if version info is not in DB, it means it is a fresh install of mczremote
    // (even if plugin is disabled the config key stays)
    try {
        $content = file_get_contents(__DIR__ . '/info.json');
        $info = json_decode($content, true);
        $pluginVer = $info['pluginVersion'];
    } catch (Throwable $e) {
        log::add(
            "mczremote",
            'warning',
            __("Impossible de récupérer le numéro de version dans le fichier info.json, ceci ce devrait pas arriver !", __FILE__)
        );
        $pluginVer = '0.0.0';
    }

    // Backup old version number
    $currentVer = config::byKey('version', 'mczremote', $pluginVer);
    // @phpstan-ignore-next-line
    $currentVer = is_int($currentVer) ? strval($currentVer) . '.0.0' : $currentVer;
    config::save('previousVersion', $currentVer, 'mczremote');

    config::save('version', $pluginVer, 'mczremote');

}


function mczremote_remove() {
    
}

?>
