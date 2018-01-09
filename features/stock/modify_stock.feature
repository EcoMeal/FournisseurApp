Feature: Modifier la quantité disponible d'un produit

	En tant que fournisseur 
	je souhaite modifier la quantité disponible d'un produit 
	afin de mettre à jour rapidement les quantités de produit que je peux donner

	@stock
	Scenario: mettre à jour la quantité d'un produit
		Given la quantité de "Poulet" est 0
		When je met à jour la quantité de "Poulet" à 42
		Then la quantité de "Poulet" est de 42