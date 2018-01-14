Feature: Supprimer une catégorie de panier

		En tant que fournisseur
		je souhaite supprimer une catégorie de panier
		afin d'alléger notre liste en supprimant les catégories obsolètes
		
		Règles:
			- On ne peut pas supprimer les catégories de panier utilisées
			
		@basket_category
		@delete_basket_category
		Scenario: Supprimer une catégorie de panier non utilisée
			Given il existe une catégorie de panier "Panier végétarien"
			When je supprime la catégorie de panier "Panier végétarien"
			Then il n'y a plus de catégorie de panier "Panier végétarien"
			And l'application renvoie un message d'information "Catégorie supprimée"
			
		@basket_category
		@delete_basket_category
		Scenario: Supprimer une catégorie de panier utilisée
			Given il existe une catégorie de panier "Panier végétarien"
			And il y a un panier "Panier végétarien 1" dans la catégorie "Panier végétarien"
			When je supprime la catégorie de panier "Panier végétarien"
			Then il y a une catégorie de panier "Panier végétarien"
			And l'application renvoie un message d'erreur "Suppression impossible, la catégorie de panier est utilisée"