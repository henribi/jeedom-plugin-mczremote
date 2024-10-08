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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }
    
    ajax::init();

    if (init('action') == 'CopyTemplateMQTT2') {
        mczremote::copyTemplateMQTT2();
        ajax::success();
    }
    
    if (init('action') == 'installTemplate') {
        mczremote::installTemplate();
        ajax::success();
    }

    if (init('action') == 'createEqptWithTemplate') {
        log::add('mczremote', 'debug', 'jMQTT equipment:' . init('eqptName'));
        $result = mczremote::createEqptWithTemplate(init('eqptName'));
        if ($result != 0) {
            throw new Exception(__('Error: Création Equipement avec Template', __FILE__));
        }
        ajax::success();
    }

    if (init('action') == 'health') {
        $data = md5(rand());
        log::add('mczremote', 'debug', 'health:: data:' . var_export($data, true));
        $params = array('method' => 'health', 'data' => $data );
        mczremote::daemon_send($params);
        ajax::success();
    }

    throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayException($e), $e->getCode());
}

