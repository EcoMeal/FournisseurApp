Feature: Ajouter un produit

	En tant que gestionnaire
	Je veux pouvoir ajouter des produits
	Afin de pouvoir diversifier mon stock
	
	Règles :
	- Les noms des produits sont uniques
        
    @product
    Scenario: Ajouter un produit avec un nom deja existant
              Given il existe le produit "test"
              When je crée un produit "test" dans l'application
              Then le produit "test" n'est pas crée car il existe deja
    @product
    Scenario: Ajouter un produit avec une image
              Given je cree le produit "test" avec une image
              When void
              Then le produit "test" s'affiche avec son image
    @product          
    Scenario: Ajouter un produit sans image
              Given je cree le produit "test" sans image
              When void
              Then le produit "test" s'affiche avec l'image par défaut
    @product          
	Scenario Outline: Ajouter un produit
		Given il n'y a aucun produit dans l'application
        And il existe une catégorie "test" dans l'application.
		When j'ajoute le produit <nom_produit> dans l'application
		Then il y a un produit <nom_produit> dans l'application

		Examples:
		| nom_produit   |
		| "poulet"		|
		| "haricot"		|
		