Feature: Ajouter une categorie

	En tant que gestionnaire
	Je veux pouvoir ajouter des categories
	Afin de pouvoir créer des produits avec une catégorie et permettre un meilleur référencement
	
	Règles :
	- Les noms des catégories sont uniques

        @add_category
        Scenario: Ajouter une categorie avec un nom deja existant
                  Given il existe la categorie "test"
                  When je crée une category "test" dans l'application
                  Then la categorie "test" n'est pas crée car elle existe deja

        @add_category
        Scenario: Ajouter une categorie avec une image
                  Given je cree la categorie "test" avec une image
                  When void
                  Then la categorie "test" s'affiche avec son image

        @add_category
        Scenario: Ajouter une categorie sans image
                  Given je cree la categorie "test" sans image
                  When void
                  Then la categorie "test" s'affiche avec l'image par défaut

        @add_category
	Scenario Outline: Ajouter une categorie
		Given il n'y a aucune categorie dans l'application
		When j'ajoute la categorie <nom_categorie> dans l'application
		Then il y a une categorie <nom_categorie> dans l'application

	Examples:
		| nom_categorie	|
		| "viande"	|
		| "legumes"	|
