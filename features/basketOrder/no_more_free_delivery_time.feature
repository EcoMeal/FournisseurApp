Feature : L’application calcule les horaires libres

            En tant que système
            Je souhaite calculer les créneaux disponibles
            Afin de proposer un horaire libre au client
	
	Scenario Outline: Il n’y a plus d’horaires libres dans la plage horaire
            Given Le client a choisi une plage horaire
            And Il n’y a plus d’horaires libres dans cette plage
            When Je calcule les horaires libres
            Then Je ne renvoie aucun horaire
            And J’affiche un message indiquant qu’il n’y a plus d’horaires libres dans la plage choisie
            And Je propose au client de choisir une autre plage horaire
