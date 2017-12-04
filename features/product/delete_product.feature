Feature: Supprimer un produit

	En tant que gestionnaire
	Je veux pouvoir supprimer des produits
	Afin de pouvoir faire évoluer mes offres
	
	Règles :
	- Un produit ne peut pas être supprimé si il est utilisé pour la composition d'un panier.

	Scenario Outline: Supprimer un produit
		Given il existe le produit <nom_produit> dans l'application
		When je supprime le produit <nom_produit> dans l'application
		Then le produit <nom_produit> n'est plus affichée dans l'application

	Examples:
		| nom_produit   |
		| "poulet"	|
		| "haricot"	|

