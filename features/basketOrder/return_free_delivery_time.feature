Feature : L’application calcule les horaires libres

            En tant que système
            Je souhaite calculer les créneaux disponibles
            Afin de proposer un horaire libre au client
	
	Scenario Outline: Renvoyer un horaire libre au client
            Given Le client a choisi une plage horaire
            And Il y a encore des horaires libres disponibles dans cette plage
            When Je calcule les horaires libres
            Then Je renvoie la première horaire libre dans cette plage
