Feature: Lister les paniers disponibles

        En tant que client,
        je souhaite consulter les différents paniers
        afin de connaître les produits disponibles.

        Scénario: Il n‘y a aucun panier disponible
        Given il n’y a aucun panier disponible
        When je veux afficher la liste des paniers
        Then aucun panier n’est affiché 


