<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('mczremote');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>


<?php Code removed bu author due to non respone by Jeedom ?>

<?php include_file('desktop', 'mczremote', 'js', 'mczremote');?>
<?php include_file('core', 'plugin.template', 'js');?>
