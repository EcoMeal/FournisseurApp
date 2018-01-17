Feature: Modifier la quantité disponible d'un produit

	En tant que fournisseur 
	je souhaite modifier la quantité disponible d'un produit 
	afin de mettre à jour rapidement les quantités de produit que je peux donner

	@stock
	Scenario: mettre à jour la quantité d'un produit
		Given la quantité de "Poulet" est "0"
		When je met à jour la quantité de "Poulet" à "42"
		Then la quantité de "Poulet" est de "42"

        @stock
	Scenario: mettre à jour la quantité d'un produit avec une valeur invalide
		Given la quantité de "Poulet" est "0"
		When je met à jour la quantité de "Poulet" à "-1"
		Then la quantité de "Poulet" est de "0"

        @stock
	Scenario: mettre à jour la quantité de plusieurs produits
		Given la quantité de "Poulet" est "0"
                And la quantité de "Salade" est "0"
		When je met à jour la quantité de "Poulet" à "42"
                And je met à jour la quantité de "Salade" à "13"
		Then la quantité de "Poulet" est de "42"
                And la quantité de "Salade" est de "13"