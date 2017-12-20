Feature: Ajouter une catégorie de panier

        En tant que gérant
        Je veux pouvoir ajouter des catégories de paniers
        Afin de pouvoir mieux référencer mes paniers

        Règles: 
        - Les noms de catégories de panier sont uniques

		@basket_category
        Scenario Outline: Ajouter une catégorie de panier
                Given il n'y a aucune catégorie de panier dans l'application
                When j'ajoute la catégorie de panier <nom_categorie_panier> dans l'application
                Then il y a une catégorie de panier <nom_categorie_panier> dans l'application

            Examples:
                | nom_categorie_panier     |
                | "panier vegan"           |
                | "panier viande"          |
