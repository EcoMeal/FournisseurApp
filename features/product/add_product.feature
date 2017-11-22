Feature: Ajouter un produit

	En tant que gestionnaire
	Je veux pouvoir ajouter des produits
	Afin de pouvoir diversifier mon stock
	
	Règles :
	- Les noms des produits sont uniques

	Scenario Outline: Ajouter un produit
		Given il n'y a aucun produit dans l'application
                And il existe une catégorie dans l'application.
		When j'ajoute le produit <nom_produit> dans l'application
		Then il y a un produit <nom_produit> dans l'application

	Examples:
		| nom_produit   |
		| "poulet"	|
		| "haricot"	|
