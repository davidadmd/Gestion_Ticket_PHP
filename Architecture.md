# Architecture de l'Application de Gestion de Tickets (MVC)

## Structure MVC

### Modèle (M)
Le modèle gère la logique métier et l'accès aux données.
```
models/
├── Model.php           # Classe de base pour tous les modèles
├── UserModel.php       # Gestion des utilisateurs
├── TicketModel.php     # Gestion des tickets
├── RoleModel.php       # Gestion des rôles
└── AdminModel.php      # Fonctionnalités d'administration
```

### Vue (V)
Les vues gèrent l'interface utilisateur avec Twig.
```
views/
├── layout/
│   └── base.twig       # Template de base
├── admin/
│   └── *.twig         # Vues d'administration
├── tickets/
│   ├── index.twig     # Liste des tickets
│   ├── create.twig    # Création de ticket
│   └── view.twig      # Vue détaillée
├── users/
│   ├── login.twig     # Connexion
│   ├── register.twig  # Inscription
│   └── profile.twig   # Profil utilisateur
└── home.twig          # Page d'accueil
```

### Contrôleur (C)
Les contrôleurs gèrent la logique de l'application et la coordination.
```
controllers/
├── Controller.php      # Contrôleur de base
├── AdminController.php # Gestion administration
├── AuthController.php  # Authentification
├── HomeController.php  # Page d'accueil
├── ProfileController.php # Profils utilisateurs
├── TicketController.php # Gestion des tickets
└── UserController.php   # Gestion des utilisateurs
```

## Structure Complète du Projet

```
Gestion_Ticket_PHP/
├── config/
│   └── twig.php          # Configuration de Twig
├── controllers/          # Contrôleurs (C)
├── models/              # Modèles (M)
├── views/               # Vues (V)
├── public/
│   ├── css/            # Styles
│   └── js/             # Scripts
├── database/
│   ├── Database.php    # Classe de connexion
│   ├── base.sql        # Structure initiale
│   └── migrations/     # Migrations
└── index.php           # Point d'entrée
```

## Composants Clés

### 1. Routage (index.php)
- Utilise AltoRouter pour le routage
- Définit les routes et les contrôleurs associés
- Gère les paramètres d'URL

### 2. Base de Données
- Utilise PDO pour la connexion
- Gestion des migrations
- Tables principales : users, tickets, roles

### 3. Templating
- Utilise Twig comme moteur de template
- Layouts réutilisables
- Fonctions personnalisées (asset, path)

### 4. Sécurité
- Authentification utilisateur
- Gestion des rôles (admin, tech, user)
- Protection contre les injections SQL
- Validation des entrées

### 5. Assets
- CSS : Bootstrap + styles personnalisés
- JavaScript : Fonctionnalités interactives
- Icônes : Font Awesome

## Flux de Données

1. La requête arrive sur index.php
2. Le routeur dirige vers le contrôleur approprié
3. Le contrôleur interagit avec le(s) modèle(s)
4. Le modèle accède à la base de données
5. Le contrôleur prépare les données pour la vue
6. La vue (Twig) génère le HTML final
