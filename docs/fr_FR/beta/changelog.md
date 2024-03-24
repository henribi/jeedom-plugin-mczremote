# Changelog mczremote

>**IMPORTANT**
>
>Pour rappel s'il n'y a pas d'information sur la mise à jour, c'est que celle-ci concerne uniquement de la mise à jour de documentation, de traduction ou de texte

# 24/03/2024 (2.0.0)
- Affichage d'un numéro de version dans la page de configuration
- Migration vers un environnement python venv pour support Jeedom 4.4.x et Debian 12 Bookworm
  Cette version nécessite la réinstallation des dépendances

# 08/01/2024
- Ajout de la référence à certains poêle Brisach

# 06/01/2024
- Suppression de la copie et de l'installation directe du template dans jMQTT
- Ajout d'une fonction de téléchargement du template
- Correction bug dans le démon

# 21/09/2023
- Correction d'une incompatibité entre MCZ Remote et jMQTT pour la copie et l'installation du template.
- Mise à jour de certains messages

# 03/01/2023
- Ajout des mode adaptatifs dans profils (Merci à Luch80)

# 21/10/2022
- Adaptation pour v4.3
- Adaptation documentation

# 27/06/2022
- Finalisation création template dans jMQTT 

# 13/06/2022
- Passage en stable avec support de jMQTT

# 04/06/2022
- Ajout fonction Installer template et créer équipement dans jMQTT
- Adaptation documentation

# 28/05/2022
- Déplacement de la fonction Installer template dans jMQTT
- Adaptation documentation

# 05/04/2022
- Doc: Ajout *Utilisation du template*
- Template: Suppression *Ajout automatique des commandes*
- Template: Spécification icône chauffage

# 02/03/2022
- Réactivation de l'installation du template dans jMQTT.

# 17/02/2022
- Add infos to recréer le log du démon s'il est supprimé
- Modification du niveau de certains messages dans le démon (Info à debug)
- Ajout du support de la commande SUBmcz 9001 pour la mise à jour de l'heure du poêle
- Documentation des commandes du poêle

# 10/02/2022
- Correction bug si pas d'authentification MQTT

# 06/02/2022
- Copie du template MCZRemote vers jMQTT
- Correction documentation

# 19/01/2022
- encrypte les infos de configuration dans la BDD
- masque les paramètres d'appel du démon
- correction message de log démon

# 18/01/2022
- contrôle des dépendances installées
- ajout info dans documentation

# 06/01/2022
- correction bug

# 04/01/2022
- Release initial

