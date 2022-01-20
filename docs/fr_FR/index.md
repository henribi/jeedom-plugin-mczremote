# Plugin mczremote

Ce plugin permet de dialoguer avec un poêle à pellets de la gamme MCZ Maestro via les serveurs de MCZ.

Ce plugin est prévu pour être installé sur le serveur Jeedom.

> **Prérequis**
>
>Votre poêle doit être connecté au wifi de votre domicile. Il doit pouvoir être contrôlé par l'application MCZ Maestro à partir de votre smartphone en 4G ou hors de votre domicile.
>
>> ***Attention***
>>
>> Ce plugin n'est pas compatible avec les poêles qui utilisent l'application Maestro MCZ et le protocole Maestro+.
>>
>>

> **MQTT**
>
> Vous devez disposer ou avoir préalablement installé un serveur MQTT. Généralement mosquitto.  Il peut être installé via le plugin ***JMQTT***
>

# Configuration

## La configuration en quelques clics

![Configuration générale](../images/configuration.png)

Dans cette page de configuration, outre les informations habituelles pour un équipement, vous avez la zone de configuration pour indiquer les paramètres de fonctionnement et de connexion.

### MCZ Maestro

Cette zone permet l'introduction des informations pour votre poêle.

#### Device serial

Vous devez indiquer dans cette zone l'information *Device serial* de votre poêle

#### Device MAC

Vous devez indiquer dans cette zone l'information *Device MAC* de votre poêle

#### URL des serveurs MCZ

Cette information est préremplie avec l'URL des serveurs MCZ.

### MQTT

#### IP du serveur

Vous devez spécifier ici l'adresse IP du serveur MQTT.  Ce serveur peut être local sur votre Jeedom. L'adresse IP est alors 127.0.0.1

#### Port du serveur

Généralement, le port est 1883 sauf si la configuration du serveur MQTT a été modifiée.

#### Utilisateur et Mot de passe

Ces informations sont optionnelles. Il faut les indiquer si votre serveur MQTT nécessite un utilisateur et mot de passe pour se connecter.

#### Topic PUB

Cette information est préremplie avec le topic de publication sur le serveur MQTT.

#### Topic SUB

Cette information est préremplie avec le topic de publication sur le serveur MQTT.


#### Port socket interne

C'est le port de dialogue entre le démon et Jeedom. Ce port doit être adapté si vous avez un conflit de port sur votre installation.


## Installation des dépendances

Cette opération va installer sur votre Jeedom les modules python nécessaires au fonctionnement du démon.

## Démon

Dans cette zone, vous pouvez agir sur le démon.

Un message éventuel dans cette zone indique un problème à corriger.


# Installation

Ce plugin n'est actuellement pas distribué via le market Jeedom. La demande est en cours.

Il faut l'installer à partir d'un dépôt Github.

> **Attention**
>
>Il n’y a pas d’assistance de l’équipe Jeedom sur les plugins installés depuis une autre source que le Market Jeedom.De plus, l’installation d’un plugin depuis une autre source que le Market Jeedom entraine la perte globale d’assistance par l’équipe Jeedom.
>

Cela nécessite une configuration.  Via Les menus *Réglages*, *Systèmes*, *Configuration* suivi de l'onglet *Mises à jour/Market*, dans la zone *Configuration des dépôts*, sélectionner *Github*.  A ce niveau, il faut valider **Activer Github**

Sauvergardez la configuration et allez dans le menu *Plugins*, *Gestion des plugins*

Sélectionnez le **+** pour ajouter un plugin.

![Installation plugin](../images/depot_github.png)

Commencez par choisir *Github* dans le *Type de source*.

Les champs de configuration du dépôt Github sont alors visibles.

Entrez les informations suivantes pour les différents champs.

| | |
|---|---|
|ID logique du plugin|mczremote|
|Utilisateur ou ...|henribi|
|Nom du dépôt|jeedom-plugin-mczremote|
|Branche|master|

Sauvegardez et allez dans un autre menu.

Revenez dans le menu *Gestion des plugins*.  Le plugin MCZ Remote est maintenant visible.

Il vous reste à installer les dépendances, à fournir les paramètres de configuration et à lancer le démon. 


# Remerciements

Ce plugin est largement inspiré des travaux de Anthony, EtienneME et Pipolas sur le forum suivant: <https://community.jeedom.com/t/mcz-maestro-et-jeedom/6159/183> 

Git original: <https://github.com/Anthony-55/maestro>  

Ce plugin a été créé pour aider, je ne souhaite en aucun cas m'approprier ce code qui n'est pas le mien, et sans lequel ce plugin n'existerait pas.



