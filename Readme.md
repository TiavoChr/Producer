# Producer Application

Ce projet est un exemple d'application Producer pour la communication avec RabbitMQ. Il envoie un message à une file d'attente RabbitMQ lorsqu'un utilisateur est inscrit.

## Configuration

avec le fichier Dockerfile vous pouvez mettre en place apache pour php et pdo mysql
creer une base de donnée mysql et executer le script dans utilisateur.sql

Veuillez aussi mettre à jour les paramètre de connéxion à la base de données dans le fichier index.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "NomDeLaBase";

- installer les dépendances avec les commandes:
. composer install
. composer require php-amqplib/php-amqplib

endpoint: http://Ip_de_Votre_serveur/Producer

mettre en place RabbitMq sur le port 15672:
docker run -it --rm --name rabbitmq -p 5672:5672 -p 15672:15672 rabbitmq:3.12-management

Pour l'enregistrement l'enregistrement d'un utilisateur api methode post:
form-data : nom, prenom, email, login, mot_de_passe

si l'enregistrement est ok : 
{
    "message": "Utilisateur inscrit avec succès. Un e-mail de confirmation a été envoyé."
}
