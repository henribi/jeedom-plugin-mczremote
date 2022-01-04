# Plugin mcztherm

Ce plugin permet de créer et gérer des thermostats simples pour piloter le chauffage d'un poêle à pellets de la gamme MCZ Maestro.

Ses principales fonctionnalités sont:
   -  module le niveau de chauffe en fonction de la différence entre la température de consigne et la température ambiante
   -  gére deux mode de chauffe: Jour et Nuit
   -  dispose d'un mode **hystérésis**
   -  utilise le module python pour dialoguer avec un poêle MCZ Maestro via MQTT
   -  conçu pour permettre un démarrage différé dans une seconde résidence
   -  permet de synchroniser l'heure du poele avec celle de Jeedom


Le mode **hystérésis** permet de gérer l’allumage et l’extinction du chauffage en fonction de la température intérieure, par rapport à un seuil correspondant à la consigne. L’hystérésis permet d’éviter des séquences arrêts, allumages trop fréquentes lorsque la température est autour la consigne.

# Configuration

Ce plugin est destiné à la création de thermostats dans Jeedom.


## La configuration en quelques clics


![Aspect sur le Dashboard](../images/dashboard.png)

Sur le dashboard, vous avez un bouton pour activer ou stopper le thermostat, un curseur pour spécifier la température de consigne.

Ce bouton vous permet de déroger aux consignes spécifiées dans les modes jour et nuit jusqu'au prochain changement de mode.

Le bouton Activation vous permet de prévoir une activation automatique du thermostat à l'heure indiquée en dessous.

Ceci permet entre autre d'activer, en pleine nuit, le thermostat et le poêle de la seconde résidence afin d'avoir une température agréable à votre arrivée le lendemain.

Ce bouton Activation est automatiquement désactivé après utilisation.

## La création d’un thermostat en détail

Pour créer un nouveau thermostat, rendez-vous sur la page de configuration en déroulant le menu Plugins/Confort et sélectionnez mcztherm. Cliquez sur le bouton *Ajouter* situé en haut à gauche et renseignez le nom souhaité pour votre thermostat.

### La configration générale

![Configuration générale](../images/mcz_config_generale.png)

Dans cette page de configuration, outre les informations habituelles pour un équipement, vous avez la possibilité d'activer ou pas un mode. De lui spécifier sa température dee consigne ainsi que son heure d'activation.

C'est aussi dans cette page que vous configurez la sonde intérieure.

Si vous désirez être alerté en cas d'erreur du poêle, vous pouvez spécifier la commande message à utiliser.

### Les consignes

![Configuration des consignes](../images/consignes.png)

Cette page permet de configurer les consignes de fonctionnement.

Vous avez l'offset des seuils d'activation des différents niveau de puissance ou d'arrêt, la définition de l'hystérèse ainsi que le délai minimum entre l'ordre d'arrêt et un nouvel allumage du poêle.

Pour chaque consigne, vous devez définir les action qui doivent être exécutées.

Par exemple, pour la consigne Puissance 1, j'ai défini chez moi que les actions suivantes doivent être exécutées:
   -  Profil manuel
   -  Mode ECO off
   -  niveau ventilateur ambiance à 1
   -  niveau ventilateur canalisé à 1
   -  Puissance niveau 1

> **Tip**
>
> Il faut définir au niveau de MQTT des commandes utnitaires.  C'est à dire une commande pour un fonction bien précise. 
> 
> Il faudra donc créer une commande par niveau de puissance, une commande par niveau du ventilateur ambaince, une commande par niveau du ventilateur canalisé, ...
>

La valeur d'hystérèse est divisée en deux.  Une demi est ajoutée à la température du seuil en phase de température montante.  En phase de température descendante, un demi est soustrait du seuil.

### Les infos

![Infos du poêle](../images/infos_poele.png)

Dans cet écran, vous avez les commandes à spécifier pour obtenir les informations du poêle et réagir en conséquence.

L'information Valeur état off est le texte donnant l'état du poêle à l'état off. 

La zone Attente sur un état spécifie les textes renvoyés par le poêle pour lesquels il faut attendre. Il est inutile d'envoyer des commandes au poêle durant ces phases.

