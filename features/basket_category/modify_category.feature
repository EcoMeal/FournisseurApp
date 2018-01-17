Feature: Modifier le nom d’une catégorie de panier
                        En tant que gestionnaire, 
                        je souhaite modifier le nom d'une catégorie de panier 
                        afin de modifier le nom d'une catégorie de panier
                       
                @basket_category
            @update_basket_category
            Scenario: Modifier le nom d’une catégorie de panier
                        Given je cree une catégorie de panier "Vegetarien"
                        When je renomme la catégorie de panier "Vegetarien" en "Carnivore"
                        Then il n'y a plus de catégorie de panier "Vegetarien"
                        And il y a une catégorie de panier "Carnivore"
                       

                @basket_category
            @update_basket_category
            Scenario: Remplacer le nom d’une catégorie de panier par un nom déjà pris
                        Given je cree une catégorie de panier "Vegetarien"
                        And je cree une catégorie de panier "Carnivore"
                        When je renomme la catégorie de panier "Vegetarien" en "Carnivore"
                        Then il y a une catégorie de panier "Vegetarien"
                        And il y a une catégorie de panier "Carnivore"
                        And un message d’erreur s’affiche qui dit "Une catégorie utilise déjà ce nom."
