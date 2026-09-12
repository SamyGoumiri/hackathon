# Esperanto — Language Learning Made Easy

Application web d'apprentissage de langues (projet réalisé en hackathon),
inspirée de Duolingo. Permet de suivre des leçons structurées en unités,
faire des exercices, jouer à des mini-jeux de vocabulaire, et discuter avec
un chatbot (traducteur + tuteur).

## Langues disponibles

- Français
- Allemand
- Italien
- Espagnol

Chaque langue est organisée en 4 unités, avec leçons, exercices et tests
par unité.

## Fonctionnalités

- **Authentification** — inscription / connexion / déconnexion
  (`content/auth/`)
- **Dashboard** — tableau de bord utilisateur (`content/main/dashboard.php`)
- **Leçons & exercices** — contenu par langue et par unité, avec tests de
  fin d'unité (`content/main/{french,german,italian,spanish}/`)
- **Mini-jeu** — Word Master (`content/main/games/word-master/`)
- **Chatbot** — traducteur et tuteur conversationnel
  (`content/main/chatbot/`)

## Stack

- **Backend** : PHP + MySQL (`mysqli`)
- **Frontend** : HTML/CSS (`index.css`), Boxicons pour les icônes
- **Base de données** : MariaDB/MySQL, dump fourni dans
  `database/esperanto.sql`

## Installation

Prérequis : un environnement PHP + MySQL (ex. XAMPP/WAMP/MAMP, ou
`php -S` + MySQL en local).

1. Créer une base `Esperanto` et importer le dump :
   ```bash
   mysql -u root -e "CREATE DATABASE Esperanto"
   mysql -u root Esperanto < database/esperanto.sql
   ```
2. Vérifier les identifiants de connexion dans `database/connect.php`
   (par défaut : host `localhost`, user `root`, pas de mot de passe).
3. Servir le dossier via un serveur PHP :
   ```bash
   php -S localhost:8000
   ```
4. Ouvrir `http://localhost:8000`.

## Structure

```
index.php                 # Page d'accueil
index.css
database/
  connect.php              # Connexion MySQL
  esperanto.sql             # Dump de la base (schéma + données)
content/
  auth/                     # Login / register / logout
  main/
    dashboard.php
    chatbot/                # Traducteur + tuteur
    games/word-master/       # Mini-jeu de vocabulaire
    french/  german/  italian/  spanish/
      units/unit1..4          # Leçons et exercices par unité
image/                      # Logos et assets
```

## Contexte

Projet réalisé dans le cadre d'un hackathon — prototype fonctionnel, pas
destiné à la production en l'état (pas de hashage visible des mots de passe
côté `connect.php`, pas de `.env`, identifiants DB en dur à adapter).
