Feature: Ajouter une catégorie de panier

        En tant que gérant
        Je veux pouvoir ajouter des catégories de paniers
        Afin de pouvoir mieux référencer mes paniers

        Règles: 
        - Les noms de catégories de panier sont uniques
        - L'image n'est pas obligatoire
        
        @basket_category
		@add_basket_category
        Scenario: Ajouter une catégorie de panier
                Given il n'y a aucune catégorie de panier
                When j'ajoute la catégorie de panier "Panier viande"
                Then il y a une catégorie de panier "Panier viande"
        
        @basket_category
        @add_basket_category
        Scenario: Ajouter une catégorie de panier sans image
        		Given il n'y a aucune catégorie de panier
        		When j'ajoute la catégorie de panier "Panier végétarien" sans ajouter d'image
        		Then il y a une catégorie de panier "Panier végétarien" avec l'image par défaut
        		
        @basket_category
        @add_basket_category
        Scenario: Ajouter une catégorie de panier avec un nom déjà existant
        		Given il existe une catégorie de panier "Panier classique"
        		When j'ajoute la catégorie de panier "Panier classique"
        		Then il y a une catégorie de panier "Panier classique"
        		And un message d'erreur s'affiche qui dit "Ajout impossible, la catégorie de panier existe déjà"
                