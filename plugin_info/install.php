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

    
}


function mczremote_remove() {
    
}

?>
