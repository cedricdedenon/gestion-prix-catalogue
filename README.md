# Le projet

Le projet se place dans le cadre de la gestion de prix d'un catalogue de produit. On veut pouvoir définir des règles de réduction du prix pour les périodes de solde qui prenne en compte la catégorie et le prix initial du produit.

ex: appliquer, en période de solde, 30% de réduction sur les produits de la catégorie Electro-ménager qui ont un prix supérieur à 100 Euros.

Pour arriver à ce but, les fonctionnalités suivantes ont été développées.

## Listing des règles de réduction

Une page web qui permet de lister toutes les règles de réduction actuellement définies.

## Formulaire de création d'une règle

L'application fournit un formulaire de création d'une règle.
Les données suivantes sont validées :
* pourcentage de réduction compris entre 1 et 50%
* expression de la règle valide

> Pas de formulaire d'édition d'une règle.

## Commande de calcul des pourcentages de réduction sur chaque produit

Chaque matin, une tâche planifiée (CRON) est lancé pour remettre à jour les prix réduits de la totalité du catalogue de produits. Cette commande envoie un email récapitulant tous les tarifs modifiés.

Ne pas oublier de changer la directive MAILER_URL dans le fichier .env pour l'envoi de l'email.
Dans le fichier src/Service/EmailService.php, modifier également les adresses mail du destinataire et de l'émetteur.

Une commande (app:send-email) a été créée pour réaliser ces tâches, il suffit de lancer la commande `php bin/console app:send-email`

Pour créer une tâche planifiée, on utilise soit le crontab (sous Linux), soit schtasks (sous Windows)
* Sous Windows [Schtasks](https://docs.microsoft.com/en-us/windows/desktop/TaskSchd/schtasks)

	`schtasks /CREATE /TN "catalogue" /TR "php \path\to\your\project\bin\console app:send-email" /SC DAILY /mo 1`
	`schtasks /DELETE /tn "Catalogue"`

* Sous Linux [Cron](https://fr.wikipedia.org/wiki/Cron)

# Les technologies/méthodologies à utiliser

## Imposées

Utilisation du framework Symfony (version 4.3)

Utilisation du composant Symfony [ExpressionLanguage](https://symfony.com/doc/current/components/expression_language.html).

Utilisation de SwiftMailer, la fonction mail() pour envoyer l'email de récapitulatif de la commande  **n'est pas utilisée** .


-----------------

# Annexes

## Schema de tables de la base de données

discount_rules|
-------------|
id|
rule_expression|
discount_percent|


products|
-------------|
id|
name|
price|
discounted_price|
type (Electro-ménager, Hi-fi, ...)|

## Exemples de produits / règles

### Produits
id|name|price|discounted_price|type|
-------------|-------------|-------------:|-------------:|-------------|
1|Cafetière|15.50|NULL|Electro-ménager|
2|Enceinte Bluetooth|100.00|NULL|Hi-fi|

### Règles

id|rule_expression|discount_percent|
-------------|-------------|-------------|
1|product.type = 'Electro-ménager' and product.price >= 100 |20|
2|product.type = 'Hi-fi' and product.price < 100 |10|
3|product.type = 'Cuisine'|10|


-----------------

# Environnement de test

Le projet a été validé sous Windows 10, avec Xampp en local (répertoire de travail htdocs) et en utilisant:
* PHP 7.2 (cli)
* Symfony 4.3.2
* Xampp 3.2.2 
* phpMyadmin 4.7.4
* Bootstrap 4.3.1
* jQuery 3.3.1
