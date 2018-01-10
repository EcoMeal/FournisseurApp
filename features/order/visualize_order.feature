Feature: Visualiser une commande
        
        En tant que marchand
        je souhaite visualiser les commandes
        afin de pouvoir les préparer
        
        @order
        Scenario: Visualiser une commande
        	Given il existe une commande
        	When je vais sur la page des commandes
        	Then le système m'affiche la commande
        	
		        
        