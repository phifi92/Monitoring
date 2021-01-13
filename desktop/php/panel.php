<?php
if (!isConnect()) {
	throw new Exception('{{401 - Accès non autorisé}}');
}

sendVarToJs('jeedomBackgroundImg', 'plugins/Monitoring/core/img/panel.jpg');

if (init('object_id') == '') {
	$object = JeeObject::byId($_SESSION['user']->getOptions('defaultDashboardObject'));
} else {
	$object = JeeObject::byId(init('object_id'));
}
if (!is_object($object)) {
	$object = JeeObject::rootObject();
}
if (!is_object($object)) {
	throw new Exception('{{Aucun objet racine trouvé. Pour en créer un, allez dans Générale -> Objet.<br/> Si vous ne savez pas quoi faire ou que c\'est la premiere fois que vous utilisez Jeedom n\'hésitez pas a consulter cette <a href="http://jeedom.fr/premier_pas.php" target="_blank">page</a>}}');
}
$child_object = JeeObject::buildTree($object);
$parentNumber = array();
?>

<div>
    <?php
if ($_SESSION['user']->getOptions('displayObjetByDefault') == 1 && init('report') != 1) {
	echo '<div id="div_displayObjectList">';
} else {
	echo '<div style="display:none;" id="div_displayObjectList">';
}
?>
	<div class="bs-sidebar">
		<ul id="ul_object" class="nav nav-list bs-sidenav">
			<li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
			<?php
				$allObject = JeeObject::buildTree(null, true);
				foreach ($allObject as $object_li) {
					$margin = 5 * $object_li->getConfiguration('parentNumber');
					if ($object_li->getId() == $object->getId()) {
						echo '<li class="cursor li_object active" ><a data-object_id="' . $object_li->getId() . '" href="index.php?v=d&p=panel&m=Monitoring&object_id=' . $object_li->getId() . '" style="padding: 2px 0px;"><span style="position:relative;left:' . $margin . 'px;">' . $object_li->getHumanName(true) . '</span><span style="font-size : 0.65em;float:right;position:relative;top:7px;">' . $object_li->getHtmlSummary() . '</span></a></li>';
					} else {
						echo '<li class="cursor li_object" ><a data-object_id="' . $object_li->getId() . '" href="index.php?v=d&p=panel&m=Monitoring&object_id=' . $object_li->getId() . '" style="padding: 2px 0px;"><span style="position:relative;left:' . $margin . 'px;">' . $object_li->getHumanName(true) . '</span><span style="font-size : 0.65em;float:right;position:relative;top:7px;">' . $object_li->getHtmlSummary() . '</span></a></li>';
					}
				}
			?>
		</ul>
	</div>
</div>
<?php
if ($_SESSION['user']->getOptions('displayObjetByDefault') == 1 && init('report') != 1) {
	echo '<div id="div_displayObject">';
} else {
	echo '<div id="div_displayObject">';
}
?>
<i id='bt_displayObject' data-display='<?php echo $_SESSION['user']->getOptions('displayObjetByDefault') ?>' title="Afficher/Masquer les objets"></i>
<i id="bt_editDashboardWidgetOrder" data-mode="0""></i>
<br/>
<?php
echo '<div class="div_displayEquipement" style="width: 100%;">';
if (init('object_id') == '') {
	foreach ($allObject as $object) {
		foreach ($object->getEqLogic(true, false, 'Monitoring') as $Monitoring) {
			echo $Monitoring->toHtml('dview');
		}
	}
} else {
	foreach ($object->getEqLogic(true, false, 'Monitoring') as $Monitoring) {
		echo $Monitoring->toHtml('dview');
	}
	foreach ($child_object as $child) {
		$Monitoring = $child->getEqLogic(true, false, 'Monitoring');
		if (count($Monitoring) > 0) {
			foreach ($Monitoring as $Monitoring) {
				echo $Monitoring->toHtml('dview');
			}
		}
	}
}
echo '</div>';
?>
</div>
</div>
<?php include_file('desktop', 'panel', 'js', 'Monitoring');?>
