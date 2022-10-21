
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
        $('#div_alert').showAlert({ message: data.result, level: 'danger' });
        return;
      } 
      else {
        //window.toastr.clear()
        //$('.pluginDisplayCard[data-plugin_id=' + $('#span_plugin_id').text() + ']').click()
        $('#div_alert').showAlert({message: '{{Installation réussie}}',level: 'success' });
      }

    }
  });
});

$('#bt_CreateEqWithTemplate').off('click').on('click', function() {
    var prompt = "{{Nom de l'équipement ?}}";
    bootbox.prompt(prompt, function (result) {
        if (result !== null && result !== '') {
            $('#div_alert').showAlert({message: result, level: 'success'});

            $.ajax({
               type: "POST",
               url: "plugins/mczremote/core/ajax/mczremote.ajax.php",
               data: {
                 action: "createEqptWithTemplate",
                 eqptName: result
               },
               dataType: 'json',
               error: function(request, status, error) {
                 handleAjaxError(request, status, error);
               },
               success: function(data) {
                 if (data.state != 'ok') {
                   $('#div_alert').showAlert({ message: data.result, level: 'danger' });
                   return;
                 } else {
                   //window.toastr.clear()
                   //$('.pluginDisplayCard[data-plugin_id=' + $('#span_plugin_id').text() + ']').click()
                   $('#div_alert').showAlert({message: '{{Installation réussie}}',level: 'success' });
                 }
               }
             });
        } 
    });
});



$('#bt_healthmczremote').on('click', function () {
    $('#div_alert').showAlert({message: '{{Communication en cours}} (MCZRemote Démon)', level: 'warning'});
    $.ajax({
        type: "POST", // méthode de transmission des données au fichier php
        url: "plugins/mczremote/core/ajax/mczremote.ajax.php",
        data: {
            action: "health",
        },
        dataType: 'json',
        global: false,
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            $('#div_alert').showAlert({message: '{{Communication terminée avec succès (MCZRemote Démon)}}', level: 'success'});
        }
    });
});

$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
/*
 * Fonction pour l'ajout de commande, appellé automatiquement par plugin.template
 */
function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom}}">';
    tr += '</td>';
    tr += '<td>';
    tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>';
    tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
    tr += '</td>';
    tr += '<td>';
    tr += '<td>';
    tr += '<span class="cmdAttr" data-l1key="htmlstate"></span>';
    tr += '</td>';

    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fa fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
    tr += '</td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    if (isset(_cmd.type)) {
        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
    }
    jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}

