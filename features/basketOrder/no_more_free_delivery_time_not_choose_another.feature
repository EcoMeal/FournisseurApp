Feature : L’application calcule les horaires libres

            En tant que système
            Je souhaite calculer les créneaux disponibles
            Afin de proposer un horaire libre au client
	
	Scenario Outline: Il n’y a plus d’horaires disponibles dans la plage et le client ne souhaite pas choisir une autre plage
            Given Il n’y a plus d’horaires disponibles dans la plage choisie par le client
            And Je propose au client de choisir une autre plage horaire
            When Le client clique sur le bouton “Annuler”
            Then Je le renvoie sur la page d’accueil de l’application
