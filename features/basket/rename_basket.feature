Feature: Modifier le nom d’un panier
                        En tant que gestionnaire, 
                        je souhaite modifier le nom d'un panier
                        afin de modifier le nom de ce panier
                       
                @basket
            @update_basket
            Scenario: Modifier le nom d’un basket
                        Given il y a un panier "Vegan" existant dans la liste des paniers
                        When je renomme le panier "Vegan" en "Végétarien"
                        Then il y a une panier "Végétarien"
                        And il n’y pas de panier "Vegan"

                @basket
            @update_basket
            Scenario: Remplacer le nom d’un panier par un nom déjà pris
                        Given il y a un panier "Vegan" existant dans la liste des paniers
                        And il y a un panier "Végétarien" existant dans la liste des paniers
                        When je renomme le panier "Vegan" en "Végétarien"
                        Then il y a un panier "Vegan"
                        And il y a un panier "Végétarien"
                        And un message d’erreur s’affiche qui dit "Un panier utilise déjà ce nom."
