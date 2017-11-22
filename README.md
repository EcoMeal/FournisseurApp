Application EcoMeal - Projet Master Info IAGL 2017-2018

Lucas Moura de Oliveira
Jean-Hugo Ouwe Missi Oukem
Eliott Bricout

## Setup linux

Si vous rencontrez des problèmes de d'installation sur linux essayez d'installer les paquets suivants  
- sudo apt-get install php-mbstring
- sudo apt-get install php-xml

Pour faire une nouvelle installation avec sqllite (pour les non casse couilles)  
	- php composer.phar update  
	- mkdir var/data  
	- Aller modifier app/config/config.yml  
		-- A la ligne 45 mettre pdo_sqlite  
		-- Décommenter database_path dans parameters.yml.dist  
		-- Décommenter path: '%database_path%' à la ligne 57  
	- php bin/console doctrine:database:create  
	- php bin/console doctrine:schema:update --force  

Pour lancer le serveur :   
	- php bin/console server:start  

