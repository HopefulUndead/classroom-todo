## Contexte du projet
Projet d'apprentissage backend d'une semaine afin de me familiariser avec le développement en MVC via framework, particulièrement le côté CRUD et Dockerisation.

Le site permet de créer des classes et l'enseignant peut ensuite assigner des tâches aux étudiants.

Le site est utilisable bien que les tests unitaires seraient a ajouter et qu'il y a quelques warnings a corriger, mais étant un projet purement éducatif je referais surement le projet avec plus de rigeur.



## ToDo donnée en début de projet
* [x] Docker(facultatif) au début du projet
* [x] systeme de classe scolaire
* [ ] tickets tt les jours avec titre
* [x] une checbox, et un bouton validé sur chacun des checbox, transition sombre avec mention "validé"
* [x] pouvoir se connecter au site web, voir ma classe et voir les tâches qui restent à faire
* [x] + : systeme d'assignation : une carte peut être asignée à une autre personne de ma classe
* [x] ++ : utilisateur administrateur : qui peut créer des classes et affecter des utilisateurs dedans
* [ ] implémentation bundle easyAdmin

## Acquis
*  Utilisation de Doctrine QueryBuilder
*  Lecture de la doc Symfony / Twig / Doctrine
*  Conteneurasitation d'un projet
*  Mapping et CRUD Doctrine (réalisés manuellement)
*  View avec Twig/Bootstrap
*  Authentification et sécurité symofony (login, roles)

## Installation
Symfony et composer doivent déjà être installés.
Recommandé d'installer sur une configuration linux sinon VRAIMENT lent...

```bash
git clone https://github.com/HopefulUndead/classroom-todo
cd classroom-todo
composer install
docker compose up -d --build 
```
le .env.local a été **exceptionnelement** rendu publique pour un usage sans configuration.

## Images
![alt text](https://github.com/HopefulUndead/classroom-todo/blob/master/img/Screenshot%202025-05-16%20at%2011-03-39%20Classroom%20Todo.png)
![alt text](https://github.com/HopefulUndead/classroom-todo/blob/master/img/Screenshot%202025-05-16%20at%2011-03-46%20Classroom%20Todo.png)

![alt text](https://github.com/HopefulUndead/classroom-todo/blob/master/img/Screenshot%202025-05-16%20at%2011-03-52%20Classroom%20Todo.png)

## Stack
* Docker Desktop
* Boostrap
* Symfony 7.2.6
* Apache
* MSQL
* PHPMyAdmin
