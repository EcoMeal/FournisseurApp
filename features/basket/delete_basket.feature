Feature: Supprimer un panier

		En tant que gestionnaire
		je souhaite supprimer un panier existant
		afin de ne plus le proposer sur l'application
		
		Règles : 
		- On ne peut pas supprimer un panier utilisé dans une commande en cours
		
		@basket
		@delete_basket
		Scenario: Supprimer un panier
			Given il existe un panier "Panier végétarien"
			When je supprime le panier "Panier végétarien"
			Then il n'y a plus de panier "Panier végétarien"
			
		@basket
		@delete_basket
		Scenario: Supprimer un panier utilisé dans une commande en cours
			Given il existe un panier "Panier végan"
			And il existe une commande en cours avec le panier "Panier végan"
			When je supprime le panier "Panier végan"
			Then l'application renvoie un message d'erreur "Ce panier a été commandé. Veuillez attendre la fin de la commande avant de le supprimer"
        	And le panier "Panier végan" est affiché