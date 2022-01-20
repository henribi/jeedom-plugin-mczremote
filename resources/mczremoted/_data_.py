#coding: utf-8

'''
Tables des correspondances

	Le rang 0 correspond à la position de l'information dans la trame MAESTRO
	Le rang 1 correspond a l'intitulé publié sur le broker
	Le rang 2 (optionnel) permet de remplacer le code de la trame par une information texte correspondante

'''
RecuperoInfo=[
	[1,"Etat du poele",[
						[0, "Eteint"],
						[1, "Controle du poele froid / chaud"],
						[2, "Clean Froid"],
						[3, "Load Froid"],
						[4, "Start 1 Froid"],
						[5, "Start 2 Froid"],
						[6, "Clean Chaud"],
						[7, "Load Chaud"],
						[8, "Start 1 chaud"],
						[9, "Start 2 chaud"],
						[10, "Stabilisation"],
						[11, "Puissance 1"],
						[12, "Puissance 2"],
						[13, "Puissance 3"],
						[14, "Puissance 4"],
						[15, "Puissance 5"],
						[30, "Mode diagnostique"],
						[31, "Marche"],
						[40, "Extinction"],
						[41, "Refroidissement en cours"],
						[42, "Nettoyage basse p."],
						[43, "Nettoyage haute p."],
						[44, "Deblocage vis sans fin"],
						[45, "AUTO ECO"],
						[46, "Standby"],
						[48, "Diagnostic"],
						[49, "CHARG. VIS SANS FIN"],
						[50, "Erreur A01 - Allumage rate"],
						[51, "Erreur A02 - Pas de flamme"],
						[52, "Erreur A03 - Surchauffe du reservoir"],
						[53, "Erreur A04 - Temperature des fumees trop haute"],
						[54, "Erreur A05 - Obstruction conduit - Vent"],
						[55, "Erreur A06 - Mauvais tirage"],
						[56, "Erreur A09 - Defaillance sonde de fumees"],
						[57, "Erreur A11 - Defaillance motoreducteur"],
						[58, "Erreur A13 - Temperature carte mere trop haute"],
						[59, "Erreur A14 - Defaut Active"],
						[60, "Erreur A18 - Temperature d'eau trop haute"],
						[61, "Erreur A19 - Defaut sonde temperature eau"],
						[62, "Erreur A20 - Defaut sonde auxiliaire"],
						[63, "Erreur A21 - Alarme pressostat"],
						[64, "Erreur A22 - Defaut sonde ambiante"],
						[65, "Erreur A23 - Defaut fermeture brasero"],
						[66, "Erreur A12 - Panne controleur motoreducteur"],
						[67, "Erreur A17 - Bourrage vis sans fin"],
						[69, "Attente Alarmes securite"],
						]],
	[2,"Etat du ventilateur ambiance",[
										[0, "Desactive"],
										[1, "Niveau 1"],
										[2, "Niveau 2"],
										[3, "Niveau 3"],
										[4, "Niveau 4"],
										[5, "Niveau 5"],
										[6, "Automatique"],
										]],
	[3,"Etat du ventilateur canalise 1",[
										[0, "Desactive"],
										[1, "Niveau 1"],
										[2, "Niveau 2"],
										[3, "Niveau 3"],
										[4, "Niveau 4"],
										[5, "Niveau 5"],
										[6, "Automatique"],
										]],
	[4,"Etat du ventilateur canalise 2",[
										[0, "Desactive"],
										[1, "Niveau 1"],
										[2, "Niveau 2"],
										[3, "Niveau 3"],
										[4, "Niveau 4"],
										[5, "Niveau 5"],
										[6, "Automatique"],
										]],
	[5,"Temperature des fumees"],
	[6,"Temperature ambiante"],
	[7,"Puffer Temperature"], # !=255 == Hydro
	[8,"Temperature chaudiere"],
	[9,"Temperature NTC3"], # !=255 == Hydro
	[10,"Etat de la bougie",[
					[0, "Ok"],
					[1, "Usee"],
					]],
	[11,"ACTIVE - Set"],
	[12,"RPM - Ventilateur fummees"],
	[13,"RPM - Vis sans fin - SET"],
	[14,"RPM - Vis sans fin - LIVE"],
	[17,"Brazero",[
					[0, "OK"],
					[101, "Ouverture brazero"],
					[100, "Fermeture brazero"],
					]], # !==Matic
	[18,"Profil",[
					[0, "Manuel"],
					[1, "Dynamic"],
					[2, "Overnight"],
					[3, "Comfort"],
					[4, "Power 110%"],
					[10, "Mode Adaptatif"],
					]],
	[20,"Etat du mode Active",[
					[0, "Off"],
					[1, "On"],
					]],  #0: Désactivé, 1: Activé
	[21,"ACTIVE - Live"],
	[22,"Mode de regulation",[
							[0, "Manuelle"],
							[1, "Dynamique"],
							]],
	[23,"Mode ECO",[
					[0, "Off"],
					[1, "On"],
					]],
	[24,"Silence",[
					[0, "Off"],
					[1, "On"],
					]],
	[25,"Mode Chronotermostato",[
					[0, "Off"],
					[1, "On"],
					]],
	[26,"TEMP - Consigne"],
	[27,"TEMP - Boiler"],
	[28,"TEMP - Carte mere"],
	[29,"Puissance Active",[
							[11, "Puissance 1"],
							[12, "Puissance 2"],
							[13, "Puissance 3"],
							[14, "Puissance 4"],
							[15, "Puissance 5"],
							]],
	[32,"Heure du poele (0-23)"],
	[33,"Minutes du poele (0-29)"],
	[34,"Jour du poele (1-31)"],
	[35,"Mois du poele (1-12)"],
	[36,"Annee du poele"],
	[37,"Heures de fonctionnement total (s)"],
	[38,"Heures de fonctionnement en puissance 1 (s)"],
	[39,"Heures de fonctionnement en puissance 2 (s)"],
	[40,"Heures de fonctionnement en puissance 3 (s)"],
	[41,"Heures de fonctionnement en puissance 4 (s)"],
	[42,"Heures de fonctionnement en puissance 5 (s)"],
	[43,"Heures avant entretien"],
	[44,"Minutes avant extinction"],
	[45,"Nombre d'allumages"],
	[47,"Sonde Pellets",[
						[0, "Sonde pas active"],
						[10, "Niveau suffisant"],
						[11, "Niveau presque vide"],
						]],
	[48,"Effet sonore",[
					[0, "Off"],
					[1, "On"],
					]],
	[49,"Etat effets sonores",[
					[0, "Off"],
					[1, "On"],
					]],
	[50,"Sleep",[
					[0, "Off"],
					[1, "On"],
					]],
	[51,"Mode",[
				[0, "Hiver"],
				[1, "Ete"],
				]],
	[52,"Sonde wifi temperature 1"],
	[53,"Sonde wifi temperature 2"],
	[54,"Sonde wifi temperature 3"],
	[55,"Inconnu"],
	[56,"Set Puffer"],
	[57,"Set Boiler"],
	[58,"Set Health"], # !==Hydro
	[59,"Temperature retour"],
	[60,"Antigel",[
					[0, "Off"],
					[1, "On"],
					]],
	]
