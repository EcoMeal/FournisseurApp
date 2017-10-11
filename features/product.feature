Feature: Ajouter un produit

	En tant que gestionnaire
	Je veux pouvoir ajouter des produits
	Afin de lister les produits que nous acceptons de recevoir
	
	Règles :
	- Les noms des produits sont uniques

	Scenario Outline: Ajouter un produit
		Given un produit <nom_produit>
		And il n'y a aucun produit dans l'application
		When j'ajoute le produit <nom_produit> dans l'application
		Then il y a un produit <nom_produit> dans l'application

	Examples:
		| nom_produit 	|
		| poulet		|
		| oeuf			|
