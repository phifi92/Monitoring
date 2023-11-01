<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; witfhout even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *f
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * *************************** Requires ********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

/* * *************************** Includes ********************************* */
include_once('phpseclib/autoload.php');
use phpseclib\Net\SSH2;

class Monitoring extends eqLogic {

	public static function pull() {
		foreach (eqLogic::byType('Monitoring', true) as $Monitoring) {
			$Monitoring->getInformations();
			$mc = cache::byKey('MonitoringWidgetmobile' . $Monitoring->getId());
			$mc->remove();
			$mc = cache::byKey('MonitoringWidgetdashboard' . $Monitoring->getId());
			$mc->remove();
			$Monitoring->toHtml('mobile');
			$Monitoring->toHtml('dashboard');
			$Monitoring->refreshWidget();
		}
	}

	public static function dependancy_info() {
		$return = array();
		$return['log'] = 'Monitoring_update';
		$return['progress_file'] = '/tmp/dependancy_monitoring_in_progress';
		if (file_exists('/tmp/dependancy_monitoring_in_progress')) {
			$return['state'] = 'in_progress';
		} else {
			if (exec('apt list --installed 2>/dev/null | grep php-phpseclib | wc -l') != 0) {
				$return['state'] = 'ok';
			} else {
				$return['state'] = 'nok';
			}
		}
		return $return;
	}

	public static function dependancy_install() {
		if (file_exists('/tmp/compilation_monitoring_in_progress')) {
			return;
		}
		log::remove('Monitoring_update');
		$cmd = 'sudo /bin/bash ' . dirname(__FILE__) . '/../../ressources/install.sh';
		$cmd .= ' >> ' . log::getPathToLog('Monitoring_update') . ' 2>&1 &';
		exec($cmd);
	}

	public function postSave() {

		$MonitoringCmd = $this->getCmd(null, 'namedistri');
		if (!is_object($MonitoringCmd)) {
			$MonitoringCmd = new MonitoringCmd();
			$MonitoringCmd->setName(__('Distribution', __FILE__));
			$MonitoringCmd->setEqLogic_id($this->getId());
			$MonitoringCmd->setLogicalId('namedistri');
			$MonitoringCmd->setType('info');
			$MonitoringCmd->setSubType('string');
			$MonitoringCmd->setIsVisible(1);
			$MonitoringCmd->save();
		}

		$MonitoringCmd = $this->getCmd(null, 'uptime');
		if (!is_object($MonitoringCmd)) {
			$MonitoringCmd = new MonitoringCmd();
			$MonitoringCmd->setName(__('Démarré depuis', __FILE__));
			$MonitoringCmd->setEqLogic_id($this->getId());
			$MonitoringCmd->setLogicalId('uptime');
			$MonitoringCmd->setType('info');
			$MonitoringCmd->setSubType('string');
			$MonitoringCmd->save();
		}

		$MonitoringCmd = $this->getCmd(null, 'loadavg1mn');
		if (!is_object($MonitoringCmd)) {
			$MonitoringCmd = new MonitoringCmd();
			$MonitoringCmd->setName(__('Charge système 1 min', __FILE__));
			$MonitoringCmd->setEqLogic_id($this->getId());
			$MonitoringCmd->setLogicalId('loadavg1mn');
			$MonitoringCmd->setType('info');
			$MonitoringCmd->setSubType('numeric');
			$MonitoringCmd->save();
		}

		$MonitoringCmd = $this->getCmd(null, 'loadavg5mn');
		if (!is_object($MonitoringCmd)) {
			$MonitoringCmd = new MonitoringCmd();
			$MonitoringCmd->setName(__('Charge système 5 min', __FILE__));
			$MonitoringCmd->setEqLogic_id($this->getId());
			$MonitoringCmd->setLogicalId('loadavg5mn');
			$MonitoringCmd->setType('info');
			$MonitoringCmd->setSubType('numeric');
			$MonitoringCmd->save();
		}

		$MonitoringCmd = $this->getCmd(null, 'loadavg15mn');
		if (!is_object($MonitoringCmd)) {
			$MonitoringCmd = new MonitoringCmd();
			$MonitoringCmd->setName(__('Charge système 15 min', __FILE__));
			$MonitoringCmd->setEqLogic_id($this->getId());
			$MonitoringCmd->setLogicalId('loadavg15mn');
			$MonitoringCmd->setType('info');
			$MonitoringCmd->setSubType('numeric');
			$MonitoringCmd->save();
		}

		$MonitoringCmd = $this->getCmd(null, 'Mem');
		if (!is_object($MonitoringCmd)) {
			$MonitoringCmd = new MonitoringCmd();
			$MonitoringCmd->setName(__('Mémoire (Méga)', __FILE__));
			$MonitoringCmd->setEqLogic_id($this->getId());
			$MonitoringCmd->setLogicalId('Mem');
			$MonitoringCmd->setType('info');
			$MonitoringCmd->setSubType('string');
			$MonitoringCmd->save();
		}

		$MonitoringCmd = $this->getCmd(null, 'Mempourc');
		if (!is_object($MonitoringCmd)) {
			$MonitoringCmd = new MonitoringCmd();
			$MonitoringCmd->setName(__('Mémoire libre (pourcentage)', __FILE__));
			$MonitoringCmd->setEqLogic_id($this->getId());
			$MonitoringCmd->setLogicalId('Mempourc');
			$MonitoringCmd->setType('info');
			$MonitoringCmd->setSubType('numeric');
			$MonitoringCmd->save();
		}

		$MonitoringCmd = $this->getCmd(null, 'Mem_swap');
		if (!is_object($MonitoringCmd)) {
			$MonitoringCmd = new MonitoringCmd();
			$MonitoringCmd->setName(__('Swap', __FILE__));
			$MonitoringCmd->setEqLogic_id($this->getId());
			$MonitoringCmd->setLogicalId('Mem_swap');
			$MonitoringCmd->setType('info');
			$MonitoringCmd->setSubType('string');
			$MonitoringCmd->save();
		}

		$MonitoringCmd = $this->getCmd(null, 'Swappourc');
		if (!is_object($MonitoringCmd)) {
			$MonitoringCmd = new MonitoringCmd();
			$MonitoringCmd->setName(__('Swap libre (pourcentage)', __FILE__));
			$MonitoringCmd->setEqLogic_id($this->getId());
			$MonitoringCmd->setLogicalId('Swappourc');
			$MonitoringCmd->setType('info');
			$MonitoringCmd->setSubType('numeric');
			$MonitoringCmd->save();
		}

		$MonitoringCmd = $this->getCmd(null, 'ethernet0');
		if (!is_object($MonitoringCmd)) {
			$MonitoringCmd = new MonitoringCmd();
			$MonitoringCmd->setName(__('Réseau (M)', __FILE__));
			$MonitoringCmd->setEqLogic_id($this->getId());
			$MonitoringCmd->setLogicalId('ethernet0');
			$MonitoringCmd->setType('info');
			$MonitoringCmd->setSubType('string');
			$MonitoringCmd->save();
		}

		$MonitoringCmd = $this->getCmd(null, 'hddtotal');
		if (!is_object($MonitoringCmd)) {
			$MonitoringCmd = new MonitoringCmd();
			$MonitoringCmd->setName(__('Espace disque Total', __FILE__));
			$MonitoringCmd->setEqLogic_id($this->getId());
			$MonitoringCmd->setLogicalId('hddtotal');
			$MonitoringCmd->setType('info');
			$MonitoringCmd->setSubType('string');
			$MonitoringCmd->save();
		}

		$MonitoringCmd = $this->getCmd(null, 'hddused');
		if (!is_object($MonitoringCmd)) {
			$MonitoringCmd = new MonitoringCmd();
			$MonitoringCmd->setName(__('Espace disque Utilisé', __FILE__));
			$MonitoringCmd->setEqLogic_id($this->getId());
			$MonitoringCmd->setLogicalId('hddused');
			$MonitoringCmd->setType('info');
			$MonitoringCmd->setSubType('string');
			$MonitoringCmd->save();
		}

		$MonitoringCmd = $this->getCmd(null, 'hddpourcused');
		if (!is_object($MonitoringCmd)) {
			$MonitoringCmd = new MonitoringCmd();
			$MonitoringCmd->setName(__('Espace disque Utilisé (pourcentage)', __FILE__));
			$MonitoringCmd->setEqLogic_id($this->getId());
			$MonitoringCmd->setLogicalId('hddpourcused');
			$MonitoringCmd->setType('info');
			$MonitoringCmd->setSubType('numeric');
			$MonitoringCmd->save();
		}

		if ($this->getConfiguration('synology') == '1') {
			if ($this->getConfiguration('synologyv2') == '1') {
				$MonitoringCmd = $this->getCmd(null, 'hddtotalv2');
				if (!is_object($MonitoringCmd)) {
					$MonitoringCmd = new MonitoringCmd();
					$MonitoringCmd->setName(__('Syno Volume 2 Espace disque Total', __FILE__));
					$MonitoringCmd->setEqLogic_id($this->getId());
					$MonitoringCmd->setLogicalId('hddtotalv2');
					$MonitoringCmd->setType('info');
					$MonitoringCmd->setSubType('string');
					$MonitoringCmd->save();
				}

				$MonitoringCmd = $this->getCmd(null, 'hddusedv2');
				if (!is_object($MonitoringCmd)) {
					$MonitoringCmd = new MonitoringCmd();
					$MonitoringCmd->setName(__('Syno Volume 2 Espace disque Utilisé', __FILE__));
					$MonitoringCmd->setEqLogic_id($this->getId());
					$MonitoringCmd->setLogicalId('hddusedv2');
					$MonitoringCmd->setType('info');
					$MonitoringCmd->setSubType('string');
					$MonitoringCmd->save();
				}

				$MonitoringCmd = $this->getCmd(null, 'hddpourcusedv2');
				if (!is_object($MonitoringCmd)) {
					$MonitoringCmd = new MonitoringCmd();
					$MonitoringCmd->setName(__('Syno Volume 2 Espace disque Utilisé (pourcentage)', __FILE__));
					$MonitoringCmd->setEqLogic_id($this->getId());
					$MonitoringCmd->setLogicalId('hddpourcusedv2');
					$MonitoringCmd->setType('info');
					$MonitoringCmd->setSubType('numeric');
					$MonitoringCmd->save();
				}

			} elseif ($this->getConfiguration('synologyv2') == '0') {
				$MonitoringCmd = $this->getCmd(null, 'hddtotalv2');
				if ( is_object($MonitoringCmd)) {
					$MonitoringCmd->remove();
				}
				$MonitoringCmd = $this->getCmd(null, 'hddusedv2');
				if ( is_object($MonitoringCmd)) {
					$MonitoringCmd->remove();
				}
				$MonitoringCmd = $this->getCmd(null, 'hddpourcusedv2');
				if ( is_object($MonitoringCmd)) {
					$MonitoringCmd->remove();
				}
			}
		}

		$MonitoringCmd = $this->getCmd(null, 'cpu');
		if (!is_object($MonitoringCmd)) {
			$MonitoringCmd = new MonitoringCmd();
			$MonitoringCmd->setName(__('CPU(s)', __FILE__));
			$MonitoringCmd->setEqLogic_id($this->getId());
			$MonitoringCmd->setLogicalId('cpu');
			$MonitoringCmd->setType('info');
			$MonitoringCmd->setSubType('string');
			$MonitoringCmd->save();
		}

		$MonitoringCmd = $this->getCmd(null, 'cpu_temp');
		if (!is_object($MonitoringCmd)) {
			$MonitoringCmd = new MonitoringCmd();
			$MonitoringCmd->setName(__('Température CPU', __FILE__));
			$MonitoringCmd->setEqLogic_id($this->getId());
			$MonitoringCmd->setLogicalId('cpu_temp');
			$MonitoringCmd->setType('info');
			$MonitoringCmd->setSubType('numeric');
			$MonitoringCmd->save();
		}

		$MonitoringCmd = $this->getCmd(null, 'cnx_ssh');
		if (!is_object($MonitoringCmd)) {
			$MonitoringCmd = new MonitoringCmd();
			$MonitoringCmd->setName(__('Statut cnx SSH Scénario', __FILE__));
			$MonitoringCmd->setEqLogic_id($this->getId());
			$MonitoringCmd->setLogicalId('cnx_ssh');
			$MonitoringCmd->setType('info');
			$MonitoringCmd->setSubType('string');
			$MonitoringCmd->save();
		}


		$MonitoringCmd = $this->getCmd(null, 'perso2');
		if (!is_object($MonitoringCmd)) {
			$MonitoringCmd = new MonitoringCmd();
			$MonitoringCmd->setName(__('perso2', __FILE__));
			$MonitoringCmd->setEqLogic_id($this->getId());
			$MonitoringCmd->setLogicalId('perso2');
			$MonitoringCmd->setType('info');
			$MonitoringCmd->setSubType('string');
			$MonitoringCmd->save();
		}

		$MonitoringCmd = $this->getCmd(null, 'perso1');
		if (!is_object($MonitoringCmd)) {
			$MonitoringCmd = new MonitoringCmd();
			$MonitoringCmd->setName(__('perso1', __FILE__));
			$MonitoringCmd->setEqLogic_id($this->getId());
			$MonitoringCmd->setLogicalId('perso1');
			$MonitoringCmd->setType('info');
			$MonitoringCmd->setSubType('string');
			$MonitoringCmd->save();
		}

		$MonitoringCmd = $this->getCmd(null, 'reboot');
		if (!is_object($MonitoringCmd)) {
			$MonitoringCmd = new MonitoringCmd();
			$MonitoringCmd->setName(__('reboot', __FILE__));
			$MonitoringCmd->setEqLogic_id($this->getId());
			$MonitoringCmd->setLogicalId('reboot');
			$MonitoringCmd->setType('action');
			$MonitoringCmd->setSubType('other');
			$MonitoringCmd->save();
		}

		$MonitoringCmd = $this->getCmd(null, 'poweroff');
		if (!is_object($MonitoringCmd)) {
			$MonitoringCmd = new MonitoringCmd();
			$MonitoringCmd->setName(__('poweroff', __FILE__));
			$MonitoringCmd->setEqLogic_id($this->getId());
			$MonitoringCmd->setLogicalId('poweroff');
			$MonitoringCmd->setType('action');
			$MonitoringCmd->setSubType('other');
			$MonitoringCmd->save();
		}

		$this->getInformations();
	}

