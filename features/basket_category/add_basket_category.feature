Feature : Ajouter une catégorie de panier

    En tant que gérant
    Je veux pouvoir ajouter des catégories de paniers
    Afin de pouvoir mieux référencer mes paniers

    Règle : 
    - Les noms de catégories de panier sont uniques

    Scenario Outline : Ajouter une catégorie de panier
            Given il n'y a aucune catégorie de panier dans l'application
            When j'ajoute la catégorie <nom_categorie_panier> dans l'application
            Then il y a une catégorie <nom_categorie_panier> dans l'application

        Examples:
            | nom_categorie     |
            | "panier vegan"    |
            | "panier viande"   |