La zone Consigne de température permet de connaître la consigne de température connue du poêle.

Les deux dernières zones permmetent l'envoi d'une notification si le niveau de pellets est presque vide.  
Ne pas remplir la commande s'il n'y a pas de sonde de pellets installée. 

### Les commandes 

![Commandes du poêle](../images/commandes_poele.png)

Dans cet écran, vous allez spécifier les commandes à utiliser pour allumer ou éteindre le poêle.

Il y également la commande pour indiquer la température de consigne au poêle. Pour fonctionner, cette commande nécessite une configuration particulière.  
Il faut définir dans MQTT une commande action, defaut, topic: SUBmcz avec comme valeur 42,*commande*  
Vous insérez *commande* avec recherche équipement.  C'est la commande T_demandee de l'équipement mcztherm

La dernière commande permet d'effectuer la mise à jour de la date et l'heure du poêle ainsi que l'heure d'exécution.  
La logique est semblable à celle pour la température. L'information pour la synchronisation de l'heure est sauvée dans la commande info "ordrepoele" de l'équipement mcztherm.  
Il faut définir dans MQTT une commande action, defaut, topic: SUBmcz avec comme valeur la commande "ordrepoele" de l'équipement mcztherm.

> **Attention**
>
> Cette commande nécessite une version modifiée du script python maestro.py.  Le script doit traiter la commande 9001 pour envoyer la commande C|SalvaDataOra|DDMMYYYYHHmm
>


## PRINCIPE DE FONCTIONNEMENT
L'évaluation des opérations à exécuter s'effectue toutes les 5 minutes via le cron5.

Lors de chaque cycle, la température ambiante est lue de la sonde référencée.  
On vérifie que le poêle n'est pas dans un état d'erreur. Si oui, on le notifie.  
Le mode jour/nuit est évalué.    

> **Tip**
>
>Pour l'explication, j'utilise les valeurs par défaut du plugin. (T consigne: 21°C, Hystérèse: 1°C, Arrêt seuil:0°C, Puissance 1: -1°C, Puissance 2: -2°C, Puissance 3: -3°C, Puissance 4: -5°C).  
>

Si la température ambiante est supérieure au seuil de l'arrêt: 21,5°C (21 - 0 + hystérèse/2), les consignes de l'arrêt sont exécutées.  
Si la température ambiante est supérieure au seuil de la Puissance 1: 20,5°C (21 - 1 + hystérèse/2), les consignes de la puissance 1 sont exécutées.  
Si la température ambiante est supérieure au seuil de la Puissance 2: 19,5°C (21 - 2 + hystérèse/2), les consignes de la puissance 2 sont exécutées.  
Si la température ambiante est supérieure au seuil de la Puissance 3: 18,5°C (21 - 3 + hystérèse/2), les consignes de la puissance 3 sont exécutées.  
Si la température ambiante est superieure au seuil de la Puissance 4: 16,5°C (21 - 5 + hystérèse/2), les consignes de la puissance 4 sont exécutées.  
Enfin, si la température ambiante est inférieure au seuil de la puissance 4, les consignes de la puissance 4 sont exécutées.  

Une fois l'arrêt du poêle, la tedance d'évolution de la température ambiante est à la baisse.
La logique est alors légèrement différente.
Si la température ambiante est inférieure au seuil de la Puissance 4: 15,5°C (21 - 5 - hystérèse/2), les consignes de la puissance 4 sont exécutées.  
Si la température ambiante est inférieure au seuil de la Puissance 3: 17,5°C (21 - 3 - hystérèse/2), les consignes de la puissance 3 sont exécutées.  
Si la température ambiante est inférieure au seuil de la Puissance 2: 18,5°C (21 - 2 - hystérèse/2), les consignes de la puissance 2 sont exécutées.  
Si la température ambiante est inférieure au seuil de la Puissance 1: 19,5°C (21 - 1 - hystérèse/2), les consignes de la puissance 1 sont exécutées.
Si la température ambiante est inférieure au seuil de l'arrêt: 20,5°C (21 - 0 - hystérèse/2), rien n'est à faire. Le poêle est déjà à l'arrêt.

Une ligne avec toutes les valeurs calculées est disponible dans le log en mode debug. 




