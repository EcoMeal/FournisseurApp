Pour ajouter une entit� : 

Pour g�n�rer une nouvelle entit� sur doctrine (database) :

php bin/console doctrine:generate:entity

Donner un nom � l'entit� :

> NomDuBundle:Entite

Renseigner les champs (l'id est g�n�r� automatiquement)

Pour mettre � jour la BDD apr�s avoir cr�e des entit�s :

php bin/console doctrine:schema:update --force

Pour cr�er un contr�leur : 

php bin/console generate:controller