Feature : L’application calcule les horaires libres
	En tant que système
	Je souhaite calculer les créneaux disponibles
	Afin de proposer une horaires libres au client
	
	Scenario : Renvoyer une horaires libre au client
		Given le client a choisi une plage horaire
		And il y a encore des horaires libres disponibles dans cette plage
		When je calcule les horaires libres
		Then je renvoie la première horaire libre dans cette plage

	Scenario : Il n’y a plus d’horaires libres dans la plage horaire
		Given le client a choisi une plage horaire
		And il n’y a plus d’horaires libres dans cette plage
		When je calcule les horaires libres
		Then je ne renvoie aucune horaire
		And j’affiche un message indiquant qu’il n’y a plus d’horaires libres dans la plage choisie
		And je propose au client de choisir une autre plage horaire

	Scenario : Il n’y a plus d’horaires disponibles dans la plage et le client souhaite en choisir une autre
		Given il n’y a plus d’horaires disponibles dans la plage choisie par le client
		And je propose au client de choisir une autre plage horaire
		When le client clique sur le bouton “Choisir une autre plage horaire”
		Then je le renvoie sur la page des choix d’une plage horaire

	Scenario : Il n’y a plus d’horaires disponibles dans la plage et le client ne souhaite pas en choisir une autre
		Given il n’y a plus d’horaires disponibles dans la plage choisie par le client
		And je propose au client de choisir une autre plage horaire
		When le client clique sur le bouton “Annuler”
		Then je le renvoie sur la page d’accueil de l’application

