## Contexte du projet
Projet d'apprentissage backend d'une semaine afin de me familiariser avec le développement en MVC via framework, particulièrement le côté CRUD et Dockerisation.

Le site permet de créer des classes et l'enseignant peut ensuite assigner des tâches aux étudiants.

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

```bash
git clone https://github.com/HopefulUndead/classroom-todo
cd classroom-todo
docker compose up --build
docker compose up -d 
```
le .env.local a été **exceptionnelement** rendu publique pour un usage sans configuration.

## Stack
* Docker Desktop
* Boostrap
* Symfony 7.2.6
* Apache
* MSQL
* PHPMyAdmin

### Symfony debug\:config , Bundles utilisés

Bundle name                Extension alias
---

DebugBundle                debug
DoctrineBundle             doctrine
DoctrineMigrationsBundle   doctrine\_migrations
FrameworkBundle            framework
MakerBundle                maker
MonologBundle              monolog
SecurityBundle             security
StimulusBundle             stimulus
TurboBundle                turbo
TwigBundle                 twig
TwigExtraBundle            twig\_extra
WebProfilerBundle          web\_profiler

---
