Feature: Supprimer une categorie

	En tant que gestionnaire
	Je veux pouvoir supprimer des categories
	Afin de pouvoir faire évoluer mes offres
	
	Règles :
		- Les produits associés à la catégorie supprimée sont aussi supprimés
        // Règle à rajouter
        And tout les produits de la categorie <nom_categorie> ne sont plus affichés dans l'application
        
        @category
        @delete_category
        Scenario: Supprimer une categorie affectée à un produit
                Given il existe une categorie "testCategory" utilisé par un produit
                When j'essaie de supprimer la categorie "testCategory"
                Then l'application renvoie un message d'erreur "Suppression impossible, la catégorie est utilisée"

		@category
        @delete_category
		Scenario: Supprimer une categorie
			Given il existe la categorie "Légumes" dans l'application
			When je supprime la categorie "Légumes" dans l'application
			Then la categorie "Légumes" n'est plus affichée dans l'application
			