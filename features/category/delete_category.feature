Feature: Supprimer une categorie

	En tant que gestionnaire
	Je veux pouvoir supprimer des categories
	Afin de pouvoir faire évoluer mes offres
	
	Règles :
	- Les produits associés à la catégorie supprimée sont aussi supprimés
          // Règle à rajouter
          And tout les produits de la categorie <nom_categorie> ne sont plus affichés dans l'application

	Scenario Outline: Supprimer une categorie
		Given il existe la categorie <nom_categorie> dans l'application
		When je supprime la categorie <nom_categorie> dans l'application
		Then la categorie <nom_categorie> n'est plus affichée dans l'application


	Examples:
		| nom_categorie	|
		| "viande"	|
		| "legumes"	|
