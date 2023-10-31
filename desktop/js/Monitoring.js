
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

function addCmdToTable(_cmd) {
	if (!isset(_cmd)) {
		var _cmd = {configuration: {}};
	}
	var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
	tr += '<td>';
	tr += '<span class="cmdAttr" data-l1key="id" ></span>';
	tr += '</td>';
	tr += '<td>';
	tr += '<input class="cmdAttr form-control input-sm" data-l1key="type" value="info" style="display: none">';
	tr += '<input class="cmdAttr form-control input-sm" data-l1key="name"">';
		if (_cmd.logicalId == 'perso1' || _cmd.logicalId == 'perso2') {
		tr += '<a class="cmdAction btn btn-default btn-sm" data-l1key="chooseIcon"><i class="fas fa-flag"></i> Icone</a>';
		tr += '<span class="cmdAttr cmdAction" data-l1key="display" data-l2key="icon" style="margin-left : 10px;"></span>';
	}
	tr += '</td>';
	tr += '<td>';
	if (_cmd.logicalId == 'loadavg1mn' || _cmd.logicalId == 'loadavg5mn' || _cmd.logicalId == 'loadavg15mn' || _cmd.logicalId == 'cpu_temp' || _cmd.logicalId == 'hddpourcused' || _cmd.logicalId == 'hddpourcusedv2') {
		tr += '<span style="color: green" >vert inférieur à <input class="cmdAttr eqLogicAttr form-control" data-l1key="configuration" data-l2key="' + init(_cmd.logicalId) + 'vertinfa" type="text" style="width: 60px;display: inherit" > - <span style="color: orange" >Orange entre <input class="cmdAttr eqLogicAttr form-control" data-l1key="configuration" data-l2key="' + init(_cmd.logicalId) + 'orangede" style="width: 60px;display: inherit" > et <input class="cmdAttr eqLogicAttr form-control" data-l1key="configuration" data-l2key="' + init(_cmd.logicalId) + 'orangea" style="width: 60px;display: inherit"></span> - <span style="color: red" >Rouge sup à <input class="cmdAttr eqLogicAttr form-control" data-l1key="configuration" data-l2key="' + init(_cmd.logicalId) + 'rougesupa" style="width: 60px;display: inherit" ></span>';
	}
	if (_cmd.logicalId == 'Mempourc' || _cmd.logicalId == 'Swappourc') {
		tr += '<span style="color: red" >rouge inférieur à <input class="cmdAttr eqLogicAttr form-control" data-l1key="configuration" data-l2key="' + init(_cmd.logicalId) + 'rougeinfa" style="width: 60px;display: inherit" > - <span style="color: orange" >Orange entre <input class="cmdAttr eqLogicAttr form-control" data-l1key="configuration" data-l2key="' + init(_cmd.logicalId) + 'orangede" style="width: 60px;display: inherit" > et <input class="cmdAttr eqLogicAttr form-control" data-l1key="configuration" data-l2key="' + init(_cmd.logicalId) + 'orangea" style="width: 60px;display: inherit"></span> - <span style="color: green" >Vert sup à <input class="cmdAttr eqLogicAttr form-control" data-l1key="configuration" data-l2key="' + init(_cmd.logicalId) + 'vertsupa" style="width: 60px;display: inherit" ></span>';
	}
	if (_cmd.logicalId == 'perso1' || _cmd.logicalId == 'perso2') {
		tr += '<span><input class="cmdAttr eqLogicAttr form-control" data-l1key="configuration" data-l2key="' + init(_cmd.logicalId) + '" style="width: 70%;display: inherit" ></input></span>';
		tr += '<span> Unité : <input class="cmdAttr eqLogicAttr form-control" data-l1key="configuration" data-l2key="' + init(_cmd.logicalId) + '_unite" style="width: 10%;display: inherit" ></input></span>';
        tr += '<br/><span style="color: green" >vert inférieur à <input class="cmdAttr eqLogicAttr form-control" data-l1key="configuration" data-l2key="' + init(_cmd.logicalId) + 'vertinfa" type="text" style="width: 60px;display: inherit" > - <span style="color: orange" >Orange entre <input class="cmdAttr eqLogicAttr form-control" data-l1key="configuration" data-l2key="' + init(_cmd.logicalId) + 'orangede" style="width: 60px;display: inherit" > et <input class="cmdAttr eqLogicAttr form-control" data-l1key="configuration" data-l2key="' + init(_cmd.logicalId) + 'orangea" style="width: 60px;display: inherit"></span> - <span style="color: red" >Rouge sup à <input class="cmdAttr eqLogicAttr form-control" data-l1key="configuration" data-l2key="' + init(_cmd.logicalId) + 'rougesupa" style="width: 60px;display: inherit" ></span>';
	}
	tr += '</td>';
	tr += '<td style="width: 150px;">';
	if (_cmd.logicalId == 'namedistri' || _cmd.logicalId == 'uptime' || _cmd.logicalId == 'loadavg1mn' || _cmd.logicalId == 'Mem' || _cmd.logicalId == 'Mem_swap' || _cmd.logicalId == 'ethernet0' || _cmd.logicalId == 'hddtotal' || _cmd.logicalId == 'cpu_temp' || _cmd.logicalId == 'hddtotalv2' || _cmd.logicalId == 'cpu' || _cmd.logicalId == 'perso1' || _cmd.logicalId == 'perso2') {
		tr += '<span><input type="checkbox" class="cmdAttr" data-size="mini" data-l1key="isVisible" checked/> {{Afficher}}<br/></span>';
	}
	if (_cmd.logicalId == 'perso1' || _cmd.logicalId == 'perso2' || _cmd.logicalId == 'loadavg1mn' || _cmd.logicalId == 'loadavg5mn' || _cmd.logicalId == 'loadavg15mn' || _cmd.logicalId == 'Mempourc' || _cmd.logicalId == 'Swappourc' || _cmd.logicalId == 'cpu_temp' || _cmd.logicalId == 'hddpourcused' || _cmd.logicalId == 'hddpourcusedv2') {
		tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="isHistorized"/> {{Historiser}}</span>';
	}

	tr += '</td>';
	tr += '<td>';
	if (_cmd.logicalId == 'perso1' || _cmd.logicalId == 'perso2') {
		tr += '<span class="type" type="info"</span>';
		tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
	}
	tr += '</td>';
	tr += '<td>';
	if (is_numeric(_cmd.id)) {
		tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> {{Tester}}</a>';
	}
	tr += '<i class="fas fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
	tr += '</tr>';
	$('#table_cmd tbody').append(tr);
	$('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
	jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}



$(".eqLogicAttr[data-l2key='synology']").on('change', function () {
	if(this.checked){
	  $(".syno_conf").show();
	}else{
	  $(".syno_conf").hide();
	}
  });
  $(".eqLogicAttr[data-l2key='syno_use_temp_path']").on('change', function () {
	if(this.checked){
	  $(".syno_conf_temppath").show();
	}else{
	  $(".syno_conf_temppath").hide();
	}
  });