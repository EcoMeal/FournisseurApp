Feature: Valider une commande
        
        En tant qu'étudiant
        je souhaite valider ma commande
        afin de pouvoir réserver mon panier repas
        
        @order
        Scenario: Valider ma commande
        	Given j'ai choisi mon horaire de livraison
        	And j'ai choisi un panier disponible
        	When je valide ma commande
        	Then le système me retourne un numéro de commande
        	
		        
        