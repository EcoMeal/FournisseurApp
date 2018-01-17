Feature: Modifier le nom d’une catégorie
                        En tant que gestionnaire, 
                        je souhaite modifier le nom d'une catégorie d'aliments 
                        afin de modifier le nom d'une catégorie d'aliments
                       
                @category
            @update_category
            Scenario: Modifier le nom d’une catégorie
                        Given il y a une catégorie d’aliment "Légumes"
                        When je renomme la catégorie d’aliment "Légumes" en "Fruits & Légumes"
                        Then il y a une catégorie d’aliment "Fruits & Légumes"
                        And il n’y pas de catégorie d’aliment "Légumes"

                @category
            @update_category
            Scenario: Remplacer le nom d’une catégorie par un nom déjà pris
                        Given il y a une catégorie d’aliment "Légumes"
                        And il y a une catégorie d’aliment "Fruits"
                        When je renomme la catégorie d’aliment "Légumes" en "Fruits"
                        Then il y a une catégorie d’aliment "Légumes"
                        And il y a une catégorie d’aliment "Fruits"
                        And un message d’erreur s’affiche qui dit "Une catégorie utilise déjà ce nom."
