Pour ajouter une entité : 

Pour générer une nouvelle entité sur doctrine (database) :

php bin/console doctrine:generate:entity

Donner un nom à l'entité :

> NomDuBundle:Entite

Renseigner les champs (l'id est généré automatiquement)

Pour mettre à jour la BDD après avoir crée des entités :

php bin/console doctrine:schema:update --force

Pour créer un contrôleur : 

php bin/console generate:controller