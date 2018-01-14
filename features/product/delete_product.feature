Feature: Supprimer un produit

	En tant que gestionnaire
	Je veux pouvoir supprimer des produits
	Afin de pouvoir faire évoluer mes offres
	
	Règles :
	- Un produit ne peut pas être supprimé si il est utilisé pour la composition d'un panier.

    @product
    @delete_product
    Scenario: Supprimer un produit utilisé dans un panier
       	Given il existe un produit "test" utilisé dans un panier
        When j'essaie de supprimer le produit "test"
        Then l'application renvoie un message d'erreur "Suppression impossible, le produit est utilisé"

    @product
    @delete_product
	Scenario Outline: Supprimer un produit
		Given il existe le produit <nom_produit> dans l'application
		When je supprime le produit <nom_produit> dans l'application
		Then le produit <nom_produit> n'est plus affichée dans l'application

	Examples:
		| nom_produit   |
		| "poulet"	|
		| "haricot"	|