	public static $_widgetPossibility = array('custom' => true, 'custom::layout' => false);

	public function toHtml($_version = 'dashboard') {
		$replace = $this->preToHtml($_version);
		if (!is_array($replace)) {
			return $replace;
		}
		$_version = jeedom::versionAlias($_version);

		$replace ['#loadavg1mnvertinfa#'] = $this->getConfiguration('loadavg1mnvertinfa');
		$replace ['#loadavg5mnvertinfa#'] = $this->getConfiguration('loadavg5mnvertinfa');
		$replace ['#loadavg15mnvertinfa#'] = $this->getConfiguration('loadavg15mnvertinfa');
		$replace ['#loadavg1mnorangede#'] = $this->getConfiguration('loadavg1mnorangede');
		$replace ['#loadavg5mnorangede#'] = $this->getConfiguration('loadavg5mnorangede');
		$replace ['#loadavg15mnorangede#'] = $this->getConfiguration('loadavg15mnorangede');
		$replace ['#loadavg1mnorangea#'] = $this->getConfiguration('loadavg1mnorangea');
		$replace ['#loadavg5mnorangea#'] = $this->getConfiguration('loadavg5mnorangea');
		$replace ['#loadavg15mnorangea#'] = $this->getConfiguration('loadavg15mnorangea');
		$replace ['#loadavg1mnrougesupa#'] = $this->getConfiguration('loadavg1mnrougesupa');
		$replace ['#loadavg5mnrougesupa#'] = $this->getConfiguration('loadavg5mnrougesupa');
		$replace ['#loadavg15mnrougesupa#'] = $this->getConfiguration('loadavg15mnrougesupa');
		$replace ['#Mempourcvertsupa#'] = $this->getConfiguration('Mempourcvertsupa');
		$replace ['#Mempourcorangede#'] = $this->getConfiguration('Mempourcorangede');
		$replace ['#Mempourcorangea#'] = $this->getConfiguration('Mempourcorangea');
		$replace ['#Mempourcrougeinfa#'] = $this->getConfiguration('Mempourcrougeinfa');
		$replace ['#Swappourcvertsupa#'] = $this->getConfiguration('Swappourcvertsupa');
		$replace ['#Swappourcorangede#'] = $this->getConfiguration('Swappourcorangede');
		$replace ['#Swappourcorangea#'] = $this->getConfiguration('Swappourcorangea');
		$replace ['#Swappourcrougeinfa#'] = $this->getConfiguration('Swappourcrougeinfa');
		$replace ['#cpu_tempvertinfa#'] = $this->getConfiguration('cpu_tempvertinfa');
		$replace ['#cpu_temporangede#'] = $this->getConfiguration('cpu_temporangede');
		$replace ['#cpu_temporangea#'] = $this->getConfiguration('cpu_temporangea');
		$replace ['#cpu_temprougesupa#'] = $this->getConfiguration('cpu_temprougesupa');
		$replace ['#hddpourcusedvertinfa#'] = $this->getConfiguration('hddpourcusedvertinfa');
		$replace ['#hddpourcusedorangede#'] = $this->getConfiguration('hddpourcusedorangede');
		$replace ['#hddpourcusedorangea#'] = $this->getConfiguration('hddpourcusedorangea');
		$replace ['#hddpourcusedrougesupa#'] = $this->getConfiguration('hddpourcusedrougesupa');
		$replace ['#hddpourcusedv2vertinfa#'] = $this->getConfiguration('hddpourcusedv2vertinfa');
		$replace ['#hddpourcusedv2orangede#'] = $this->getConfiguration('hddpourcusedv2orangede');
		$replace ['#hddpourcusedv2orangea#'] = $this->getConfiguration('hddpourcusedv2orangea');
		$replace ['#hddpourcusedv2rougesupa#'] = $this->getConfiguration('hddpourcusedv2rougesupa');
		$replace ['#perso1vertinfa#'] = $this->getConfiguration('perso1vertinfa');
		$replace ['#perso1orangede#'] = $this->getConfiguration('perso1orangede');
		$replace ['#perso1orangea#'] = $this->getConfiguration('perso1orangea');
		$replace ['#perso1rougesupa#'] = $this->getConfiguration('perso1rougesupa');
		$replace ['#perso2vertinfa#'] = $this->getConfiguration('perso2vertinfa');
		$replace ['#perso2orangede#'] = $this->getConfiguration('perso2orangede');
		$replace ['#perso2orangea#'] = $this->getConfiguration('perso2orangea');
		$replace ['#perso2rougesupa#'] = $this->getConfiguration('perso2rougesupa');

		$namedistri = $this->getCmd(null,'namedistri');
		$replace['#namedistri#'] = (is_object($namedistri)) ? $namedistri->execCmd() : '';
		$replace['#namedistriid#'] = is_object($namedistri) ? $namedistri->getId() : '';
		$replace['#namedistri_display#'] = (is_object($namedistri) && $namedistri->getIsVisible()) ? "#namedistri_display#" : "none";


		$loadavg1mn = $this->getCmd(null,'loadavg1mn');
		$replace['#loadavg1mn#'] = (is_object($loadavg1mn)) ? $loadavg1mn->execCmd() : '';
		$replace['#loadavg1mnid#'] = is_object($loadavg1mn) ? $loadavg1mn->getId() : '';
		$replace['#loadavg_display#'] = (is_object($loadavg1mn) && $loadavg1mn->getIsVisible()) ? "#loadavg_display#" : "none";

		$loadavg5mn = $this->getCmd(null,'loadavg5mn');
		$replace['#loadavg5mn#'] = (is_object($loadavg5mn)) ? $loadavg5mn->execCmd() : '';
		$replace['#loadavg5mnid#'] = is_object($loadavg5mn) ? $loadavg5mn->getId() : '';

		$loadavg15mn = $this->getCmd(null,'loadavg15mn');
		$replace['#loadavg15mn#'] = (is_object($loadavg15mn)) ? $loadavg15mn->execCmd() : '';
		$replace['#loadavg15mnid#'] = is_object($loadavg15mn) ? $loadavg15mn->getId() : '';

		$uptime = $this->getCmd(null,'uptime');
		$replace['#uptime#'] = (is_object($uptime)) ? $uptime->execCmd() : '';
		$replace['#uptimeid#'] = is_object($uptime) ? $uptime->getId() : '';
		$replace['#uptime_display#'] = (is_object($uptime) && $uptime->getIsVisible()) ? "#uptime_display#" : "none";

		$Mem = $this->getCmd(null,'Mem');
		$replace['#Mem#'] = (is_object($Mem)) ? $Mem->execCmd() : '';
		$replace['#Memid#'] = is_object($Mem) ? $Mem->getId() : '';
		$replace['#Mem_display#'] = (is_object($Mem) && $Mem->getIsVisible()) ? "#Mem_display#" : "none";

		$Mem_swap = $this->getCmd(null,'Mem_swap');
		$replace['#Mem_swap#'] = (is_object($Mem_swap)) ? $Mem_swap->execCmd() : '';
		$replace['#Mem_swapid#'] = is_object($Mem_swap) ? $Mem_swap->getId() : '';
		$replace['#Mem_swap_display#'] = (is_object($Mem_swap) && $Mem_swap->getIsVisible()) ? "#Mem_swap_display#" : "none";

		$ethernet0 = $this->getCmd(null,'ethernet0');
		$replace['#ethernet0#'] = (is_object($ethernet0)) ? $ethernet0->execCmd() : '';
		$replace['#ethernet0id#'] = is_object($ethernet0) ? $ethernet0->getId() : '';
		$replace['#ethernet0_display#'] = (is_object($ethernet0) && $ethernet0->getIsVisible()) ? "#ethernet0_display#" : "none";

		$hddused = $this->getCmd(null,'hddused');
		$replace['#hddused#'] = (is_object($hddused)) ? $hddused->execCmd() : '';
		$replace['#hddusedid#'] = is_object($hddused) ? $hddused->getId() : '';

		$hddused_pourc = $this->getCmd(null,'hddpourcused');
		$replace['#hddpourcused#'] = (is_object($hddused_pourc)) ? $hddused_pourc->execCmd() : '';
		$replace['#hddpourcusedid#'] = is_object($hddused_pourc) ? $hddused_pourc->getId() : '';

		$hddtotal = $this->getCmd(null,'hddtotal');
		$replace['#hddtotal#'] = (is_object($hddtotal)) ? $hddtotal->execCmd() : '';
		$replace['#hddtotalid#'] = is_object($hddtotal) ? $hddtotal->getId() : '';
		$replace['#hddused_display#'] = (is_object($hddtotal) && $hddtotal->getIsVisible()) ? "#hddused_display#" : "none";

		$cpu = $this->getCmd(null,'cpu');
		$replace['#cpu#'] = (is_object($cpu)) ? $cpu->execCmd() : '';
		$replace['#cpuid#'] = is_object($cpu) ? $cpu->getId() : '';
		$replace['#cpu_display#'] = (is_object($cpu) && $cpu->getIsVisible()) ? "#cpu_display#" : "none";

		$SynoV2Visible = (is_object($this->getCmd(null,'hddusedv2')) && $this->getCmd(null,'hddusedv2')->getIsVisible()) ? 'OK' : '';

		if($this->getConfiguration('synology') == '1' && $SynoV2Visible == 'OK' && $this->getConfiguration('synologyv2') == '1'){
			$hddusedv2 = $this->getCmd(null,'hddusedv2');
			$replace['#hddusedv2#'] = (is_object($hddusedv2)) ? $hddusedv2->execCmd() : '';
			$replace['#hddusedv2id#'] = is_object($hddusedv2) ? $hddusedv2->getId() : '';

			$hddusedv2_pourc = $this->getCmd(null,'hddpourcusedv2');
			$replace['#hddpourcusedv2#'] = (is_object($hddusedv2_pourc)) ? $hddusedv2_pourc->execCmd() : '';
			$replace['#hddpourcusedv2id#'] = is_object($hddusedv2_pourc) ? $hddusedv2_pourc->getId() : '';

			$hddtotalv2 = $this->getCmd(null,'hddtotalv2');
			$replace['#hddtotalv2#'] = (is_object($hddtotalv2)) ? $hddtotalv2->execCmd() : '';
			$replace['#hddtotalv2id#'] = is_object($hddtotalv2) ? $hddtotalv2->getId() : '';
			$replace['#hddusedv2_display#'] = (is_object($hddtotalv2) && $hddtotalv2->getIsVisible()) ? "#hddusedv2_display#" : "none";
			$replace['#synovolume2_display#'] = (is_object($hddtotalv2) && $hddtotalv2->getIsVisible()) ? "OK" : "";
		}

		$cnx_ssh = $this->getCmd(null,'cnx_ssh');
		$replace['#cnx_ssh#'] = (is_object($cnx_ssh)) ? $cnx_ssh->execCmd() : '';
		$replace['#cnx_sshid#'] = is_object($cnx_ssh) ? $cnx_ssh->getId() : '';

		$Mempourc = $this->getCmd(null,'Mempourc');
		$replace['#Mempourc#'] = (is_object($Mempourc)) ? $Mempourc->execCmd() : '';
		$replace['#Mempourcid#'] = is_object($Mempourc) ? $Mempourc->getId() : '';

		$Swappourc = $this->getCmd(null,'Swappourc');
		$replace['#Swappourc#'] = (is_object($Swappourc)) ? $Swappourc->execCmd() : '';
		$replace['#Swappourcid#'] = is_object($Swappourc) ? $Swappourc->getId() : '';

		$cpu_temp = $this->getCmd(null,'cpu_temp');
		$replace['#cpu_temp#'] = (is_object($cpu_temp)) ? $cpu_temp->execCmd() : '';
		$replace['#cpu_tempid#'] = is_object($cpu_temp) ? $cpu_temp->getId() : '';

		$perso1 = $this->getCmd(null,'perso1');
		$replace['#perso1#'] = (is_object($perso1)) ? $perso1->execCmd() : '';
		$replace['#perso1id#'] = is_object($perso1) ? $perso1->getId() : '';
		$replace['#perso1_display#'] = (is_object($perso1) && $perso1->getIsVisible()) ? "#perso1_display#" : "none";
		$nameperso_1 = (is_object($perso1)) ? $this->getCmd(null,'perso1')->getName() : '';
		$iconeperso_1 = (is_object($perso1)) ? $this->getCmd(null,'perso1')->getdisplay('icon') : '';
		$replace['#nameperso1#'] = (is_object($perso1)) ? $nameperso_1 : "";
		$replace['#iconeperso1#'] = (is_object($perso1)) ? $iconeperso_1 : "";
		$perso_1unite = $this->getConfiguration('perso1_unite');
		$replace['#uniteperso1#'] = (is_object($perso1)) ? $perso_1unite : "";

		$perso2 = $this->getCmd(null,'perso2');
		$replace['#perso2#'] = (is_object($perso2)) ? $perso2->execCmd() : '';
		$replace['#perso2id#'] = is_object($perso2) ? $perso2->getId() : '';
		$replace['#perso2_display#'] = (is_object($perso2) && $perso2->getIsVisible()) ? "#perso2_display#" : "none";
		$nameperso_2 = (is_object($perso2)) ? $this->getCmd(null,'perso2')->getName() : '';
		$iconeperso_2 = (is_object($perso2)) ? $this->getCmd(null,'perso2')->getdisplay('icon') : '';
		$replace['#nameperso2#'] = (is_object($perso2)) ? $nameperso_2 : "";
		$replace['#iconeperso2#'] = (is_object($perso2)) ? $iconeperso_2 : "";
		$perso_2unite = $this->getConfiguration('perso2_unite');
		$replace['#uniteperso2#'] = (is_object($perso2)) ? $perso_2unite : "";

		foreach ($this->getCmd('action') as $cmd) {
			$replace['#cmd_' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
		}

		$html = template_replace($replace, getTemplate('core', $_version, 'Monitoring','Monitoring'));
		cache::set('MonitoringWidget' . $_version . $this->getId(), $html, 0);
		return $html;
	}

	public function getInformations() {

		// $swap_pourc_cmd = '';
		$bitdistri_cmd = '';
		$uname = "Inconnu";
		$Mem = '';
		$memorylibre_pourc = '';
		$ethernet0 = '';
		

		if ($this->getConfiguration('cartereseau') == 'netautre'){
			$cartereseau = $this->getConfiguration('cartereseauautre');
		}else{
			$cartereseau = $this->getConfiguration('cartereseau');
		}
		$SynoV2Visible = (is_object($this->getCmd(null,'hddusedv2')) && $this->getCmd(null,'hddusedv2')->getIsVisible()) ? 'OK' : '';
		if ($this->getConfiguration('maitreesclave') == 'deporte' && $this->getIsEnable()) {
			$ip = $this->getConfiguration('addressip');
			$user = $this->getConfiguration('user');
			$pass = $this->getConfiguration('password');
			$port = $this->getConfiguration('portssh');
			$equipement = $this->getName();

			if (!$sshconnection = new SSH2($ip,$port)) {
				log::add('Monitoring', 'error', 'connexion SSH KO pour '.$equipement);
				$cnx_ssh = 'KO';
			}
			else {
				if (!$sshconnection->login($user, $pass)) {
					log::add('Monitoring', 'error', 'Authentification SSH KO pour '.$equipement);
					$cnx_ssh = 'KO';
				}
				else {
					$cnx_ssh = 'OK';
					$ARMv_cmd = "lscpu 2>/dev/null | grep Architecture | awk '{ print $2 }'";
					$uptime_cmd = "uptime";

					if($this->getConfiguration('synology') == '1') {
						$namedistri_cmd = "cat /proc/sys/kernel/syno_hw_version 2>/dev/null";
						$VersionID_cmd = "awk -F'=' '/productversion/ {print $2}' /etc.defaults/VERSION | tr -d '\"'";
					}
					else {
						$namedistri_cmd = "cat /etc/*-release | grep PRETTY_NAME=";
						// $swap_pourc_cmd = "free | awk -F':' '/Swap|Partition d.échange/ { print $2 }' | awk '{ print $1,$2,$3}'";
						$VersionID_cmd = "awk -F'=' '/VERSION_ID/ {print $2}' /etc/os-release | tr -d '\"'";
						$bitdistri_cmd = "getconf LONG_BIT";
					}

					$memory_cmd = "free | grep 'Mem' | head -1 | awk '{ print $2,$3,$4,$7 }'";
					$swap_cmd = "free | awk -F':' '/Swap|Partition d.échange/ { print $2 }' | awk '{ print $1,$2,$3}'";
					
					$loadavg_cmd = "cat /proc/loadavg";
					
					$ReseauRXTX_cmd = "cat /proc/net/dev | grep ".$cartereseau." | awk '{print $2,$10}'";
					
					$perso_1cmd = $this->getConfiguration('perso1');
					$perso_2cmd = $this->getConfiguration('perso2');

					$ARMv = trim($sshconnection->exec($ARMv_cmd));

					$uptime = $sshconnection->exec($uptime_cmd);

					$VersionID = trim($sshconnection->exec($VersionID_cmd));
					
					$namedistri = $sshconnection->exec($namedistri_cmd);
					$bitdistri = $sshconnection->exec($bitdistri_cmd);

					$loadav = $sshconnection->exec($loadavg_cmd);

					$ReseauRXTX = $sshconnection->exec($ReseauRXTX_cmd);

					$memory = $sshconnection->exec($memory_cmd);
					$swap = $sshconnection->exec($swap_cmd);
					// $Swappourc = $sshconnection->exec($swap_pourc_cmd);

					$perso_1 = $sshconnection->exec($perso_1cmd);
					$perso_2 = $sshconnection->exec($perso_2cmd);

					if($this->getConfiguration('synology') == '1') {
						$platform_cmd = "get_key_value /etc/synoinfo.conf unique | cut -d'_' -f2";
						$synoplatorm = $sshconnection->exec($platform_cmd);

						$nbcpuARM_cmd = "cat /proc/sys/kernel/syno_CPU_info_core";
						$nbcpu = trim($sshconnection->exec($nbcpuARM_cmd));

						$cpufreq0ARM_cmd = "cat /proc/sys/kernel/syno_CPU_info_clock";
						$cpufreq0 = trim($sshconnection->exec($cpufreq0ARM_cmd));
						
						// $hdd_cmd = "df -h | grep 'vg1000\|volume1' | head -1 | awk '{ print $2,$3,$5 }' | cut -d '%' -f1";
						$hdd_cmd = "df -h | grep 'vg1000\|volume1' | head -1 | awk '{ print $2,$3,$5 }'";
						$hdd = $sshconnection->exec($hdd_cmd);

						$versionsyno_cmd = "cat /etc.defaults/VERSION | cut -d'=' -f2 | cut -d'=' -f2 | tr '\n' ' ' | awk '{ print $3,$4,$5,$7,$9}'";
						$versionsyno = $sshconnection->exec($versionsyno_cmd);

						$synotemp_cmd='$(find /sys/devices/* -name temp*_input | head -1)';
						if($this->getconfiguration('syno_use_temp_path')) $synotemp_cmd=$this->getconfiguration('syno_temp_path');				

						$cputemp0_cmd = "timeout 3 cat ".$synotemp_cmd;
						log::add("Monitoring","debug", "commande temp syno : ".$cputemp0_cmd);
						$cputemp0 = $sshconnection->exec($cputemp0_cmd);
					
						if($this->getConfiguration('synology') == '1' && $SynoV2Visible == 'OK' && $this->getConfiguration('synologyv2') == '1') {
							// $hddv2cmd = "df -h | grep 'vg1001\|volume2' | head -1 | awk '{ print $2,$3,$5 }' | cut -d '%' -f1"; // DSM 5.x & 6.x
							$hddv2cmd = "df -h | grep 'vg1001\|volume2' | head -1 | awk '{ print $2,$3,$5 }'"; // DSM 5.x & 6.x
							$hddv2 = $sshconnection->exec($hddv2cmd);
						}
					}	
					elseif ($ARMv == 'armv6l') {
						$nbcpuARM_cmd = "lscpu | grep 'CPU(s):' | awk '{ print $2 }'";
						$nbcpu = trim($sshconnection->exec($nbcpuARM_cmd));
						
						$uname = '.';

						$hdd_cmd = "df -h | grep '/$' | head -1 | awk '{ print $2,$3,$5 }'";
						$hdd = $sshconnection->exec($hdd_cmd);

						$cpufreq0ARM_cmd = "cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq 2>/dev/null";
						$cpufreq0 = $sshconnection->exec($cpufreq0ARM_cmd);

						$cputemp_cmd = $this->getCmd(null,'cpu_temp');
						if (is_object($cputemp_cmd) && $cputemp_cmd->getIsVisible() == 1) {
							$cputemp0armv6l_cmd = "cat /sys/class/thermal/thermal_zone0/temp 2>/dev/null";
							$cputemp0 = $sshconnection->exec($cputemp0armv6l_cmd);
						}

					}
					elseif ($ARMv == 'armv7l' || $ARMv == 'aarch64' || $ARMv == 'mips64'){
						$nbcpuARM_cmd = "lscpu | grep '^CPU(s):' | awk '{ print $2 }'";
						$nbcpu = trim($sshconnection->exec($nbcpuARM_cmd));
						
						$uname = '.';

						$cpufreq0ARM_cmd = "cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq 2>/dev/null";
						$cpufreq0 = trim($sshconnection->exec($cpufreq0ARM_cmd));

						$hdd_cmd = "df -h | grep '/$' | head -1 | awk '{ print $2,$3,$5 }'";
						$hdd = $sshconnection->exec($hdd_cmd);

						$cputemp_cmd = $this->getCmd(null,'cpu_temp');
						if (is_object($cputemp_cmd) && $cputemp_cmd->getIsVisible() == 1) {
							$cputemp0RPi2_cmd = "cat /sys/class/thermal/thermal_zone0/temp 2>/dev/null";	// OK RPi2
							$cputemp0 = $sshconnection->exec($cputemp0RPi2_cmd);

							if ($cputemp0 == '') {
								$cputemp0armv7l_cmd = "cat /sys/devices/platform/sunxi-i2c.0/i2c-0/0-0034/temp1_input 2>/dev/null"; // OK Banana Pi (Cubie surement un jour...)
								$cputemp0 = $sshconnection->exec($cputemp0armv7l_cmd);
							}
						}

					}
					elseif ($ARMv == 'i686' || $ARMv == 'x86_64' || $ARMv == 'i386'){
						$NF = '';
						$cputemp0 ='';
						$uname = '.';
						
						$nbcpuVM_cmd = "lscpu | grep 'Processeur(s)' | awk '{ print $NF }'"; // OK pour Debian
						$nbcpu = $sshconnection->exec($nbcpuVM_cmd);

						if ($nbcpu == '') {
							$nbcpuVMbis_cmd = "lscpu | grep '^CPU(s):' | awk '{ print $2 }'"; // OK pour LXC Linux/Ubuntu
							$nbcpu = $sshconnection->exec($nbcpuVMbis_cmd);
						}
						$nbcpu = preg_replace("/[^0-9]/","",$nbcpu);

						$hdd_cmd = "df -h | grep '/$' | head -1 | awk '{ print $2,$3,$5 }'";
						$hdd = $sshconnection->exec($hdd_cmd);

						$cpufreqVM_cmd = "lscpu | grep 'Vitesse du processeur en MHz' | awk '{print $NF}'"; // OK pour Debian/Ubuntu, mais pas Ubuntu 22.04
						$cpufreq = $sshconnection->exec($cpufreqVM_cmd);

						if ($cpufreq == '') {
							$cpufreqVMbis_cmd = "lscpu | grep '^CPU MHz' | awk '{ print $NF }'";	// OK pour LXC Linux
							$cpufreq = $sshconnection->exec($cpufreqVMbis_cmd);
						}
						if ($cpufreq == '') {
							$cpufreqVMbis_cmd = "cat /proc/cpuinfo | grep '^cpu MHz' | head -1 | cut -d':' -f2 | awk '{ print $NF }'";	// OK pour Debian 10/11, Ubuntu 22.04
							$cpufreq = $sshconnection->exec($cpufreqVMbis_cmd);
						}
						$cpufreq=preg_replace("/[^0-9.]/","",$cpufreq);

						$cputemp_cmd = $this->getCmd(null,'cpu_temp');
						if (is_object($cputemp_cmd) && $cputemp_cmd->getIsVisible() == 1) {
							$cputemp0_cmd = "cat /sys/devices/virtual/thermal/thermal_zone0/temp 2>/dev/null";	// OK Dell WYSE
							$cputemp0 = $sshconnection->exec($cputemp0_cmd);

							if ($cputemp0 == '') {
								$cputemp0_cmd = "cat /sys/devices/platform/coretemp.0/hwmon/hwmon0/temp?_input 2>/dev/null";	// OK AOpen DE2700
								$cputemp0 = $sshconnection->exec($cputemp0_cmd);
								
							}
							if ($cputemp0 == '') {
								$cputemp0AMD_cmd = "cat /sys/devices/pci0000:00/0000:00:18.3/hwmon/hwmon0/temp1_input 2>/dev/null";	// OK AMD Ryzen
								$cputemp0 = $sshconnection->exec($cputemp0AMD_cmd);
							}
							if ($cputemp0 == '') {
								$cputemp0sensors_cmd = "sensors 2>/dev/null | awk '{if (match($0, \"MB Temperature\")){printf(\"%f\",$3);} }'"; // OK by sensors
								$cputemp0 = $sshconnection->exec($cputemp0sensors_cmd);
							}
						}

					}
					elseif ($ARMv == '' & $this->getConfiguration('synology') != '1') {
						$unamecmd = "uname -a | awk '{print $2,$1}'";
						$unamedata = $sshconnection->exec($unamecmd);
						$uname = $unamedata;

						if (preg_match("#RasPlex|OpenELEC|LibreELEC#", $namedistri)) {
							$bitdistri = '32';
							$ARMv = 'arm';

							$nbcpuARM_cmd = "grep 'model name' /proc/cpuinfo | wc -l";
							$nbcpu = trim($sshconnection->exec($nbcpuARM_cmd));

							$hdd_cmd = "df -h | grep '/dev/mmcblk0p2' | head -1 | awk '{ print $2,$3,$5 }'";
							$hdd = $sshconnection->exec($hdd_cmd);

							$cpufreq0ARM_cmd = "cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq 2>/dev/null";
							$cpufreq0 = $sshconnection->exec($cpufreq0ARM_cmd);

							$cputemp_cmd = $this->getCmd(null,'cpu_temp');
							if (is_object($cputemp_cmd) && $cputemp_cmd->getIsVisible() == 1) {
								$cputemp0armv6l_cmd = "cat /sys/class/thermal/thermal_zone0/temp 2>/dev/null";
								$cputemp0 = $sshconnection->exec($cputemp0armv6l_cmd);
							}

						}
						elseif (preg_match("#osmc#", $namedistri)) {
							$bitdistri = '32';
							$ARMv = 'arm';

							$nbcpuARM_cmd = "grep 'model name' /proc/cpuinfo | wc -l";
							$nbcpu = trim($sshconnection->exec($nbcpuARM_cmd));

							$hdd_cmd = "df -h | grep '/$' | head -1 | awk '{ print $2,$3,$5 }'";
							$hdd = $sshconnection->exec($hdd_cmd);

							$cpufreq0ARM_cmd = "cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq 2>/dev/null";
							$cpufreq0 = $sshconnection->exec($cpufreq0ARM_cmd);

							$cputemp_cmd = $this->getCmd(null,'cpu_temp');
							if (is_object($cputemp_cmd) && $cputemp_cmd->getIsVisible() == 1) {
								$cputemp0armv6l_cmd = "cat /sys/class/thermal/thermal_zone0/temp 2>/dev/null";
								$cputemp0 = $sshconnection->exec($cputemp0armv6l_cmd);
							}
						}
						elseif (preg_match("#piCorePlayer#", $uname)) {
							$bitdistri = '32';
							$ARMv = 'arm';
							$namedistri_cmd = "uname -a | awk '{print $2,$3}'";
							$namedistri = $sshconnection->exec($namedistri_cmd);

							$nbcpuARM_cmd = "grep 'model name' /proc/cpuinfo | wc -l";
							$nbcpu = trim($sshconnection->exec($nbcpuARM_cmd));

							$hdd_cmd = "df -h | grep /dev/mmcblk0p | head -1 | awk '{print $2,$3,$5 }'";
							$hdd = $sshconnection->exec($hdd_cmd);

							$cpufreq0ARM_cmd = "cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq 2>/dev/null";
							$cpufreq0 = $sshconnection->exec($cpufreq0ARM_cmd);

							$cputemp_cmd = $this->getCmd(null,'cpu_temp');
							if (is_object($cputemp_cmd) && $cputemp_cmd->getIsVisible() == 1) {
								$cputemp0armv6l_cmd = "cat /sys/class/thermal/thermal_zone0/temp 2>/dev/null";
								$cputemp0 = $sshconnection->exec($cputemp0armv6l_cmd);
							}

						}
						elseif (preg_match("#FreeBSD#", $uname)) {
							$namedistri_cmd = "uname -a | awk '{ print $1,$3}'";
							$namedistri = $sshconnection->exec($namedistri_cmd);

							$ARMv_cmd = "sysctl hw.machine | awk '{ print $2}'";
							$ARMv = trim($sshconnection->exec($ARMv_cmd));

							$loadavg_cmd = "uptime | awk '{print $8,$9,$10}'";
							$loadav = $sshconnection->exec($loadavg_cmd);

							$memory_cmd = "dmesg | grep Mem | tr '\n' ' ' | awk '{print $4,$10}'";
							$memory = $sshconnection->exec($memory_cmd);

							$bitdistri_cmd = "sysctl kern.smp.maxcpus | awk '{ print $2}'";
							$bitdistri = $sshconnection->exec($bitdistri_cmd);

							$nbcpuARM_cmd = "sysctl hw.ncpu | awk '{ print $2}'";
							$nbcpu = trim($sshconnection->exec($nbcpuARM_cmd));

							$hdd_cmd = "df -h | grep '/$' | head -1 | awk '{ print $2,$3,$5 }'";
							$hdd = $sshconnection->exec($hdd_cmd);

							$cpufreq0ARM_cmd = "sysctl -a | egrep -E 'cpu.0.freq' | awk '{ print $2}'";
							$cpufreq0 = $sshconnection->exec($cpufreq0ARM_cmd);

							$cputemp_cmd = $this->getCmd(null,'cpu_temp');
							if (is_object($cputemp_cmd) && $cputemp_cmd->getIsVisible() == 1) {
								$cputemp0armv6l_cmd = "sysctl -a | egrep -E 'cpu.0.temp' | awk '{ print $2}'";
								$cputemp0 = $sshconnection->exec($cputemp0armv6l_cmd);
							}
						}
					}
				}
			}
		}
		elseif($this->getConfiguration('maitreesclave') == 'local' && $this->getIsEnable()) {
			$cnx_ssh = 'No';
			$uptime_cmd = "uptime";
			

			if($this->getConfiguration('synology') == '1') {
				$namedistri_cmd = "cat /proc/sys/kernel/syno_hw_version 2>/dev/null";
				// $memory_cmd = "cat /proc/meminfo | cut -d':' -f2 | awk '{ print $1}' | tr '\n' ' ' | awk '{ print $1,$2,$3,$4}'";
				// $swap_cmd = "free | grep 'Swap' | head -1 | awk '{ print $2,$3,$4 }'";
				// $hdd_cmd = "df -h | grep 'vg1000\|volume1' | head -1 | awk '{ print $2,$3,$5 }' | cut -d '%' -f1";
				$hdd_cmd = "df -h | grep 'vg1000\|volume1' | head -1 | awk '{ print $2,$3,$5 }'";
				$VersionID_cmd = "awk -F'=' '/productversion/ {print $2}' /etc.defaults/VERSION | tr -d '\"'";
			}
			else {
				$ARMv_cmd = "lscpu | grep Architecture | awk '{ print $2 }'";
				$namedistri_cmd = "cat /etc/*-release | grep PRETTY_NAME=";
				$VersionID_cmd = "awk -F'=' '/VERSION_ID/ {print $2}' /etc/os-release | tr -d '\"'";
				
				// $memory_cmd = "free | grep 'Mem' | head -1 | awk '{ print $2,$3,$4,$7 }'";
				// $swap_cmd = "free -h | awk -F':' '/Swap|Partition d.échange/ { print $2 }' | awk '{ print $1,$2,$3}'";
				// $swap_pourc_cmd = "free | awk -F':' '/Swap|Partition d.échange/ { print $2 }' | awk '{ print $1,$2,$3}'";
				
				$hdd_cmd = "df -h | grep '/$' | head -1 | awk '{ print $2,$3,$5 }'";
				$bitdistri_cmd = "getconf LONG_BIT";
				
				$ARMv = exec($ARMv_cmd);
				$bitdistri = exec($bitdistri_cmd);
			}

			$memory_cmd = "free | grep 'Mem' | head -1 | awk '{ print $2,$3,$4,$7 }'";
			$swap_cmd = "free | awk -F':' '/Swap|Partition d.échange/ { print $2 }' | awk '{ print $1,$2,$3}'";

			$loadavg_cmd = "cat /proc/loadavg";

			$ReseauRXTX_cmd = "cat /proc/net/dev | grep ".$cartereseau." | awk '{print $2,$10}'";

			$perso_1cmd = $this->getConfiguration('perso1');
			$perso_2cmd = $this->getConfiguration('perso2');

			$uptime = exec($uptime_cmd);

			$namedistri = exec($namedistri_cmd);
			$VersionID = trim(exec($VersionID_cmd));
			$loadav = exec($loadavg_cmd);
			$ReseauRXTX = exec($ReseauRXTX_cmd);
			$hdd = exec($hdd_cmd);
			$memory = exec($memory_cmd);
			$swap = exec($swap_cmd);
			// $Swappourc = exec($swap_pourc_cmd);
			
			if ($perso_1cmd != '') {
				$perso_1 = exec ($perso_1cmd);
			}
			if ($perso_2cmd != '') {
				$perso_2 = exec ($perso_2cmd);
			}

			if($this->getConfiguration('synology') == '1'){
				$uname = '.';
				$nbcpuARM_cmd = "cat /proc/sys/kernel/syno_CPU_info_core";
				$cpufreq0ARM_cmd = "cat /proc/sys/kernel/syno_CPU_info_clock";
				$versionsyno_cmd = "cat /etc.defaults/VERSION | cut -d'=' -f2 | cut -d'=' -f2 | tr '\n' ' ' | awk '{ print $3,$4,$5,$7,$9}'";
				
				$nbcpu = exec($nbcpuARM_cmd);
				$cpufreq0 = exec($cpufreq0ARM_cmd);
				$versionsyno = exec($versionsyno_cmd);

				if($this->getConfiguration('synology') == '1' && $SynoV2Visible == 'OK' && $this->getConfiguration('synologyv2') == '1') {
					$hddv2cmd = "df -h | grep 'vg1001' | head -1 | awk '{ print $2,$3,$5 }' | cut -d '%' -f1";
					$hddv2 = exec($hddv2cmd);
				}
			}
			elseif ($ARMv == 'armv6l') {
				$uname = '.';

				$nbcpuARM_cmd = "lscpu | grep 'CPU(s):' | awk '{ print $2 }'";
				$nbcpu = exec($nbcpuARM_cmd);
				
				if (file_exists('/sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq')) {
					$cpufreq0ARM_cmd = "cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq";
					$cpufreq0 = exec($cpufreq0ARM_cmd);
				}
				$cputemp_cmd = $this->getCmd(null,'cpu_temp');
				if (is_object($cputemp_cmd) && $cputemp_cmd->getIsVisible() == 1) {
					if (file_exists('/sys/class/thermal/thermal_zone0/temp')) {
						$cputemp0armv6l_cmd = "cat /sys/class/thermal/thermal_zone0/temp";
						$cputemp0 = exec($cputemp0armv6l_cmd);
					}
				}
			}
			elseif ($ARMv == 'armv7l' || $ARMv == 'aarch64') {
				$uname = '.';
				$cputemp0 = '';
				$cpufreq0 = '';

				$nbcpuARM_cmd = "lscpu | grep '^CPU(s):' | awk '{ print $2 }'";
				$nbcpu = exec($nbcpuARM_cmd);
				
				if (file_exists('/sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq')) {
					$cpufreq0ARM_cmd = "cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq";
					$cpufreq0 = exec($cpufreq0ARM_cmd);
				}
				
				$cputemp_cmd = $this->getCmd(null,'cpu_temp');
				if (is_object($cputemp_cmd) && $cputemp_cmd->getIsVisible() == 1) {
					if (file_exists('/sys/class/thermal/thermal_zone0/temp')) {
						$cputemp0RPi2_cmd = "cat /sys/class/thermal/thermal_zone0/temp";	// OK RPi2/3, Odroid
						$cputemp0 = exec($cputemp0RPi2_cmd);
					}
					if ($cputemp0 == '') {
						if (file_exists('/sys/devices/platform/sunxi-i2c.0/i2c-0/0-0034/temp1')) {
							$cputemp0armv7l_cmd = "cat /sys/devices/platform/sunxi-i2c.0/i2c-0/0-0034/temp1"; // OK Banana Pi (Cubie surement un jour...)
							$cputemp0 = exec($cputemp0armv7l_cmd);
						}
					}
				}
			}
			elseif ($ARMv == 'i686' || $ARMv == 'x86_64' || $ARMv == 'i386') {
				$NF = '';
				$uname = '.';
				$cputemp0 = '';
				$cpufreq = '';

				$nbcpuVM_cmd = "lscpu | grep 'Processeur(s)' | awk '{ print $NF }'"; // OK pour Debian
				$nbcpu = exec($nbcpuVM_cmd);

				if ($nbcpu == ''){
					$nbcpuVMbis_cmd = "lscpu | grep '^CPU(s):' | awk '{ print $NF }'"; // OK pour LXC Linux/Ubuntu
					$nbcpu = exec($nbcpuVMbis_cmd);
				}
				$nbcpu = preg_replace("/[^0-9]/","",$nbcpu);
				
				$cpufreqVM_cmd = "lscpu | grep 'Vitesse du processeur en MHz' | awk '{print $NF}'"; // OK pour Debian/Ubuntu, mais pas Ubuntu 22.04
				$cpufreq = exec($cpufreqVM_cmd);
				
				if ($cpufreq == ''){
					$cpufreqVMbis_cmd = "lscpu | grep '^CPU MHz' | awk '{ print $NF }'";	// OK pour LXC Linux
					$cpufreq = exec($cpufreqVMbis_cmd);
				}
				if ($cpufreq == ''){
					$cpufreqVMbis_cmd = "cat /proc/cpuinfo | grep '^cpu MHz' | head -1 | cut -d':' -f2 | awk '{ print $NF }'";	// OK pour Debian 10/11, Ubuntu 22.04
					$cpufreq = exec($cpufreqVMbis_cmd);
				}
				$cpufreq = preg_replace("/[^0-9.]/","",$cpufreq);
				
				$cputemp_cmd = $this->getCmd(null,'cpu_temp');
				if (is_object($cputemp_cmd) && $cputemp_cmd->getIsVisible() == 1) {
					if (file_exists('/sys/devices/virtual/thermal/thermal_zone0/temp')) {
						$cputemp0RPi2_cmd = "cat /sys/devices/virtual/thermal/thermal_zone0/temp"; // OK Dell Whyse
						$cputemp0 = exec($cputemp0RPi2_cmd);
					}
				
					if ($cputemp0 == '') {
						if (file_exists('/sys/devices/platform/coretemp.0/hwmon/hwmon0/temp?_input')) {
							$cputemp0AOpen_cmd = "cat /sys/devices/platform/coretemp.0/hwmon/hwmon0/temp?_input";	// OK AOpen DE2700
							$cputemp0 = exec($cputemp0AOpen_cmd);
						}
					}
				
					if ($cputemp0 == '') {
						if (file_exists('/sys/devices/pci0000:00/0000:00:18.3/hwmon/hwmon0/temp1_input')) {
							$cputemp0AMD_cmd = "cat /sys/devices/pci0000:00/0000:00:18.3/hwmon/hwmon0/temp1_input";	// OK AMD Ryzen
							$cputemp0 = exec($cputemp0AMD_cmd);
						}
					}
				}
			}
		}
		if (isset($cnx_ssh)) {
			if($this->getConfiguration('maitreesclave') == 'local' || $cnx_ssh == 'OK') {
				if($this->getConfiguration('synology') == '1'){
					if (isset($versionsyno)) {
						$versionsyno = str_ireplace('"', '', $versionsyno);
						$versionsyno = explode(' ', $versionsyno);
						if (isset($versionsyno[0]) && isset($versionsyno[1]) && isset($versionsyno[2]) && isset($versionsyno[3]) && isset($versionsyno[4])) {
							$versionsyno = 'DSM '.$versionsyno[0].'.'.$versionsyno[1].'.'.$versionsyno[2].'-'.$versionsyno[3].' Update '.$versionsyno[4];
						}
						if (isset($namedistri) && isset($versionsyno)) {
							$namedistri = trim($namedistri);
							$namedistri = $versionsyno.' ('.$namedistri.')';
						}
					}
				}
				else {
					if (isset($namedistri)) {
						$namedistrifin = str_ireplace('PRETTY_NAME="', '', $namedistri);
						$namedistrifin = str_ireplace('"', '', $namedistrifin);
						if (isset($namedistri) && isset($namedistrifin) && isset($bitdistri) && isset($ARMv)) {
							$namedistri = $namedistrifin.' '.$bitdistri.'bits ('.$ARMv.')';
						}
					}
				}
				
				if($SynoV2Visible == 'OK' && $this->getConfiguration('synology') == '1' && $this->getConfiguration('synologyv2') == '1'){
					if (isset($hddv2)) {
						$hdddatav2 = explode(' ', $hddv2);
						if (isset($hdddatav2[0]) && isset($hdddatav2[1]) && isset($hdddatav2[2])) {
							$hddtotalv2 = $hdddatav2[0];
							$hddusedv2 = $hdddatav2[1];
							$hddusedv2_pourc = preg_replace("/[^0-9.]/","",$hdddatav2[2]);
						}
					}
				}

				if (isset($uptime)) {
					$datauptime = explode(' up ', $uptime);
					if (isset($datauptime[0]) && isset($datauptime[1])) {
						$datauptime = explode(', ', $datauptime[1]);
						$datauptime = str_replace("days", "jour(s)", $datauptime);
						$datauptime = str_replace("day", "jour(s)", $datauptime);
						$datauptime = str_replace(":", "h", $datauptime);
						if (strpos($datauptime[0], 'jour(s)') === false){
							$uptime = $datauptime[0];
						}
						else {
							if (isset($datauptime[0]) && isset($datauptime[1])) {
								$uptime = $datauptime[0].' et '.$datauptime[1];
							}
						}
					}
				}

				if (isset($loadav)) {
					$loadavg = explode(" ", $loadav);
					if (isset($loadavg[0]) && isset($loadavg[1]) && isset($loadavg[2])) {
						$loadavg1mn = $loadavg[0];
						$loadavg5mn = $loadavg[1];
						$loadavg15mn = $loadavg[2];
					}
				}

				if (isset($memory)) {
					if (!preg_match("#FreeBSD#", $uname)) {
						$memory = explode(' ', $memory);
						if($this->getConfiguration('synology') == '1'){
							if (isset($memory[3])) {
								$memorylibre = intval($memory[3]);
								log::add('Monitoring', 'debug', '[Memory] Version Syno ('.$VersionID.') / Mémoire Libre = '.$memorylibre);
							}
						}
						else {
							if (isset($memory[3])) {
								$memorylibre = intval($memory[3]);
								log::add('Monitoring', 'debug', '[Memory] Version Linux ('.$VersionID.') / Mémoire Libre = '.$memorylibre);
							}
						}
						/* elseif(intval($VersionID) >= 9 && isset($memory[3])){
							$memorylibre = intval($memory[3]);
							log::add('Monitoring', 'debug', '[Memory] VersionID ('.$VersionID.') >= 9 et Free3 ok : '.$memorylibre.' / '.$memory[3]);
						}
						elseif(intval($VersionID) < 9 && isset($memory[2]) && isset($memory[3])){
							$memorylibre = intval($memory[2]) + intval($memory[3]);
							log::add('Monitoring', 'debug', 'Version ('.$VersionID.') < 9 et Free2 et Free3 ok : '.$memorylibre.' / '.$memory[2].' / '.$memory[3]);
						} */
						
						if (isset($memory[0]) && isset($memorylibre)) {
							if (intval($memory[0]) != 0) {
								$memorylibre_pourc = round(intval($memorylibre) / intval($memory[0]) * 100);
								log::add('Monitoring', 'debug', '[Memory] Memorylibre% = '.$memorylibre_pourc);
							}
							else {
								$memorylibre_pourc = 0;
							}
						}

						if (isset($memorylibre)) {
							if ((intval($memorylibre) / 1024) > 1024) {
								$memorylibre = round(intval($memorylibre) / 1048576, 2) . " Go";
							}
							else {
								$memorylibre = round(intval($memorylibre) / 1024) . " Mo";
							}
						}
						if (isset($memory[0])) {
							if ((intval($memory[0]) / 1024) > 1024) {
								$memtotal = round(intval($memory[0]) / 1048576, 2) . " Go";
							}
							else {
								$memtotal = round(intval($memory[0]) / 1024, 2) . " Mo";
							}
						}
						if (isset($memtotal) && isset($memorylibre)) {
							$Mem = 'Total : '.$memtotal.' - Libre : '.$memorylibre;
						}
					}
					elseif (preg_match("#FreeBSD#", $uname)) {
						$memory = explode(' ', $memory);
						if (isset($memory[0]) && isset($memory[1])) {
							if (intval($memory[0]) != 0) {
								$memorylibre_pourc = round(intval($memory[1]) / intval($memory[0]) * 100);
							}
							else {
								$memorylibre_pourc = 0;
							}
						}
						if ((intval($memory[1]) / 1024) > 1024) {
							$memorylibre = round(intval($memory[1]) / 1048576, 2) . " Go";
						}
						else{
							$memorylibre = round(intval($memory[1]) / 1024) . " Mo";
						}
						if (($memory[0] / 1024) > 1024) {
							$memtotal = round(intval($memory[0]) / 1048576, 2) . " Go";
						}
						else{
							$memtotal = round(intval($memory[0]) / 1024) . " Mo";
						}
						$Mem = 'Total : '.$memtotal.' - Libre : '.$memorylibre;
					}
				}
				else {
					$Mem = '';
				}

				if (isset($swap)) {
					$swap = explode(' ', $swap);

					if(isset($swap[0]) && isset($swap[2])) {
						if (intval($swap[0]) != 0) {
							$swaplibre_pourc = round(intval($swap[2]) / intval($swap[0]) * 100);
						}
						else {
							$swaplibre_pourc = 0;
						}
					}

					if(isset($swap[0])){
						if ((intval($swap[0]) / 1024) > 1024) {
							$swap[0] = round(intval($swap[0]) / 1048576, 2) . " Go";
						}
						else {
							$swap[0] = round(intval($swap[0]) / 1024, 2) . " Mo";
						}
					}
					if(isset($swap[1])) {
						if ((intval($swap[1]) / 1024) > 1024) {
							$swap[1] = round(intval($swap[1]) / 1048576, 2) . " Go";
						}
						else {
							$swap[1] = round(intval($swap[1]) / 1024, 2) . " Mo";
						}
					}
					if(isset($swap[2])){
						if ((intval($swap[2]) / 1024) > 1024) {
							$swap[2] = round(intval($swap[2]) / 1048576, 2) . " Go";
						}
						else {
							$swap[2] = round(intval($swap[2]) / 1024, 2) . " Mo";
						}
					}

					if(isset($swap[0]) && isset($swap[1]) && isset($swap[2])){
						$swap[0] = str_replace("B"," o", $swap[0]);
						$swap[1] = str_replace("B"," o", $swap[1]);
						$swap[2] = str_replace("B"," o", $swap[2]);
						$Memswap = 'Total : '.$swap[0].' - Utilisé : '.$swap[1].' - Libre : '.$swap[2];
					}

					/* $Swappourc = explode(' ', $Swappourc);
					if (isset($Swappourc[0]) && isset($Swappourc[1]))
					{
						log::add('Monitoring', 'debug', 'Variable Swappourc[0] = '.$Swappourc[0].' / Swappourc[1] = '.$Swappourc[1]);
						if (intval($Swappourc[0]) != 0){
							$swaplibre_pourc = round(intval($Swappourc[1]) / intval($Swappourc[0]) * 100, 2);
						if (intval($memory[0]) != 0) {
							$memorylibre_pourc = round(intval($memorylibre) / intval($memory[0]) * 100);
							log::add('Monitoring', 'debug', '[Memory] Memorylibre% = '.$memorylibre_pourc);
						}
						else {
							$swaplibre_pourc = 0;
							$memorylibre_pourc = 0;
						}
					} */
				} 
				else {
					$Memswap = '';
				}

				if (isset($ReseauRXTX)) {
					$ReseauRXTX = explode(' ', $ReseauRXTX);
					if(isset($ReseauRXTX[0]) && isset($ReseauRXTX[1])){
						if ((intval($ReseauRXTX[1]) / 1024) > 1048576) {
							$ReseauTX = round(intval($ReseauRXTX[1]) / 1262485504, 2) . " Go";
						}
						elseif ((intval($ReseauRXTX[1]) / 1024) > 1024) {
							$ReseauTX = round(intval($ReseauRXTX[1]) / 1048576, 2) . " Mo";
						}
						else {
							$ReseauTX = round(intval($ReseauRXTX[1]) / 1024) . " Ko";
						}
						
						if ((intval($ReseauRXTX[0]) / 1024) > 1048576) {
							$ReseauRX = round(intval($ReseauRXTX[0]) / 1262485504, 2) . " Go";
						}
						elseif ((intval($ReseauRXTX[0]) / 1024) > 1024) {
							$ReseauRX = round(intval($ReseauRXTX[0]) / 1048576, 2) . " Mo";
						}
						else {
							$ReseauRX = round(intval($ReseauRXTX[0]) / 1024) . " Ko";
						}
						$ethernet0 = 'TX : '.$ReseauTX.' - RX : '.$ReseauRX;
					}
				}

				$hddtotal = '';
				$hddused = '';
				$hddused_pourc = '';
				if (isset($hdd)) {
					$hdddata = explode(' ', $hdd);
					if(isset($hdddata[0]) && isset($hdddata[1]) && isset($hdddata[2])){
						$hddtotal = str_replace(array("K","M","G","T"),array(" Ko"," Mo"," Go"," To"), $hdddata[0]);
						$hddused = str_replace(array("K","M","G","T"),array(" Ko"," Mo"," Go"," To"), $hdddata[1]);
						$hddused_pourc = preg_replace("/[^0-9.]/","",$hdddata[2]);
						$hddused_pourc = trim($hddused_pourc);
						if ($hddused_pourc < '10') {
							$hddused_pourc = '0'.$hddused_pourc; // A quoi sert certe conversion ?
						}
					}
				}

				if (isset($ARMv)) {
					if ($ARMv == 'i686' || $ARMv == 'x86_64' || $ARMv == 'i386'){
						if ((floatval($cpufreq) / 1024) > 1) {
							$cpufreq = round(floatval($cpufreq) / 1000, 1, PHP_ROUND_HALF_UP) . " GHz";
						}
						else {
							$cpufreq = $cpufreq . " MHz";
						}
						
						$cputemp_cmd = $this->getCmd(null,'cpu_temp');
						if (is_object($cputemp_cmd) && $cputemp_cmd->getIsVisible() == 1) {
							if (floatval($cputemp0) > 200){
								$cputemp0 = floatval($cputemp0) / 1000;
								$cputemp0 = round(floatval($cputemp0), 1);
							}
						}
						$cpu = $nbcpu.' - '.$cpufreq;
					}
					elseif ($ARMv == 'armv6l' || $ARMv == 'armv7l' || $ARMv == 'aarch64' || $ARMv == 'mips64'){
						if ((floatval($cpufreq0) / 1000) > 1000) {
							$cpufreq0 = round(floatval($cpufreq0) / 1000000, 1, PHP_ROUND_HALF_UP) . " GHz";
						}
						else {
							$cpufreq0 = round(floatval($cpufreq0) / 1000) . " MHz";
						}
						
						$cputemp_cmd = $this->getCmd(null,'cpu_temp');
						if (is_object($cputemp_cmd) && $cputemp_cmd->getIsVisible() == 1) {
							if (floatval($cputemp0) > 200){
								$cputemp0 = floatval($cputemp0) / 1000;
								$cputemp0 = round(floatval($cputemp0), 1);
							}
						}
						if (floatval($cpufreq0) == 0) {
							$cpu = $nbcpu.' Socket(s) ';
							$cpufreq0 = '';
						}
						else {
							$cpu = $nbcpu.' - '.$cpufreq0;
						}
					}
					elseif ($ARMv == 'arm') {
						if (preg_match("#RasPlex|OpenELEC|osmc|LibreELEC#", $namedistri) || preg_match("#piCorePlayer#", $uname)) {
							if ((floatval($cpufreq0) / 1000) > 1000) {
								$cpufreq0 = round(floatval($cpufreq0) / 1000000, 1, PHP_ROUND_HALF_UP) . " GHz";
							}
							else {
								$cpufreq0 = round(floatval($cpufreq0) / 1000) . " MHz";
							}
							$cputemp_cmd = $this->getCmd(null,'cpu_temp');
							if (is_object($cputemp_cmd) && $cputemp_cmd->getIsVisible() == 1) {
								if (floatval($cputemp0) > 200){
									$cputemp0 = floatval($cputemp0) / 1000;
									$cputemp0 = round(floatval($cputemp0), 1);
								}
							}
							$cpu = $nbcpu.' - '.$cpufreq0;
						}
					}
				}

				if($this->getConfiguration('synology') == '1'){
					if ((floatval($cpufreq0) / 1000) > 1) {
						$cpufreq0 = round(floatval($cpufreq0) / 1000, 1, PHP_ROUND_HALF_UP) . " GHz";
					}
					else{
						$cpufreq0 = $cpufreq0 . " MHz";
					}
					if (floatval($cputemp0) > 200){
						$cputemp0 = floatval($cputemp0) / 1000;
						$cputemp0 = round(floatval($cputemp0), 1);
					}
					$cpu = $nbcpu.' - '.$cpufreq0;
				}
				if (empty($cputemp0)) {$cputemp0 = '';}
				if (empty($perso_1)) {$perso_1 = '';}
				if (empty($perso_2)) {$perso_2 = '';}
				if (empty($cnx_ssh)) {$cnx_ssh = '';}
				// if (empty($swap_pourc_cmd)) {$swap_pourc_cmd = '';}
				if (empty($uname)) {$uname = 'Inconnu';}
				if (empty($Mem)) {$Mem = '';}
				if (empty($memorylibre_pourc)) {$memorylibre_pourc = '';}
				if (empty($Memswap)) {$Memswap = '';}
				if (empty($swaplibre_pourc)) {$swaplibre_pourc = '';}

				$dataresult = array(
					'namedistri' => $namedistri,
					'uptime' => $uptime,
					'loadavg1mn' => $loadavg1mn,
					'loadavg5mn' => $loadavg5mn,
					'loadavg15mn' => $loadavg15mn,
					'Mem' => $Mem,
					'ethernet0' => $ethernet0,
					'hddtotal' => $hddtotal,
					'hddused' => $hddused,
					'hddpourcused' => $hddused_pourc,
					'cpu' => $cpu,
					'cpu_temp' => $cputemp0,
					'cnx_ssh' => $cnx_ssh,
					'Mem_swap' => $Memswap,
					'Mempourc' => $memorylibre_pourc,
					'Swappourc' => $swaplibre_pourc,
					'perso1' => $perso_1,
					'perso2' => $perso_2,
				);
				if($this->getConfiguration('synology') == '1' && $SynoV2Visible == 'OK' && $this->getConfiguration('synologyv2') == '1'){
					$dataresultv2 = array(
						'hddtotalv2' => $hddtotalv2,
						'hddusedv2' => $hddusedv2,
						'hddpourcusedv2' => $hddusedv2_pourc,
					);
				}

				$namedistri = $this->getCmd(null,'namedistri');
				if(is_object($namedistri)){
					$namedistri->event($dataresult['namedistri']);
				}

				$uptime = $this->getCmd(null,'uptime');
				if(is_object($uptime)){
					$uptime->event($dataresult['uptime']);
				}

				$loadavg1mn = $this->getCmd(null,'loadavg1mn');
				if(is_object($loadavg1mn)){
					$loadavg1mn->event($dataresult['loadavg1mn']);
				}

				$loadavg5mn = $this->getCmd(null,'loadavg5mn');
				if(is_object($loadavg5mn)){
					$loadavg5mn->event($dataresult['loadavg5mn']);
				}

				$loadavg15mn = $this->getCmd(null,'loadavg15mn');
				if(is_object($loadavg15mn)){
					$loadavg15mn->event($dataresult['loadavg15mn']);
				}

				$Mem = $this->getCmd(null,'Mem');
				if(is_object($Mem)){
					$Mem->event($dataresult['Mem']);
				}

				$Mem_swap = $this->getCmd(null,'Mem_swap');
				if(is_object($Mem_swap)){
					$Mem_swap->event($dataresult['Mem_swap']);
				}

				$ethernet0 = $this->getCmd(null,'ethernet0');
				if(is_object($ethernet0)){
					$ethernet0->event($dataresult['ethernet0']);
				}

				$hddtotal = $this->getCmd(null,'hddtotal');
				if(is_object($hddtotal)){
					$hddtotal->event($dataresult['hddtotal']);
				}

				$hddused = $this->getCmd(null,'hddused');
				if(is_object($hddused)){
					$hddused->event($dataresult['hddused']);
				}

				$hddused_pourc = $this->getCmd(null,'hddpourcused');
				if(is_object($hddused_pourc)){
					$hddused_pourc->event($dataresult['hddpourcused']);
				}

				if($this->getConfiguration('synology') == '1' && $SynoV2Visible == 'OK' && $this->getConfiguration('synologyv2') == '1'){
					$hddtotalv2 = $this->getCmd(null,'hddtotalv2');
					if(is_object($hddtotalv2)){
						$hddtotalv2->event($dataresultv2['hddtotalv2']);
					}
					$hddusedv2 = $this->getCmd(null,'hddusedv2');
					if(is_object($hddusedv2)){
						$hddusedv2->event($dataresultv2['hddusedv2']);
					}
					$hddusedv2_pourc = $this->getCmd(null,'hddpourcusedv2');
					if(is_object($hddusedv2_pourc)){
						$hddusedv2_pourc->event($dataresultv2['hddpourcusedv2']);
					}
				}

				$cpu = $this->getCmd(null,'cpu');
				if(is_object($cpu)){
					$cpu->event($dataresult['cpu']);
				}

				$cpu_temp = $this->getCmd(null,'cpu_temp');
				if(is_object($cpu_temp)){
					$cpu_temp->event($dataresult['cpu_temp']);
				}

				$cnx_ssh = $this->getCmd(null,'cnx_ssh');
				if(is_object($cnx_ssh)){
					$cnx_ssh->event($dataresult['cnx_ssh']);
				}

				$Mempourc = $this->getCmd(null,'Mempourc');
				if(is_object($Mempourc)){
					$Mempourc->event($dataresult['Mempourc']);
				}

				$Swappourc = $this->getCmd(null,'Swappourc');
				if(is_object($Swappourc)){
					$Swappourc->event($dataresult['Swappourc']);
				}

				$perso1 = $this->getCmd(null,'perso1');
				if(is_object($perso1)){
					$perso1->event($dataresult['perso1']);
				}

				$perso2 = $this->getCmd(null,'perso2');
				if(is_object($perso2)){
					$perso2->event($dataresult['perso2']);
				}
			}
		}
		if (isset($cnx_ssh)) {
			if($cnx_ssh == 'KO'){
				$dataresult = array(
					'namedistri' => 'Connexion SSH KO',
					'cnx_ssh' => $cnx_ssh
				);
				$namedistri = $this->getCmd(null,'namedistri');
				if(is_object($namedistri)){
					$namedistri->event($dataresult['namedistri']);
				}
				$cnx_ssh = $this->getCmd(null,'cnx_ssh');
				if(is_object($cnx_ssh)){
					$cnx_ssh->event($dataresult['cnx_ssh']);
				}
			}
		}
	}

	function getCaseAction($paramaction) {
		if ($this->getConfiguration('maitreesclave') == 'deporte' && $this->getIsEnable()){

			$ip = $this->getConfiguration('addressip');
			$user = $this->getConfiguration('user');
			$pass = $this->getConfiguration('password');
			$port = $this->getConfiguration('portssh');
			$equipement = $this->getName();

			if (!$sshconnection = new SSH2($ip,$port)) {
				log::add('Monitoring', 'error', 'connexion SSH KO pour '.$equipement);
				$cnx_ssh = 'KO';
			}
			else {
				if (!$sshconnection->login($user, $pass)){
					log::add('Monitoring', 'error', 'Authentification SSH KO pour '.$equipement);
					$cnx_ssh = 'KO';
				}
				else {
					switch ($paramaction) {
						case "reboot":
							$paramaction =
							// $Rebootcmd = "sudo shutdown -r now >/dev/null & shutdown -r now >/dev/null";
							$Rebootcmd = "sudo reboot >/dev/null & reboot >/dev/null";
							$Reboot = $sshconnection->exec($Rebootcmd);
							log::add('Monitoring','debug','lancement commande deporte reboot ' . $this->getHumanName());
							break;
						case "poweroff":
							$paramaction =
							// $poweroffcmd = "sudo shutdown -P now >/dev/null & shutdown -P now >/dev/null";
							$poweroffcmd = "sudo poweroff >/dev/null & poweroff  >/dev/null";
							$poweroff = $sshconnection->exec($poweroffcmd);
							log::add('Monitoring','debug','lancement commande deporte poweroff' . $this->getHumanName());
							break;
					}
				}
			}
		}
		elseif ($this->getConfiguration('maitreesclave') == 'local' && $this->getIsEnable()) {
			if($this->getConfiguration('synology') == '1'){
				switch ($paramaction) {
					case "reboot":
						$paramaction =
						$cmdreboot = "sudo shutdown -r now >/dev/null & shutdown -r now >/dev/null";
						exec($cmdreboot);
						log::add('Monitoring','debug','lancement commande local reboot ' . $this->getHumanName());
						break;
					case "poweroff":
						$paramaction =
						exec('sudo shutdown -P now >/dev/null & shutdown -P now >/dev/null');
						log::add('Monitoring','debug','lancement commande local poweroff ' . $this->getHumanName());
					break;
				}
			}
			else {
				switch ($paramaction) {
					case "reboot":
						$paramaction =
						$cmdreboot = "sudo shutdown -r now >/dev/null & shutdown -r now >/dev/null";
						exec($cmdreboot);
						log::add('Monitoring','debug','lancement commande local reboot ' . $this->getHumanName());
						break;
					case "poweroff":
						$paramaction =
						exec('sudo shutdown -P now >/dev/null & shutdown -P now >/dev/null');
						log::add('Monitoring','debug','lancement commande local poweroff ' . $this->getHumanName());
						break;
				}
			}
		}
	}
}

class MonitoringCmd extends cmd {
	/* * *************************Attributs****************************** */
	public static $_widgetPossibility = array('custom' => false);

	/* * *********************Methode d'instance************************* */
	public function execute($_options = null) {
		$eqLogic = $this->getEqLogic();
		$paramaction = $this->getLogicalId();

		if ( $this->GetType = "action" ) {
			$eqLogic->getCmd();
			$contentCmd = $eqLogic->getCaseAction($paramaction);
		} else {
			throw new Exception(__('Commande non implémentée actuellement', __FILE__));
		}
		return true;
	}
}

?>
