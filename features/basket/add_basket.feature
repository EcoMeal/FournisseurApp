Feature: Lister les paniers disponibles 
        
        En tant que gestionnaire,
        je souhaite créer un panier
        afin de proposer des bons repas à mes utilisateurs.

	Règles :
	- Les noms des paniers sont uniques
        
        @basket
        Scenario: Ajouter un panier qui existe déjà
            Given j’ai un panier "Panier 1" disponible
            When j’ajoute un nouveau panier "Panier 1"
            Then j'obtiens une erreur "Le panier existe déjà"
 

        @basket
        Scenario Outline: Ajouter un panier
                Given il n’y a aucun panier dans l'application
                When je crée un panier <nom_panier> dans l'application
                Then le panier <nom_panier> est affiché 

        Examples:

        |	nom_panier	|
        |	"vegan"		|
        |	"classic"	|
