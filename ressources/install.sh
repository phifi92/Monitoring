#!/bin/bash
touch /tmp/dependancy_monitoring_in_progress
echo 0 > /tmp/dependancy_monitoring_in_progress
versionPHP=$(php -i 2>/dev/null | grep 'PHP Version' -m 1 | awk '{ print $4 }' | cut -d'.' -f1)
[[ -z "$versionPHP" ]] && versionPHP=0
if [ "$versionPHP" -eq 5 ]
	then
		echo "*****************************************************************************************************"
		echo "*   Lancement de l'installation du module SSH pour PHP 5 / Launch install of module ssh2 for PHP 5  *"
		echo "*****************************************************************************************************"
		sudo apt-get clean
		echo 20 > /tmp/dependancy_monitoring_in_progress
		sudo apt-get update
		echo 80 > /tmp/dependancy_monitoring_in_progress
		sudo apt-get install -y php-phpseclib
		echo 100 > /tmp/dependancy_monitoring_in_progress
		echo "Tout est installé avec succès - Everything is successfully installed!"
		echo "*****************************************************************************************************"
		echo "*  Pour finaliser l'installation, redémarrer Jeedom / To complete the installation, restart Jeedom  *"
		echo "*****************************************************************************************************"

elif [ "$versionPHP" -ge 7 ]
	then
		echo "*****************************************************************************************************"
		echo "*   Lancement de l'installation du module SSH pour PHP 7 / Launch install of module ssh2 for PHP 7  *"
		echo "*****************************************************************************************************"
		sudo apt-get clean
		echo 20 > /tmp/dependancy_monitoring_in_progress
		sudo apt-get update
		echo 80 > /tmp/dependancy_monitoring_in_progress
		sudo apt-get install -y php-phpseclib
		echo 90 > /tmp/dependancy_monitoring_in_progress
		echo 100 > /tmp/dependancy_monitoring_in_progress
		echo "*****************************************************************************************************"
		echo "*  Pour finaliser l'installation, redémarrer Jeedom / To complete the installation, restart Jeedom  *"
		echo "*****************************************************************************************************"

else
		echo "Installation KO, PHP5 ou PHP7 n'est pas installé"
		echo 100 > /tmp/dependancy_monitoring_in_progress
fi
rm /tmp/dependancy_monitoring_in_progress
