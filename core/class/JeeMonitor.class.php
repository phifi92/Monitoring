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

class JeeMonitor extends eqLogic {

	public static function pull() {
		foreach (eqLogic::byType('JeeMonitor', true) as $JeeMonitor) {
			$JeeMonitor->getInformations();
			$mc = cache::byKey('JeeMonitorWidgetmobile' . $JeeMonitor->getId());
			$mc->remove();
			$mc = cache::byKey('JeeMonitorWidgetdashboard' . $JeeMonitor->getId());
			$mc->remove();
			$JeeMonitor->toHtml('mobile');
			$JeeMonitor->toHtml('dashboard');
			$JeeMonitor->refreshWidget();
		}
	}

	public static function dependancy_info() {
		$return = array();
		$return['log'] = 'JeeMonitor_update';
		$return['progress_file'] = '/tmp/dependancy_jeemonitor_in_progress';
		if (file_exists('/tmp/dependancy_jeemonitor_in_progress')) {
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
		if (file_exists('/tmp/compilation_jeemonitor_in_progress')) {
			return;
		}
		log::remove('JeeMonitor_update');
		$cmd = 'sudo /bin/bash ' . dirname(__FILE__) . '/../../ressources/install.sh';
		$cmd .= ' >> ' . log::getPathToLog('JeeMonitor_update') . ' 2>&1 &';
		exec($cmd);
	}

	public function postSave() {

		$JeeMonitorCmd = $this->getCmd(null, 'namedistri');
		if (!is_object($JeeMonitorCmd)) {
			$JeeMonitorCmd = new JeeMonitorCmd();
			$JeeMonitorCmd->setName(__('Distribution', __FILE__));
			$JeeMonitorCmd->setEqLogic_id($this->getId());
			$JeeMonitorCmd->setLogicalId('namedistri');
			$JeeMonitorCmd->setType('info');
			$JeeMonitorCmd->setSubType('string');
			$JeeMonitorCmd->setIsVisible(1);
			$JeeMonitorCmd->save();
		}

		$JeeMonitorCmd = $this->getCmd(null, 'uptime');
		if (!is_object($JeeMonitorCmd)) {
			$JeeMonitorCmd = new JeeMonitorCmd();
			$JeeMonitorCmd->setName(__('Démarré depuis', __FILE__));
			$JeeMonitorCmd->setEqLogic_id($this->getId());
			$JeeMonitorCmd->setLogicalId('uptime');
			$JeeMonitorCmd->setType('info');
			$JeeMonitorCmd->setSubType('string');
			$JeeMonitorCmd->save();
		}

		$JeeMonitorCmd = $this->getCmd(null, 'loadavg1mn');
		if (!is_object($JeeMonitorCmd)) {
			$JeeMonitorCmd = new JeeMonitorCmd();
			$JeeMonitorCmd->setName(__('Charge système 1 min', __FILE__));
			$JeeMonitorCmd->setEqLogic_id($this->getId());
			$JeeMonitorCmd->setLogicalId('loadavg1mn');
			$JeeMonitorCmd->setType('info');
			$JeeMonitorCmd->setSubType('numeric');
			$JeeMonitorCmd->save();
		}

		$JeeMonitorCmd = $this->getCmd(null, 'loadavg5mn');
		if (!is_object($JeeMonitorCmd)) {
			$JeeMonitorCmd = new JeeMonitorCmd();
			$JeeMonitorCmd->setName(__('Charge système 5 min', __FILE__));
			$JeeMonitorCmd->setEqLogic_id($this->getId());
			$JeeMonitorCmd->setLogicalId('loadavg5mn');
			$JeeMonitorCmd->setType('info');
			$JeeMonitorCmd->setSubType('numeric');
			$JeeMonitorCmd->save();
		}

		$JeeMonitorCmd = $this->getCmd(null, 'loadavg15mn');
		if (!is_object($JeeMonitorCmd)) {
			$JeeMonitorCmd = new JeeMonitorCmd();
			$JeeMonitorCmd->setName(__('Charge système 15 min', __FILE__));
			$JeeMonitorCmd->setEqLogic_id($this->getId());
			$JeeMonitorCmd->setLogicalId('loadavg15mn');
			$JeeMonitorCmd->setType('info');
			$JeeMonitorCmd->setSubType('numeric');
			$JeeMonitorCmd->save();
		}

		$JeeMonitorCmd = $this->getCmd(null, 'Mem');
		if (!is_object($JeeMonitorCmd)) {
			$JeeMonitorCmd = new JeeMonitorCmd();
			$JeeMonitorCmd->setName(__('Mémoire (Méga)', __FILE__));
			$JeeMonitorCmd->setEqLogic_id($this->getId());
			$JeeMonitorCmd->setLogicalId('Mem');
			$JeeMonitorCmd->setType('info');
			$JeeMonitorCmd->setSubType('string');
			$JeeMonitorCmd->save();
		}

		$JeeMonitorCmd = $this->getCmd(null, 'Mempourc');
		if (!is_object($JeeMonitorCmd)) {
			$JeeMonitorCmd = new JeeMonitorCmd();
			$JeeMonitorCmd->setName(__('Mémoire libre (pourcentage)', __FILE__));
			$JeeMonitorCmd->setEqLogic_id($this->getId());
			$JeeMonitorCmd->setLogicalId('Mempourc');
			$JeeMonitorCmd->setType('info');
			$JeeMonitorCmd->setSubType('numeric');
			$JeeMonitorCmd->save();
		}

		$JeeMonitorCmd = $this->getCmd(null, 'Mem_swap');
		if (!is_object($JeeMonitorCmd)) {
			$JeeMonitorCmd = new JeeMonitorCmd();
			$JeeMonitorCmd->setName(__('Swap', __FILE__));
			$JeeMonitorCmd->setEqLogic_id($this->getId());
			$JeeMonitorCmd->setLogicalId('Mem_swap');
			$JeeMonitorCmd->setType('info');
			$JeeMonitorCmd->setSubType('string');
			$JeeMonitorCmd->save();
		}

		$JeeMonitorCmd = $this->getCmd(null, 'Swappourc');
		if (!is_object($JeeMonitorCmd)) {
			$JeeMonitorCmd = new JeeMonitorCmd();
			$JeeMonitorCmd->setName(__('Swap libre (pourcentage)', __FILE__));
			$JeeMonitorCmd->setEqLogic_id($this->getId());
			$JeeMonitorCmd->setLogicalId('Swappourc');
			$JeeMonitorCmd->setType('info');
			$JeeMonitorCmd->setSubType('numeric');
			$JeeMonitorCmd->save();
		}

		$JeeMonitorCmd = $this->getCmd(null, 'ethernet0');
		if (!is_object($JeeMonitorCmd)) {
			$JeeMonitorCmd = new JeeMonitorCmd();
			$JeeMonitorCmd->setName(__('Réseau (M)', __FILE__));
			$JeeMonitorCmd->setEqLogic_id($this->getId());
			$JeeMonitorCmd->setLogicalId('ethernet0');
			$JeeMonitorCmd->setType('info');
			$JeeMonitorCmd->setSubType('string');
			$JeeMonitorCmd->save();
		}

		$JeeMonitorCmd = $this->getCmd(null, 'hddtotal');
		if (!is_object($JeeMonitorCmd)) {
			$JeeMonitorCmd = new JeeMonitorCmd();
			$JeeMonitorCmd->setName(__('Espace disque Total', __FILE__));
			$JeeMonitorCmd->setEqLogic_id($this->getId());
			$JeeMonitorCmd->setLogicalId('hddtotal');
			$JeeMonitorCmd->setType('info');
			$JeeMonitorCmd->setSubType('string');
			$JeeMonitorCmd->save();
		}

		$JeeMonitorCmd = $this->getCmd(null, 'hddused');
		if (!is_object($JeeMonitorCmd)) {
			$JeeMonitorCmd = new JeeMonitorCmd();
			$JeeMonitorCmd->setName(__('Espace disque Utilisé', __FILE__));
			$JeeMonitorCmd->setEqLogic_id($this->getId());
			$JeeMonitorCmd->setLogicalId('hddused');
			$JeeMonitorCmd->setType('info');
			$JeeMonitorCmd->setSubType('string');
			$JeeMonitorCmd->save();
		}

		$JeeMonitorCmd = $this->getCmd(null, 'hddpourcused');
		if (!is_object($JeeMonitorCmd)) {
			$JeeMonitorCmd = new JeeMonitorCmd();
			$JeeMonitorCmd->setName(__('Espace disque Utilisé (pourcentage)', __FILE__));
			$JeeMonitorCmd->setEqLogic_id($this->getId());
			$JeeMonitorCmd->setLogicalId('hddpourcused');
			$JeeMonitorCmd->setType('info');
			$JeeMonitorCmd->setSubType('numeric');
			$JeeMonitorCmd->save();
		}

		if ($this->getConfiguration('synology') == '1') {
			if ($this->getConfiguration('synologyv2') == '1') {
				$JeeMonitorCmd = $this->getCmd(null, 'hddtotalv2');
				if (!is_object($JeeMonitorCmd)) {
					$JeeMonitorCmd = new JeeMonitorCmd();
					$JeeMonitorCmd->setName(__('Syno Volume 2 Espace disque Total', __FILE__));
					$JeeMonitorCmd->setEqLogic_id($this->getId());
					$JeeMonitorCmd->setLogicalId('hddtotalv2');
					$JeeMonitorCmd->setType('info');
					$JeeMonitorCmd->setSubType('string');
					$JeeMonitorCmd->save();
				}

				$JeeMonitorCmd = $this->getCmd(null, 'hddusedv2');
				if (!is_object($JeeMonitorCmd)) {
					$JeeMonitorCmd = new JeeMonitorCmd();
					$JeeMonitorCmd->setName(__('Syno Volume 2 Espace disque Utilisé', __FILE__));
					$JeeMonitorCmd->setEqLogic_id($this->getId());
					$JeeMonitorCmd->setLogicalId('hddusedv2');
					$JeeMonitorCmd->setType('info');
					$JeeMonitorCmd->setSubType('string');
					$JeeMonitorCmd->save();
				}

				$JeeMonitorCmd = $this->getCmd(null, 'hddpourcusedv2');
				if (!is_object($JeeMonitorCmd)) {
					$JeeMonitorCmd = new JeeMonitorCmd();
					$JeeMonitorCmd->setName(__('Syno Volume 2 Espace disque Utilisé (pourcentage)', __FILE__));
					$JeeMonitorCmd->setEqLogic_id($this->getId());
					$JeeMonitorCmd->setLogicalId('hddpourcusedv2');
					$JeeMonitorCmd->setType('info');
					$JeeMonitorCmd->setSubType('numeric');
					$JeeMonitorCmd->save();
				}

			} elseif ($this->getConfiguration('synologyv2') == '0') {
				$JeeMonitorCmd = $this->getCmd(null, 'hddtotalv2');
				if ( is_object($JeeMonitorCmd)) {
					$JeeMonitorCmd->remove();
				}
				$JeeMonitorCmd = $this->getCmd(null, 'hddusedv2');
				if ( is_object($JeeMonitorCmd)) {
					$JeeMonitorCmd->remove();
				}
				$JeeMonitorCmd = $this->getCmd(null, 'hddpourcusedv2');
				if ( is_object($JeeMonitorCmd)) {
					$JeeMonitorCmd->remove();
				}
			}
		}

		$JeeMonitorCmd = $this->getCmd(null, 'cpu');
		if (!is_object($JeeMonitorCmd)) {
			$JeeMonitorCmd = new JeeMonitorCmd();
			$JeeMonitorCmd->setName(__('CPU(s)', __FILE__));
			$JeeMonitorCmd->setEqLogic_id($this->getId());
			$JeeMonitorCmd->setLogicalId('cpu');
			$JeeMonitorCmd->setType('info');
			$JeeMonitorCmd->setSubType('string');
			$JeeMonitorCmd->save();
		}

		$JeeMonitorCmd = $this->getCmd(null, 'cpu_temp');
		if (!is_object($JeeMonitorCmd)) {
			$JeeMonitorCmd = new JeeMonitorCmd();
			$JeeMonitorCmd->setName(__('Température CPU', __FILE__));
			$JeeMonitorCmd->setEqLogic_id($this->getId());
			$JeeMonitorCmd->setLogicalId('cpu_temp');
			$JeeMonitorCmd->setType('info');
			$JeeMonitorCmd->setSubType('numeric');
			$JeeMonitorCmd->save();
		}

		$JeeMonitorCmd = $this->getCmd(null, 'cnx_ssh');
		if (!is_object($JeeMonitorCmd)) {
			$JeeMonitorCmd = new JeeMonitorCmd();
			$JeeMonitorCmd->setName(__('Statut cnx SSH Scénario', __FILE__));
			$JeeMonitorCmd->setEqLogic_id($this->getId());
			$JeeMonitorCmd->setLogicalId('cnx_ssh');
			$JeeMonitorCmd->setType('info');
			$JeeMonitorCmd->setSubType('string');
			$JeeMonitorCmd->save();
		}


		$JeeMonitorCmd = $this->getCmd(null, 'perso2');
		if (!is_object($JeeMonitorCmd)) {
			$JeeMonitorCmd = new JeeMonitorCmd();
			$JeeMonitorCmd->setName(__('perso2', __FILE__));
			$JeeMonitorCmd->setEqLogic_id($this->getId());
			$JeeMonitorCmd->setLogicalId('perso2');
			$JeeMonitorCmd->setType('info');
			$JeeMonitorCmd->setSubType('string');
			$JeeMonitorCmd->save();
		}

		$JeeMonitorCmd = $this->getCmd(null, 'perso1');
		if (!is_object($JeeMonitorCmd)) {
			$JeeMonitorCmd = new JeeMonitorCmd();
			$JeeMonitorCmd->setName(__('perso1', __FILE__));
			$JeeMonitorCmd->setEqLogic_id($this->getId());
			$JeeMonitorCmd->setLogicalId('perso1');
			$JeeMonitorCmd->setType('info');
			$JeeMonitorCmd->setSubType('string');
			$JeeMonitorCmd->save();
		}

		$JeeMonitorCmd = $this->getCmd(null, 'reboot');
		if (!is_object($JeeMonitorCmd)) {
			$JeeMonitorCmd = new JeeMonitorCmd();
			$JeeMonitorCmd->setName(__('reboot', __FILE__));
			$JeeMonitorCmd->setEqLogic_id($this->getId());
			$JeeMonitorCmd->setLogicalId('reboot');
			$JeeMonitorCmd->setType('action');
			$JeeMonitorCmd->setSubType('other');
			$JeeMonitorCmd->save();
		}

		$JeeMonitorCmd = $this->getCmd(null, 'poweroff');
		if (!is_object($JeeMonitorCmd)) {
			$JeeMonitorCmd = new JeeMonitorCmd();
			$JeeMonitorCmd->setName(__('poweroff', __FILE__));
			$JeeMonitorCmd->setEqLogic_id($this->getId());
			$JeeMonitorCmd->setLogicalId('poweroff');
			$JeeMonitorCmd->setType('action');
			$JeeMonitorCmd->setSubType('other');
			$JeeMonitorCmd->save();
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

		$hddpourcused = $this->getCmd(null,'hddpourcused');
		$replace['#hddpourcused#'] = (is_object($hddpourcused)) ? $hddpourcused->execCmd() : '';
		$replace['#hddpourcusedid#'] = is_object($hddpourcused) ? $hddpourcused->getId() : '';

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

			$hddpourcusedv2 = $this->getCmd(null,'hddpourcusedv2');
			$replace['#hddpourcusedv2#'] = (is_object($hddpourcusedv2)) ? $hddpourcusedv2->execCmd() : '';
			$replace['#hddpourcusedv2id#'] = is_object($hddpourcusedv2) ? $hddpourcusedv2->getId() : '';

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

		$html = template_replace($replace, getTemplate('core', $_version, 'JeeMonitor','JeeMonitor'));
		cache::set('JeeMonitorWidget' . $_version . $this->getId(), $html, 0);
		return $html;
	}

	public function getInformations() {

		$swap_pourc_cmd = '';
		$uname = "Inconnu";
		$Mem = '';
		$mem_usage_pourc = '';
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
				log::add('JeeMonitor', 'error', 'connexion SSH KO pour '.$equipement);
				$cnx_ssh = 'KO';
			}
			else {
				if (!$sshconnection->login($user, $pass)) {
					log::add('JeeMonitor', 'error', 'Authentification SSH KO pour '.$equipement);
					$cnx_ssh = 'KO';
				}
				else {
					$cnx_ssh = 'OK';
					$ARMvcmd = "lscpu | grep Architecture | awk '{ print $2 }'";
					$uptimecmd = "uptime";

					if($this->getConfiguration('synology') == '1') {
						$namedistricmd = "get_key_value /etc/synoinfo.conf upnpmodelname";
						$freecmd = "cat /proc/meminfo | cut -d':' -f2 | awk '{ print $1}' | tr '\n' ' ' | awk '{ print $1,$2,$3,$4}'";
						$swapcmd = "free | grep 'Swap' | head -1 | awk '{ print $2,$3,$4 }'";
						$VersionIDcmd = "awk -F'=' '/productversion/ {print $2}' /etc.defaults/VERSION | tr -d '\"'";
					}
					else {
						$namedistricmd = "cat /etc/*-release | grep PRETTY_NAME=";
						$freecmd = "free | grep 'Mem' | head -1 | awk '{ print $2,$3,$4,$7 }'";
						$swapcmd = "free -h | awk -F':' '/Swap|Partition d.échange/ { print $2 }' | awk '{ print $1,$2,$3}'";
						$swap_pourc_cmd = "free | awk -F':' '/Swap|Partition d.échange/ { print $2 }' | awk '{ print $1,$2,$3}'";
						$VersionIDcmd = "awk -F'=' '/VERSION_ID/ {print $2}' /etc/os-release | tr -d '\"'";
					}

					$bitdistricmd = "getconf LONG_BIT";
					$loadavgcmd = "cat /proc/loadavg";
					$ReseauRXTXcmd = "cat /proc/net/dev | grep ".$cartereseau." | awk '{print $2,$10}'";
					$perso_1cmd = $this->getConfiguration('perso1');
					$perso_2cmd = $this->getConfiguration('perso2');

					$ARMv = trim($sshconnection->exec($ARMvcmd));

					$uptime = $sshconnection->exec($uptimecmd);

					$VersionID = trim($sshconnection->exec($VersionIDcmd));
					$namedistri = $sshconnection->exec($namedistricmd);
					$bitdistri = $sshconnection->exec($bitdistricmd);

					$loadav = $sshconnection->exec($loadavgcmd);

					$ReseauRXTX = $sshconnection->exec($ReseauRXTXcmd);

					$free = $sshconnection->exec($freecmd);
					$swap = $sshconnection->exec($swapcmd);
					$Swappourc = $sshconnection->exec($swap_pourc_cmd);

					$perso_1 = $sshconnection->exec($perso_1cmd);
					$perso_2 = $sshconnection->exec($perso_2cmd);

					if($this->getConfiguration('synology') == '1') {
						$platformcmd = "get_key_value /etc/synoinfo.conf unique | cut -d'_' -f2";
						$synoplatorm = $sshconnection->exec($platformcmd);

						$nbcpuARMcmd = "cat /proc/sys/kernel/syno_CPU_info_core";
						$nbcpu = trim($sshconnection->exec($nbcpuARMcmd));

						$cpufreq0ARMcmd = "cat /proc/sys/kernel/syno_CPU_info_clock";
						$cpufreq0 = trim($sshconnection->exec($cpufreq0ARMcmd));
						
						$hddcmd = "df -h | grep 'vg1000\|volume1' | head -1 | awk '{ print $2,$3,$5 }' | cut -d '%' -f1";
						$hdd = $sshconnection->exec($hddcmd);

						$versionsynocmd = "cat /etc.defaults/VERSION | cut -d'=' -f2 | cut -d'=' -f2 | tr '\n' ' ' | awk '{ print $3,$4,$5,$9}'";
						$versionsyno = $sshconnection->exec($versionsynocmd);

						$synocmdTemp='$(find /sys/devices/* -name temp*_input | head -1)';
						if($this->getconfiguration('syno_use_temp_path')) $synocmdTemp=$this->getconfiguration('syno_temp_path');				

						$cputemp0cmd = "timeout 3 cat ".$synocmdTemp;
						log::add(__CLASS__,"debug", "commande temp syno : ".$cputemp0cmd);
						$cputemp0 = $sshconnection->exec($cputemp0cmd);
					}
					if($this->getConfiguration('synology') == '1' && $SynoV2Visible == 'OK' && $this->getConfiguration('synologyv2') == '1') {
						$hddv2cmd = "df -h | grep 'vg1001\|volume2' | head -1 | awk '{ print $2,$3,$5 }' | cut -d '%' -f1"; // DSM 5.x & 6.x
						$hddv2 = $sshconnection->exec($hddv2cmd);
					}
					if ($ARMv == 'armv6l') {
						$nbcpuARMcmd = "lscpu | grep 'CPU(s):' | awk '{ print $2 }'";
						$nbcpu = trim($sshconnection->exec($nbcpuARMcmd));
						
						$uname = '.';

						$hddcmd = "df -h | grep '/$' | head -1 | awk '{ print $2,$3,$5 }'";
						$hdd = $sshconnection->exec($hddcmd);

						$cpufreq0ARMcmd = "cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq";
						$cpufreq0 = $sshconnection->exec($cpufreq0ARMcmd);

						$cpuTempCmd = $this->getCmd(null,'cpu_temp');
						if (is_object($cpuTempCmd) && $cpuTempCmd->getIsVisible() == 1) {
							$cputemp0armv6lcmd = "cat /sys/class/thermal/thermal_zone0/temp";
							$cputemp0 = $sshconnection->exec($cputemp0armv6lcmd);
						}

					}
					elseif ($ARMv == 'armv7l' || $ARMv == 'aarch64' || $ARMv == 'mips64'){
						$nbcpuARMcmd = "lscpu | grep '^CPU(s):' | awk '{ print $2 }'";
						$nbcpu = trim($sshconnection->exec($nbcpuARMcmd));
						
						$uname = '.';

						$cpufreq0ARMcmd = "cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq";
						$cpufreq0 = trim($sshconnection->exec($cpufreq0ARMcmd));

						$hddcmd = "df -h | grep '/$' | head -1 | awk '{ print $2,$3,$5 }'";
						$hdd = $sshconnection->exec($hddcmd);

						$cpuTempCmd = $this->getCmd(null,'cpu_temp');
						if (is_object($cpuTempCmd) && $cpuTempCmd->getIsVisible() == 1) {
							$cputemp0RPi2cmd = "cat /sys/class/thermal/thermal_zone0/temp";	// OK RPi2
							$cputemp0 = $sshconnection->exec($cputemp0RPi2cmd);

							if ($cputemp0 == '') {
								$cputemp0armv7lcmd = "cat /sys/devices/platform/sunxi-i2c.0/i2c-0/0-0034/temp1_input"; // OK Banana Pi (Cubie surement un jour...)
								$cputemp0 = $sshconnection->exec($cputemp0armv7lcmd);
							}
						}

					}
					elseif ($ARMv == 'i686' || $ARMv == 'x86_64' || $ARMv == 'i386'){
						$NF = '';
						$cputemp0 ='';
						$uname = '.';
						$nbcpuVMcmd = "lscpu | grep 'Processeur(s)' | awk '{ print $NF }'"; // OK pour Debian
						
						$nbcpu = $sshconnection->exec($nbcpuVMcmd);

						if ($nbcpu == '') {
							$nbcpuVMbiscmd = "lscpu | grep '^CPU(s)' | awk '{ print $NF }'"; // OK pour LXC Linux/Ubuntu
							$nbcpu = $sshconnection->exec($nbcpuVMbiscmd);
						}
						$nbcpu = preg_replace("/[^0-9]/","",$nbcpu);

						$hddcmd = "df -h | grep '/$' | head -1 | awk '{ print $2,$3,$5 }'";
						$hdd = $sshconnection->exec($hddcmd);

						$cpufreqVMcmd = "lscpu | grep 'Vitesse du processeur en MHz' | awk '{print $NF}'"; // OK pour Debian/Ubuntu
						$cpufreq = $sshconnection->exec($cpufreqVMcmd);

						if ($cpufreq == '') {
							$cpufreqVMbiscmd = "lscpu | grep '^CPU MHz' | awk '{ print $NF }'";	// OK pour LXC Linux
							$cpufreq = $sshconnection->exec($cpufreqVMbiscmd);
						}
						if ($cpufreq == '') {
							$cpufreqVMbiscmd = "cat /proc/cpuinfo | grep '^cpu MHz' | head -1 | cut -d':' -f2 | awk '{ print $NF }'";	// OK pour Debian 10/11, Ubuntu 22.04
							$cpufreq = $sshconnection->exec($cpufreqVMbiscmd);
						}
						$cpufreq=preg_replace("/[^0-9.]/","",$cpufreq);

						$cpuTempCmd = $this->getCmd(null,'cpu_temp');
						if (is_object($cpuTempCmd) && $cpuTempCmd->getIsVisible() == 1) {
							if (file_exists('/sys/devices/virtual/thermal/thermal_zone0/temp')) {
								$cputemp0cmd = "cat /sys/devices/virtual/thermal/thermal_zone0/temp";	// OK Dell WYSE
								$cputemp0 = $sshconnection->exec($cputemp0cmd);
							}

							if ($cputemp0 == '') {
								if (file_exists('/sys/devices/platform/coretemp.0/hwmon/hwmon0/temp?_input')) {
									$cputemp0cmd = "cat /sys/devices/platform/coretemp.0/hwmon/hwmon0/temp?_input";	// OK AOpen DE2700
									$cputemp0 = $sshconnection->exec($cputemp0cmd);
								}
							}
							if ($cputemp0 == '') {
								if (file_exists('/sys/devices/pci0000:00/0000:00:18.3/hwmon/hwmon0/temp1_input')) {
									$cputemp0AMDcmd = "cat /sys/devices/pci0000:00/0000:00:18.3/hwmon/hwmon0/temp1_input";	// OK AMD Ryzen
									$cputemp0 = $sshconnection->exec($cputemp0AMDcmd);
								}
							}
							if ($cputemp0 == '') {
								$cputemp0sensorscmd = "sensors 2>/dev/null | awk '{if (match($0, \"MB Temperature\")){printf(\"%f\",$3);} }'"; // OK by sensors
								$cputemp0 = $sshconnection->exec($cputemp0sensorscmd);
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

							$nbcpuARMcmd = "grep 'model name' /proc/cpuinfo | wc -l";
							$nbcpu = trim($sshconnection->exec($nbcpuARMcmd));

							$hddcmd = "df -h | grep '/dev/mmcblk0p2' | head -1 | awk '{ print $2,$3,$5 }'";
							$hdd = $sshconnection->exec($hddcmd);

							$cpufreq0ARMcmd = "cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq";
							$cpufreq0 = $sshconnection->exec($cpufreq0ARMcmd);

							$cpuTempCmd = $this->getCmd(null,'cpu_temp');
							if (is_object($cpuTempCmd) && $cpuTempCmd->getIsVisible() == 1) {
								if (file_exists('/sys/class/thermal/thermal_zone0/temp')) {
									$cputemp0armv6lcmd = "cat /sys/class/thermal/thermal_zone0/temp";
									$cputemp0 = $sshconnection->exec($cputemp0armv6lcmd);
								}
							}

						}
						elseif (preg_match("#osmc#", $namedistri)) {
							$bitdistri = '32';
							$ARMv = 'arm';

							$nbcpuARMcmd = "grep 'model name' /proc/cpuinfo | wc -l";
							$nbcpu = trim($sshconnection->exec($nbcpuARMcmd));

							$hddcmd = "df -h | grep '/$' | head -1 | awk '{ print $2,$3,$5 }'";
							$hdd = $sshconnection->exec($hddcmd);

							$cpufreq0ARMcmd = "cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq";
							$cpufreq0 = $sshconnection->exec($cpufreq0ARMcmd);

							$cpuTempCmd = $this->getCmd(null,'cpu_temp');
							if (is_object($cpuTempCmd) && $cpuTempCmd->getIsVisible() == 1) {
								if (file_exists('/sys/class/thermal/thermal_zone0/temp')) {
									$cputemp0armv6lcmd = "cat /sys/class/thermal/thermal_zone0/temp";
									$cputemp0 = $sshconnection->exec($cputemp0armv6lcmd);
								}
							}
						}
						elseif (preg_match("#piCorePlayer#", $uname)) {
							$bitdistri = '32';
							$ARMv = 'arm';
							$namedistricmd = "uname -a | awk '{print $2,$3}'";
							$namedistri = $sshconnection->exec($namedistricmd);

							$nbcpuARMcmd = "grep 'model name' /proc/cpuinfo | wc -l";
							$nbcpu = trim($sshconnection->exec($nbcpuARMcmd));

							$hddcmd = "df -h | grep /dev/mmcblk0p | head -1 | awk '{print $2,$3,$5 }'";
							$hdd = $sshconnection->exec($hddcmd);

							$cpufreq0ARMcmd = "cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq";
							$cpufreq0 = $sshconnection->exec($cpufreq0ARMcmd);

							$cpuTempCmd = $this->getCmd(null,'cpu_temp');
							if (is_object($cpuTempCmd) && $cpuTempCmd->getIsVisible() == 1) {
								if (file_exists('/sys/class/thermal/thermal_zone0/temp')) {
									$cputemp0armv6lcmd = "cat /sys/class/thermal/thermal_zone0/temp";
									$cputemp0 = $sshconnection->exec($cputemp0armv6lcmd);
								}
							}

						}
						elseif (preg_match("#FreeBSD#", $uname)) {
							$namedistricmd = "uname -a | awk '{ print $1,$3}'";
							$namedistri = $sshconnection->exec($namedistricmd);

							$ARMvcmd = "sysctl hw.machine | awk '{ print $2}'";
							$ARMv = trim($sshconnection->exec($ARMvcmd));

							$loadavgcmd = "uptime | awk '{print $8,$9,$10}'";
							$loadav = $sshconnection->exec($loadavgcmd);

							$freecmd = "dmesg | grep Mem | tr '\n' ' ' | awk '{print $4,$10}'";
							$free = $sshconnection->exec($freecmd);

							$bitdistricmd = "sysctl kern.smp.maxcpus | awk '{ print $2}'";
							$bitdistri = $sshconnection->exec($bitdistricmd);

							$nbcpuARMcmd = "sysctl hw.ncpu | awk '{ print $2}'";
							$nbcpu = trim($sshconnection->exec($nbcpuARMcmd));

							$hddcmd = "df -h | grep '/$' | head -1 | awk '{ print $2,$3,$5 }'";
							$hdd = $sshconnection->exec($hddcmd);

							$cpufreq0ARMcmd = "sysctl -a | egrep -E 'cpu.0.freq' | awk '{ print $2}'";
							$cpufreq0 = $sshconnection->exec($cpufreq0ARMcmd);

							$cpuTempCmd = $this->getCmd(null,'cpu_temp');
							if (is_object($cpuTempCmd) && $cpuTempCmd->getIsVisible() == 1) {
								$cputemp0armv6lcmd = "sysctl -a | egrep -E 'cpu.0.temp' | awk '{ print $2}'";
								$cputemp0 = $sshconnection->exec($cputemp0armv6lcmd);
							}
						}
					}
				}
			}
		}
		elseif($this->getConfiguration('maitreesclave') == 'local' && $this->getIsEnable()) {
			$cnx_ssh = 'No';
			$uptimecmd = "uptime";
			
			if($this->getConfiguration('synology') == '1') {
				$namedistricmd = "get_key_value /etc/synoinfo.conf upnpmodelname";
				$freecmd = "cat /proc/meminfo | cut -d':' -f2 | awk '{ print $1}' | tr '\n' ' ' | awk '{ print $1,$2,$3,$4}'";
				$swapcmd = "free | grep 'Swap' | head -1 | awk '{ print $2,$3,$4 }'";
				$hddcmd = "df -h | grep 'vg1000\|volume1' | head -1 | awk '{ print $2,$3,$5 }' | cut -d '%' -f1";
				$VersionIDcmd = "awk -F'=' '/productversion/ {print $2}' /etc.defaults/VERSION | tr -d '\"'";
			}
			else {
				$ARMvcmd = "lscpu | grep Architecture | awk '{ print $2 }'";
				$namedistricmd = "cat /etc/*-release | grep PRETTY_NAME=";
				$VersionIDcmd = "awk -F'=' '/VERSION_ID/ {print $2}' /etc/os-release | tr -d '\"'";
				$freecmd = "free | grep 'Mem' | head -1 | awk '{ print $2,$3,$4,$7 }'";
				$swapcmd = "free -h | awk -F':' '/Swap|Partition d.échange/ { print $2 }' | awk '{ print $1,$2,$3}'";
				$swap_pourc_cmd = "free | awk -F':' '/Swap|Partition d.échange/ { print $2 }' | awk '{ print $1,$2,$3}'";
				$hddcmd = "df -h | grep '/$' | head -1 | awk '{ print $2,$3,$5 }'";
				$bitdistricmd = "getconf LONG_BIT";
				$ARMv = exec($ARMvcmd);
				$bitdistri = exec($bitdistricmd);
			}

			$loadavgcmd = "cat /proc/loadavg";
			$ReseauRXTXcmd = "cat /proc/net/dev | grep ".$cartereseau." | awk '{print $2,$10}'";
			$perso_1cmd = $this->getConfiguration('perso1');
			$perso_2cmd = $this->getConfiguration('perso2');
			$uptime = exec($uptimecmd);
			$namedistri = exec($namedistricmd);
			$VersionID = trim(exec($VersionIDcmd));
			log::add('JeeMonitor', 'debug', 'VersionID(2) = >'.$VersionID.'<');
			$loadav = exec($loadavgcmd);
			$ReseauRXTX = exec($ReseauRXTXcmd);
			$hdd = exec($hddcmd);
			$free = exec($freecmd);
			$swap = exec($swapcmd);
			$Swappourc = exec($swap_pourc_cmd);
			
			if ($perso_1cmd != '') {
				$perso_1 = exec ($perso_1cmd);
			}
			
			if ($perso_2cmd != '') {
				$perso_2 = exec ($perso_2cmd);
			}

			if($this->getConfiguration('synology') == '1'){
				$uname = '.';
				$nbcpuARMcmd = "cat /proc/sys/kernel/syno_CPU_info_core";
				$cpufreq0ARMcmd = "cat /proc/sys/kernel/syno_CPU_info_clock";
				$versionsynocmd = "cat /etc.defaults/VERSION | cut -d'=' -f2 | cut -d'=' -f2 | tr '\n' ' ' | awk '{ print $3,$4,$5,$9}'";
				
				$nbcpu = exec($nbcpuARMcmd);
				$cpufreq0 = exec($cpufreq0ARMcmd);
				$versionsyno = exec($versionsynocmd);
			}
			
			if($this->getConfiguration('synology') == '1' && $SynoV2Visible == 'OK' && $this->getConfiguration('synologyv2') == '1') {
				$hddv2cmd = "df -h | grep 'vg1001' | head -1 | awk '{ print $2,$3,$5 }' | cut -d '%' -f1";
				$hddv2 = exec($hddv2cmd);
			}
			elseif ($ARMv == 'armv6l') {
				$uname = '.';

				$nbcpuARMcmd = "lscpu | grep 'CPU(s):' | awk '{ print $2 }'";
				$nbcpu = exec($nbcpuARMcmd);
				
				if (file_exists('/sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq')) {
					$cpufreq0ARMcmd = "cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq";
					$cpufreq0 = exec($cpufreq0ARMcmd);
				}
				$cpuTempCmd = $this->getCmd(null,'cpu_temp');
				if (is_object($cpuTempCmd) && $cpuTempCmd->getIsVisible() == 1) {
					if (file_exists('/sys/class/thermal/thermal_zone0/temp')) {
						$cputemp0armv6lcmd = "cat /sys/class/thermal/thermal_zone0/temp";
						$cputemp0 = exec($cputemp0armv6lcmd);
					}
				}
			}
			elseif ($ARMv == 'armv7l' || $ARMv == 'aarch64') {
				$uname = '.';
				$cputemp0 = '';
				$cpufreq0 = '';

				$nbcpuARMcmd = "lscpu | grep '^CPU(s):' | awk '{ print $2 }'";
				$nbcpu = exec($nbcpuARMcmd);
				if (file_exists('/sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq')) {
					$cpufreq0ARMcmd = "cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq";
					$cpufreq0 = exec($cpufreq0ARMcmd);
				}
				$cpuTempCmd = $this->getCmd(null,'cpu_temp');
				if (is_object($cpuTempCmd) && $cpuTempCmd->getIsVisible() == 1) {
					if (file_exists('/sys/class/thermal/thermal_zone0/temp')) {
						$cputemp0RPi2cmd = "cat /sys/class/thermal/thermal_zone0/temp 2>/dev/null";	// OK RPi2/3, Odroid
						$cputemp0 = exec($cputemp0RPi2cmd);
					}
					if ($cputemp0 == '') {
						if (file_exists('/sys/devices/platform/sunxi-i2c.0/i2c-0/0-0034/temp1')) {
							$cputemp0armv7lcmd = "cat /sys/devices/platform/sunxi-i2c.0/i2c-0/0-0034/temp1 2>/dev/null"; // OK Banana Pi (Cubie surement un jour...)
							$cputemp0 = exec($cputemp0armv7lcmd);
						}
					}
				}

			}
			elseif ($ARMv == 'i686' || $ARMv == 'x86_64' || $ARMv == 'i386') {
				$NF = '';
				$uname = '.';
				$cputemp0 = '';
				$cpufreq = '';

				$nbcpuVMcmd = "lscpu | grep 'Processeur(s)' | awk '{ print $NF }'"; // OK pour Debian
				$nbcpu = exec($nbcpuVMcmd);

				if ($nbcpu == ''){
					$nbcpuVMbiscmd = "lscpu | grep '^CPU(s)' | awk '{ print $NF }'"; // OK pour LXC Linux/Ubuntu
					$nbcpu = exec($nbcpuVMbiscmd);
				}
				$nbcpu = preg_replace("/[^0-9]/","",$nbcpu);
				
				$cpufreqVMcmd = "lscpu | grep 'Vitesse du processeur en MHz' | awk '{print $NF}'"; // OK pour Debian/Ubuntu
				$cpufreq = exec($cpufreqVMcmd);
				
				if ($cpufreq == ''){
					$cpufreqVMbiscmd = "lscpu | grep '^CPU MHz' | awk '{ print $NF }'";	// OK pour LXC Linux
					$cpufreq = exec($cpufreqVMbiscmd);
				}
				if ($cpufreq == ''){
					$cpufreqVMbiscmd = "cat /proc/cpuinfo | grep '^cpu MHz' | head -1 | cut -d':' -f2 | awk '{ print $NF }'";	// OK pour Debian 10/11, Ubuntu 22.04
					$cpufreq = exec($cpufreqVMbiscmd);
				}
				$cpufreq = preg_replace("/[^0-9.]/","",$cpufreq);
				
				$cpuTempCmd = $this->getCmd(null,'cpu_temp');
				
				if (is_object($cpuTempCmd) && $cpuTempCmd->getIsVisible() == 1) {
					if (file_exists('/sys/devices/virtual/thermal/thermal_zone0/temp')) {
						$cputemp0RPi2cmd = "cat /sys/devices/virtual/thermal/thermal_zone0/temp"; // OK Dell Whyse
						$cputemp0 = exec($cputemp0RPi2cmd);
					}
				
					if ($cputemp0 == '') {
						$cputemp0AOpencmd = "cat /sys/devices/platform/coretemp.0/hwmon/hwmon0/temp?_input";	// OK AOpen DE2700
						$cputemp0 = exec($cputemp0AOpencmd);
					}
				
					if ($cputemp0 == '') {
						$cputemp0AMDcmd = "cat /sys/devices/pci0000:00/0000:00:18.3/hwmon/hwmon0/temp1_input";	// OK AMD Ryzen
						$cputemp0 = exec($cputemp0AMDcmd);
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
						if (isset($versionsyno[0]) && isset($versionsyno[1]) && isset($versionsyno[2]) && isset($versionsyno[3])) {
							$versionsyno = 'DSM '.$versionsyno[0].'.'.$versionsyno[1].'-'.$versionsyno[2].' Update '.$versionsyno[3];
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
							$hddpourcusedv2 = preg_replace("/[^0-9.]/","",$hdddatav2[2]);
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

				if (isset($free)) {
					if (!preg_match("#FreeBSD#", $uname)) {
						$free = explode(' ', $free);
						if($this->getConfiguration('synology') == '1'){
							if (isset($free[1]) && isset($free[3])) {
								$freelibre = intval($free[1]) + intval($free[3]);
								log::add('JeeMonitor', 'debug', 'Version Syno ('.$VersionID.') et Free1 et Free3 ok : '.$freelibre.' / '.$free[1].' / '.$free[3]);
							}
						}
						if(intval($VersionID) >= 9 && isset($free[3])){
							$freelibre = intval($free[3]);
							log::add('JeeMonitor', 'debug', 'VersionID ('.$VersionID.') >= 9 et Free3 ok : '.$freelibre.' / '.$free[3]);
						}
						elseif(intval($VersionID) < 9 && isset($free[2]) && isset($free[3])){
							$freelibre = intval($free[2]) + intval($free[3]);
							log::add('JeeMonitor', 'debug', 'Version ('.$VersionID.') < 9 et Free2 et Free3 ok : '.$freelibre.' / '.$free[2].' / '.$free[3]);
						}
						
						if (isset($free[0]) && isset($freelibre)) {
							$mem_usage_pourc = round(intval($freelibre) / intval($free[0]) * 100);
							log::add('JeeMonitor', 'debug', 'Variable MemPourcentage = '.$mempourcusage.' / freelibre : '.$freelibre.' / free0 : '.$free[0]);
						}
						
						$Swappourc = explode(' ', $Swappourc);
						if (isset($Swappourc[0]) && isset($Swappourc[1]))
						{
							if (intval($Swappourc[0]) != 0){
								$Swappourcusage = round(intval($Swappourc[1]) / intval($Swappourc[0]) * 100, 2);
							}
							else {
								$Swappourcusage = 0;
							}
						}

						if (isset($freelibre)) {
							if ((intval($freelibre) / 1000) > 1000) {
								$freelibre = round(intval($freelibre) / 1000000, 2) . " Go";
							}
							else {
								$freelibre = round(intval($freelibre) / 1000) . " Mo";
							}
						}
						if (isset($free[0])) {
							if ((intval($free[0]) / 1000) > 1000) {
								$memtotal = round(intval($free[0]) / 1000000, 2) . " Go";
							}
							else {
								$memtotal = round(intval($free[0]) / 1000) . " Mo";
							}
						}
						if (isset($memtotal) && isset($freelibre)) {
							$Mem = 'Total : '.$memtotal.' - Libre : '.$freelibre;
						}
					}
					elseif (preg_match("#FreeBSD#", $uname)) {
						$free = explode(' ', $free);
						if (isset($free[0]) && intval($free[0]) != 0) {
							if (intval($fre[0]) != 0) {
								$mem_usage_pourc = round(intval($free[1]) / intval($free[0]) * 100);
							}
							else {
								$mem_usage_pourc = 0;
							}
						}
						if ((intval($free[1]) / 1000) > 1000) {
							$freelibre = round(intval($free[1]) / 1000000, 2) . " Go";
						}
						else{
							$freelibre = round(intval($free[1]) / 1000) . " Mo";
						}
						if (($free[0] / 1000) > 1000) {
							$memtotal = round(intval($free[0]) / 1000000, 2) . " Go";
						}
						else{
							$memtotal = round(intval($free[0]) / 1000) . " Mo";
						}
						$Mem = 'Total : '.$memtotal.' - Libre : '.$freelibre;
					}
				}
				else {
					$free = '';
				}

				if (isset($swap)) {
					$swap = explode(' ', $swap);

					if($this->getConfiguration('synology') == '1'){
						if(isset($swap[0])){
							if ((intval($swap[0]) / 1000) > 1000) {
								$swap[0] = round(intval($swap[0]) / 1000000, 2) . " Go";
							}
							else {
								$swap[0] = round(intval($swap[0]) / 1000) . " Mo";
							}
						}
						if(isset($swap[1])) {
							if ((intval($swap[1]) / 1000) > 1000) {
								$swap[1] = round(intval($swap[1]) / 1000000, 2) . " Go";
							}
							else {
								$swap[1] = round(intval($swap[1]) / 1000) . " Mo";
							}
						}
						if(isset($swap[2])){
							if ((intval($swap[2]) / 1000) > 1000) {
								$swap[2] = round(intval($swap[2]) / 1000000, 2) . " Go";
							}
							else {
								$swap[2] = round(intval($swap[2]) / 1000) . " Mo";
							}
						}
					}
					if(isset($swap[0]) && isset($swap[1]) && isset($swap[2])){
						$swap[0] = str_replace("B"," o", $swap[0]);
						$swap[1] = str_replace("B"," o", $swap[1]);
						$swap[2] = str_replace("B"," o", $swap[2]);
						$Memswap = 'Total : '.$swap[0].' - Utilisé : '.$swap[1].' - Libre : '.$swap[2];
					}
				}else {
					$swap = '';
				}

				if (isset($ReseauRXTX)) {
					$ReseauRXTX = explode(' ', $ReseauRXTX);
					if(isset($ReseauRXTX[0]) && isset($ReseauRXTX[1])){
						if ((intval($ReseauRXTX[1]) / 1000) > 1000000) {
							$ReseauTX = round(intval($ReseauRXTX[1]) / 1000000000, 2) . " Go";
						}
						elseif ((intval($ReseauRXTX[1]) / 1000) > 1000) {
							$ReseauTX = round(intval($ReseauRXTX[1]) / 1000000, 2) . " Mo";
						}
						else {
							$ReseauTX = round(intval($ReseauRXTX[1]) / 1000) . " Ko";
						}
						
						if ((intval($ReseauRXTX[0]) / 1000) > 1000000) {
							$ReseauRX = round(intval($ReseauRXTX[0]) / 1000000000, 2) . " Go";
						}
						elseif ((intval($ReseauRXTX[0]) / 1000) > 1000) {
							
							$ReseauRX = round(intval($ReseauRXTX[0]) / 1000000, 2) . " Mo";
						}else{
							$ReseauRX = round(intval($ReseauRXTX[0]) / 1000) . " Ko";
						}
						
						$ethernet0 = 'TX : '.$ReseauTX.' - RX : '.$ReseauRX;
					}
				}

				$hddtotal = '';
				$hddused = '';
				$hddpourcused = '';
				if (isset($hdd)) {
					$hdddata = explode(' ', $hdd);
					if(isset($hdddata[0]) && isset($hdddata[1]) && isset($hdddata[2])){
						$hddtotal = str_replace(array("K","M","G","T"),array(" Ko"," Mo"," Go"," To"), $hdddata[0]);
						$hddused = str_replace(array("K","M","G","T"),array(" Ko"," Mo"," Go"," To"), $hdddata[1]);
						$hddpourcused = preg_replace("/[^0-9.]/","",$hdddata[2]);
						$hddpourcused = trim($hddpourcused);
						if ($hddpourcused < '10'){
							$hddpourcused = '0'.$hddpourcused;
						}
					}
				}

				if (isset($ARMv)) {
					if ($ARMv == 'i686' || $ARMv == 'x86_64' || $ARMv == 'i386'){
						if ((floatval($cpufreq) / 1000) > 1) {
							$cpufreq = round(floatval($cpufreq) / 1000, 1, PHP_ROUND_HALF_UP) . " GHz";
						}
						else {
							$cpufreq = $cpufreq . " MHz";
						}
						$cpuTempCmd = $this->getCmd(null,'cpu_temp');
						if (is_object($cpuTempCmd) && $cpuTempCmd->getIsVisible() == 1) {
							if (floatval($cputemp0) != 0 & floatval($cputemp0) > 200){
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
						
						$cpuTempCmd = $this->getCmd(null,'cpu_temp');
						if (is_object($cpuTempCmd) && $cpuTempCmd->getIsVisible() == 1) {
							if (floatval($cputemp0) != 0 & floatval($cputemp0) > 200){
								$cputemp0 = floatval($cputemp0) / 1000;
								$cputemp0 = round(floatval($cputemp0), 1);
							}
						}
						if (floaval($cpufreq0) == 0){
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
							$cpuTempCmd = $this->getCmd(null,'cpu_temp');
							if (is_object($cpuTempCmd) && $cpuTempCmd->getIsVisible() == 1) {
								if (floatval($cputemp0) != 0 & floatval($cputemp0) > 200){
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
					if (floatval($cputemp0) != 0 & floatval($cputemp0) > 200){
						$cputemp0 = floatval($cputemp0) / 1000;
						$cputemp0 = round(floatval($cputemp0), 1);
					}
					$cpu = $nbcpu.' - '.$cpufreq0;
				}
				if (empty($cputemp0)) {$cputemp0 = '';}
				if (empty($perso_1)) {$perso_1 = '';}
				if (empty($perso_2)) {$perso_2 = '';}
				if (empty($Memswap)) {$Memswap = '';}
				if (empty($cnx_ssh)) {$cnx_ssh = '';}
				if (empty($swap_pourc_cmd)) {$swap_pourc_cmd = '';}
				if (empty($uname)) {$uname = 'Inconnu';}
				if (empty($Swappourcusage)) {$Swappourcusage = '';}
				if (empty($Mem)) {$Mem = '';}
				if (empty($mem_usage_pourc)) {$mem_usage_pourc = '';}

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
					'hddpourcused' => $hddpourcused,
					'cpu' => $cpu,
					'cpu_temp' => $cputemp0,
					'cnx_ssh' => $cnx_ssh,
					'Mem_swap' => $Memswap,
					'Mempourc' => $mem_usage_pourc,
					'Swappourc' => $Swappourcusage,
					'perso1' => $perso_1,
					'perso2' => $perso_2,
				);
				if($this->getConfiguration('synology') == '1' && $SynoV2Visible == 'OK' && $this->getConfiguration('synologyv2') == '1'){
					$dataresultv2 = array(
						'hddtotalv2' => $hddtotalv2,
						'hddusedv2' => $hddusedv2,
						'hddpourcusedv2' => $hddpourcusedv2,
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

				$hddpourcused = $this->getCmd(null,'hddpourcused');
				if(is_object($hddpourcused)){
					$hddpourcused->event($dataresult['hddpourcused']);
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
					$hddpourcusedv2 = $this->getCmd(null,'hddpourcusedv2');
					if(is_object($hddpourcusedv2)){
						$hddpourcusedv2->event($dataresultv2['hddpourcusedv2']);
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
				log::add('JeeMonitor', 'error', 'connexion SSH KO pour '.$equipement);
				$cnx_ssh = 'KO';
			}
			else {
				if (!$sshconnection->login($user, $pass)){
					log::add('JeeMonitor', 'error', 'Authentification SSH KO pour '.$equipement);
					$cnx_ssh = 'KO';
				}
				else {
					switch ($paramaction) {
						case "reboot":
							$paramaction =
							// $Rebootcmd = "sudo shutdown -r now >/dev/null & shutdown -r now >/dev/null";
							$Rebootcmd = "sudo reboot >/dev/null & reboot >/dev/null";
							$Reboot = $sshconnection->exec($Rebootcmd);
							log::add('JeeMonitor','debug','lancement commande deporte reboot ' . $this->getHumanName());
							break;
						case "poweroff":
							$paramaction =
							// $poweroffcmd = "sudo shutdown -P now >/dev/null & shutdown -P now >/dev/null";
							$poweroffcmd = "sudo poweroff >/dev/null & poweroff  >/dev/null";
							$poweroff = $sshconnection->exec($poweroffcmd);
							log::add('JeeMonitor','debug','lancement commande deporte poweroff' . $this->getHumanName());
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
						log::add('JeeMonitor','debug','lancement commande local reboot ' . $this->getHumanName());
						break;
					case "poweroff":
						$paramaction =
						exec('sudo shutdown -P now >/dev/null & shutdown -P now >/dev/null');
						log::add('JeeMonitor','debug','lancement commande local poweroff ' . $this->getHumanName());
					break;
				}
			}
			else {
				switch ($paramaction) {
					case "reboot":
						$paramaction =
						$cmdreboot = "sudo shutdown -r now >/dev/null & shutdown -r now >/dev/null";
						exec($cmdreboot);
						log::add('JeeMonitor','debug','lancement commande local reboot ' . $this->getHumanName());
						break;
					case "poweroff":
						$paramaction =
						exec('sudo shutdown -P now >/dev/null & shutdown -P now >/dev/null');
						log::add('JeeMonitor','debug','lancement commande local poweroff ' . $this->getHumanName());
						break;
				}
			}
		}
	}
}

class JeeMonitorCmd extends cmd {
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
