Feature: Lister les paniers disponibles 
        
        En tant que gestionnaire,
        je souhaite créer un panier
        afin de proposer des bons repas à mes utilisateurs.

		Règles :
			- Les noms des paniers sont uniques
        
        @basket
        @add_basket
        Scenario: Ajouter un panier qui existe déjà
            Given j’ai un panier "Panier 1" disponible
            When j’ajoute un nouveau panier "Panier 1"
            Then un message d'erreur s'affiche qui dit "Le panier existe déjà"
 		  
        @basket
        @add_basket
        Scenario: Ajouter un panier
                Given il n’y a aucun panier dans l'application
                When je crée un panier "Panier 1" dans l'application
                Then le panier "Panier 1" est affiché 
                
