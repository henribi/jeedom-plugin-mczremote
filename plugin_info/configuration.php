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
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
?>
<form class="form-horizontal">
    <fieldset>
        <legend><i class="fas fa-user-cog"></i> {{MCZ Maestro}}</legend>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Device Serial}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control" data-l1key="DevSerial" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Device MAC}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control" data-l1key="DevMac" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{URL des serveurs MCZ}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control" data-l1key="UrlMCZ" />
            </div>
        </div>
        <legend><i class="fas fa-user-cog"></i> {{MQTT}}</legend>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{IP du serveur}} <sup><i class="fas fa-question-circle" title="{{Adresse IP du serveur MQTT.}}"></i></sup></label>
            <div class="col-lg-2">
                <input class="configKey form-control" data-l1key="MQTTip" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Port du serveur}} <sup><i class="fas fa-question-circle" title="{{Port pour le dialogue avec le serveur MQTT.}}"></i></sup></label>
            <div class="col-lg-2">
                <input class="configKey form-control" data-l1key="MQTTport" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Utilisateur}} <sup><i class="fas fa-question-circle" title="{{Utilisateur pour l'authentification au serveur MQTT.}}"></i></sup></label>
            <div class="col-sm-1" style="width:20px">
                <label class="checkbox-inline" style="vertical-align:top;"><input type="checkbox" class="configKey" data-l1key="MQTTauth"/></label>
            </div>
            <div class="col-lg-2">
                <input class="configKey form-control" data-l1key="MQTTuser" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Mot de passe}} <sup><i class="fas fa-question-circle" title="{{Mot de passe pour l'authentification au serveur MQTT.}}"></i></sup></label>
            <div class="col-sm-1" style="width:20px">
            </div>
            <div class="col-lg-2">
                <input class="configKey form-control" data-l1key="MQTTpwd" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Topic PUB}} <sup><i class="fas fa-question-circle" title="{{Topic dans MQTT pour la réception des informations du poele}}"></i></sup></label>
            <div class="col-lg-2">
                <input class="configKey form-control" data-l1key="TopicPub" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Topic SUB}} <sup><i class="fas fa-question-circle" title="{{Topic dans MQTT pour l'envoi des commandes au poele.}}"></i></sup></label>
            <div class="col-lg-2">
                <input class="configKey form-control" data-l1key="TopicSub" />
            </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label">{{Installer template dans jMQTT}}</label>
          <div class="col-md-4">
            <a class="btn btn-warning" id="bt_InstallTemplate">{{Installer}}</a>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label">{{Installer template et créer équipement dans jMQTT}}</label>
          <div class="col-md-4">
            <a class="btn btn-warning" id="bt_CreateEqptWithTemplate">{{Installer & créer}}</a>
          </div>
        </div>
        <legend><i class="fas fa-university"></i> {{Démon}}</legend>
        <div class="form-group">
            <label class="col-sm-4 control-label">{{Port socket interne}} <sup><i class="fas fa-question-circle" title="{{Si le numéro de port est en conflit avec un autre service, mettez à jour ce champ en indiquant un numéro de port non utilisé par le système.}}"></i></sup></label>
            <div class="col-sm-1">
                <input class="configKey form-control" data-l1key="socketport" />
            </div>
        </div>
  </fieldset>
</form>
<script>
  $('#bt_InstallTemplate').off('click').on('click', function() {
    $.ajax({
      type: "POST",
      url: "plugins/mczremote/core/ajax/mczremote.ajax.php",
      data: {
        action: "installTemplate"
      },
      dataType: 'json',
      error: function(request, status, error) {
        handleAjaxError(request, status, error);
      },
      success: function(data) {
        if (data.state != 'ok') {
          $('#div_alert').showAlert({
            message: data.result,
            level: 'danger'
          });
          return;
        } else {
          window.toastr.clear()
          $('.pluginDisplayCard[data-plugin_id=' + $('#span_plugin_id').text() + ']').click()
          $('#div_alert').showAlert({
            message: '{{Installation réussie}}',
            level: 'success'
          });

        }
      }
    });
  });
  $('#bt_CreateEqptWithTemplate').off('click').on('click', function() {
    $.ajax({
      type: "POST",
      url: "plugins/mczremote/core/ajax/mczremote.ajax.php",
      data: {
        action: "createEqptWithTemplate"
      },
      dataType: 'json',
      error: function(request, status, error) {
        handleAjaxError(request, status, error);
      },
      success: function(data) {
        if (data.state != 'ok') {
          $('#div_alert').showAlert({
            message: data.result,
            level: 'danger'
          });
          return;
        } else {
          window.toastr.clear()
          $('.pluginDisplayCard[data-plugin_id=' + $('#span_plugin_id').text() + ']').click()
          $('#div_alert').showAlert({
            message: '{{Installation réussie}}',
            level: 'success'
          });

        }
      }
    });
  });
</script>   

