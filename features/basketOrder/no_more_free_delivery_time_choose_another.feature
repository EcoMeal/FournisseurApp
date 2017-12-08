Feature : L’application calcule les horaires libres

            En tant que système
            Je souhaite calculer les créneaux disponibles
            Afin de proposer une horaires libres au client
	
	Scenario Outline: Il n’y a plus d’horaires disponibles dans la plage et le client souhaite choisir une autre plage
            Given Il n’y a plus d’horaires disponibles dans la plage choisie par le client
            And Je propose au client de choisir une autre plage horaire
            When Le client clique sur le bouton “Choisir une autre plage horaire”
            Then Je le renvoie sur la page des choix d’une plage horaire

