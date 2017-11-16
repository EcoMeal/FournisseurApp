Feature: Ajouter une categorie

	En tant que gestionnaire
	Je veux pouvoir ajouter des categories
	Afin de pouvoir créer des produits avec une catégorie et permettre un meilleur référencement
	
	Règles :
	- Les noms des catégories sont uniques

	Scenario Outline: Ajouter une categorie
		Given il n'y a aucune categorie dans l'application
		When j'ajoute la categorie <nom_categorie> dans l'application
		Then il y a une categorie <nom_categorie> dans l'application

	Examples:
		| nom_categorie	|
		| "viande"	|
		| "legumes"	|
