Feature: Lister les paniers disponibles 
        
        En tant que gestionnaire,
        je souhaite créer un panier
        afin de proposer des bons repas à mes utilisateurs.

		Règles :
			- Les noms des paniers sont uniques
        
        @basket
        @add_basket
        Scenario: Ajouter un panier qui existe déjà
            Given il existe un panier "Panier 1"
            When j'ajoute le panier "Panier 1"
            Then un message d'erreur s'affiche qui dit "Le panier existe déjà"
 		  
        @basket
        @add_basket
        Scenario: Ajouter un panier
            Given il n’y a aucun panier dans l'application
            When j'ajoute le panier "Panier 1"
            Then le panier "Panier 1" est affiché 
                
