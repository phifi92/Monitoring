<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}

$plugin = plugin::byId('Monitoring');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
	<div class="col-lg-2 col-md-3 col-sm-4">
		<div class="bs-sidebar">
			<ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
				<a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter Monitoring}}</a>
				<li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
				<?php
					foreach ($eqLogics as $eqLogic) {
					echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
					}
				?>
			</ul>
		</div>
	</div>
	<div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
		<legend><i class="fa fa-cog"></i>  {{Gestion}}</legend>
		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction" data-action="add" style="background-color : #ffffff; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
				<center><i class="fa fa-plus-circle" style="font-size : 6em;color:#94ca02;"></i></center>
			<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;;color:#94ca02"><center>{{Ajouter}}</center></span>
			</div>
			<div class="cursor eqLogicAction" data-action="gotoPluginConf" style="background-color : #ffffff; height : 140px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
				<center><i class="fa fa-wrench" style="font-size : 6em;color:#767676;"></i></center>
				<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Configuration}}</center></span>
			</div>
		</div>
		<legend><i class="fa fa-bolt"></i>  {{Mes Monitorings}}</legend>
		<div class="eqLogicThumbnailContainer">
			<?php
				foreach ($eqLogics as $eqLogic) {
				$opacity = '';
				if ($eqLogic->getIsEnable() != 1) {
					$opacity = 'opacity:0.3;';
				}
				echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
				echo "<center>";
				echo '<img src="/plugins/Monitoring/doc/images/monitoring_icon.png" height="105" width="95" />';
				echo "</center>";
				echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
				echo '</div>';
				}
			?>
		</div>
	</div>

	<div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
		<a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
		<a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
		<a class="btn btn-default eqLogicAction pull-right" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a>
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Equipement}}</a></li>
			<li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
		</ul>
		<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
				<br/>
				<div class="row">
					<div class="col-xs-6">
						<form class="form-horizontal">
						<fieldset>
							<legend><i class="fa fa-arrow-circle-left eqLogicAction cursor" data-action="returnToThumbnailDisplay"></i> {{Général}}<i class='fa fa-cogs eqLogicAction pull-right cursor' data-action='configure'></i></legend>
						<div class="form-group">
							<label class="col-md-4 control-label">{{Nom de l'équipement Monitoring}}</label>
							<div class="col-md-8">
								<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
								<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement Monitoring}}"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" >{{Objet parent}}</label>
							<div class="col-md-8">
								<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
									<option value="">{{Aucun}}</option>
									<?php
										foreach (object::all() as $object) {
										echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
										}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">{{Catégorie}}</label>
							<div class="col-sm-8">
								<?php
									foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
									echo '<label class="checkbox-inline">';
									echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
									echo '</label>';
									}
								?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label"></label>
							<div class="col-md-8">
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label">{{Carte Réseau}}</label>
							<div class="col-md-8">
								<select id="cartereseau" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="cartereseau"
					  onchange="if(this.selectedIndex == 2) document.getElementById('netautre').style.display = 'block';
					  else document.getElementById('netautre').style.display = 'none';">
									<option value="eth0">{{1er port Ethernet (par défaut)}}</option>
									<option value="wlan0">{{1er port Wi-Fi}}</option>
									<option value="netautre">{{Autre}}</option>
								</select>
							</div>
						</div>
						<div id="netautre">
							<div class="form-group">
								<label class="col-md-4 control-label">{{Nom de la carte réseau}}</label>
								<div class="col-md-8">
									<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="cartereseauautre" type="text" placeholder="{{saisir le nom de la carte}}">
									<span style="font-size: 75%;">(eth1 : 2éme port Ethernet, wlan1 : 2éme port Wi-Fi...)</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label">{{Local ou déporté ?}}</label>
							<div class="col-md-8">
								<select id="maitreesclave" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="maitreesclave"
					  onchange="if(this.selectedIndex == 1) document.getElementById('deporte').style.display = 'block';
					  else document.getElementById('deporte').style.display = 'none';">
									<option value="local">{{Local}}</option>
									<option value="deporte">{{Déporté}}</option>
								</select>
							</div>
						</div>
						<div id="deporte">
							<div class="form-group">
								<label class="col-md-4 control-label">{{Adresse IP}}</label>
								<div class="col-md-8">
									<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="addressip" type="text" placeholder="{{saisir l'adresse IP}}">
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label">{{Port SSH}}</label>
								<div class="col-md-8">
									<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="portssh" type="text" placeholder="{{saisir le port SSH}}">
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label">{{Identifiant}}</label>
								<div class="col-md-8">
									<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="user" type="text" placeholder="{{saisir le login}}">
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label">{{Mot de passe}}</label>
								<div class="col-md-8">
									<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="password" type="password" placeholder="{{saisir le password}}">
								</div>
							</div>
						</div>
					</fieldset>
					</form>
				</div>
				<div class="col-xs-6">
					<form class="form-horizontal">
					<fieldset>
						<legend>{{NAS Synology}}</legend>
						<div class="form-group">
							<label class="col-md-2 control-label" >{{Activer}}</label>
							<div class="col-md-8">
								<input type="checkbox" class="eqLogicAttr" data-l1key="configuration"  data-l2key="synology">
								<span style="font-size: 75%;">(à cocher seulement si vous désirez Monitorer un NAS Synology)</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label" >{{Volume 2}}</label>
							<div class="col-md-8">
								<input type="checkbox" class="eqLogicAttr" data-l1key="configuration"  data-l2key="synologyv2" >
								<span style="font-size: 75%;">(à cocher seulement si vous avez un 2ème volume (Volume 2) dans Synology. Le volume 1 est pris en compte par défaut)</span>
							</div>
						</div>
					</fieldset>
					</form>
				</div>
			</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="commandtab">
				<br/>
				<legend><i class="fa fa-arrow-circle-left eqLogicAction cursor" data-action="returnToThumbnailDisplay"></i> {{Commandes}}</legend>

				<table id="table_cmd" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th>{{Id}}</th>
							<th>{{Nom}}</th>
							<th>{{Colorisation des valeurs}}</th>
							<th>{{Afficher/Historiser}}</th>
							<th>{{Action}}</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
</div>

<?php include_file('desktop', 'Monitoring', 'js', 'Monitoring'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>
