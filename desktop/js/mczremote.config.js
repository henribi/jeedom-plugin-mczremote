
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
// Namespace
mczremote_config = {};

$(document).ready(function() {
    // Display the real version number (X.Y.Z) just before the plugin version number (YYYY-MM-DD hh:mm:ss)
    var dateVersion = $("#span_plugin_install_date").html();
    $("#span_plugin_install_date").empty().append("v" + version + " (" + dateVersion + ")");

});

