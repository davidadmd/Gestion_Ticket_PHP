Voici un exemple détaillé de fichier README.md adapté à votre projet de gestion de tickets, en prenant en compte l'architecture décrite et les fonctionnalités principales :

---

# Gestion de Tickets

**Gestion de Tickets** est une application web basée sur une architecture MVC (Modèle-Vue-Contrôleur) qui permet la gestion de tickets de support. L'application est conçue pour être simple et évolutive, et utilise PHP 8, MySQL, Twig pour les templates, et Composer pour la gestion des dépendances.

## Table des Matières

- [Architecture du Projet](#architecture-du-projet)
- [Fonctionnalités](#fonctionnalit%C3%A9s)
- [Installation et Configuration](#installation-et-configuration)
- [Utilisation](#utilisation)
- [Contributions](#contributions)
- [Licence](#licence)

## Architecture du Projet

Le projet suit une structure de répertoires claire afin de séparer les responsabilités :

```
gestion_tickets/
├── assets/             # Ressources statiques (CSS, JS, images)
├── controllers/        # Contrôleurs de l'application (ex: TicketController, AuthController, AdminController)
├── models/             # Modèles de données et classes d'accès à la BDD (ex: Ticket.php, User.php)
├── views/              # Templates Twig
│   ├── admin/        # Interface d'administration
│   ├── auth/         # Pages d'authentification
│   └── tickets/      # Gestion des tickets (liste, création, modification, attribution, etc.)
├── middlewares/        # Middlewares (ex: AuthMiddleware)
├── forms/              # Gestion des formulaires (le cas échéant)
├── vendor/             # Dépendances installées via Composer (ignoré par Git grâce au fichier .gitignore)
└── index.php           # Point d'entrée de l'application
```

## Fonctionnalités

L'application propose trois niveaux d'utilisateurs avec des fonctionnalités spécifiques :

### 1. Utilisateur (Client)

- **Connexion / Déconnexion** : Accès sécurisé via une authentification.
- **Gestion des Tickets** :
  - Créer un ticket en renseignant un titre et une description.
  - Visualiser la liste de ses tickets créés avec leur état (attribution, statut, priorité, date de création).
  - Modifier ou supprimer un ticket qu'il a créé.
  
### 2. Technicien

- **Connexion / Déconnexion** : Accès sécurisé.
- **Gestion des Tickets** (en plus des actions utilisateurs) :
  - Attribuer un ticket à un technicien.
  - Modifier le statut du ticket (par exemple : ouvert, en cours, résolu).
  - Modifier la priorité d'un ticket (par exemple : faible, moyenne, élevée).

### 3. Administrateur

- **Gestion des Comptes Utilisateurs** :
  - Créer, modifier et supprimer des comptes utilisateurs.
- **Gestion des Tickets** :
  - Possibilité d'effectuer toutes les actions qu'un technicien peut faire, ainsi que de visualiser et gérer l'ensemble des tickets.

> **Remarque :** Seules les fonctionnalités essentielles sont implémentées, sans fonctionnalités avancées comme les statistiques ou l'historique détaillé.

## Installation et Configuration

### Prérequis

- **PHP 8.x**
- **MySQL**
- **Composer**

### Installation

1. **Cloner le Dépôt**  
   ```bash
   git clone https://votre-depot.git
   cd gestion_tickets
   ```

2. **Installer les Dépendances**  
   Utilisez Composer pour installer les dépendances :
   ```bash
   composer install
   ```

3. **Configuration de la Base de Données**  
   Créez une base de données MySQL nommée `gestion_tickets`.  
   Utilisez les scripts SQL suivants pour créer les tables principales :  

   - **Table `users`** (pour la gestion des comptes)  
     ```sql
     CREATE TABLE users (
         id INT AUTO_INCREMENT PRIMARY KEY,
         username VARCHAR(50) UNIQUE NOT NULL,
         password VARCHAR(255) NOT NULL,
         role ENUM('user', 'technician', 'admin') DEFAULT 'user',
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
     );
     ```
   - **Table `tickets`** (pour la gestion des tickets)  
     ```sql
     CREATE TABLE tickets (
         id INT AUTO_INCREMENT PRIMARY KEY,
         title VARCHAR(255) NOT NULL,
         description TEXT,
         status ENUM('open', 'in_progress', 'resolved') DEFAULT 'open',
         priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
         user_id INT DEFAULT NULL,          -- Le créateur du ticket (client)
         assigned_to INT DEFAULT NULL,        -- L'utilisateur (technicien) à qui le ticket est attribué
         FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
         FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
     );
     ```
   - **(Optionnel) Table `comments`**  
     Si vous souhaitez ajouter un système de commentaires :  
     ```sql
     CREATE TABLE comments (
         id INT AUTO_INCREMENT PRIMARY KEY,
         ticket_id INT NOT NULL,
         user_id INT NOT NULL,
         comment TEXT NOT NULL,
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
         FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
     );
     ```

4. **Configuration de l'Application**  
   Modifiez le fichier `config/config.php` pour définir les constantes de connexion à la base de données :
   ```php
   <?php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'gestion_tickets');
   define('DB_USER', 'votre_utilisateur');
   define('DB_PASS', 'votre_mot_de_passe');
   ```
5. **Créer un Fichier `.gitignore`**  
   Assurez-vous d'ignorer le dossier `vendor/`. Exemple de contenu de `.gitignore` :
   ```
   vendor/
   composer.lock
   ```
   
## Utilisation

1. **Point d'entrée**  
   L'application démarre via le fichier `index.php` à la racine du projet.

2. **Authentification**  
   L'utilisateur se connecte via l'interface de connexion. Les pages de l'application (tickets, profil, etc.) sont protégées et nécessitent une authentification.

3. **Fonctionnalités Utilisateurs**  
   - Un utilisateur peut créer un ticket, consulter la liste de ses tickets, modifier ou supprimer un ticket qu'il a créé.
  
4. **Fonctionnalités Technicien**  
   - Un technicien peut attribuer un ticket à un technicien et modifier le statut ainsi que la priorité d'un ticket.
  
5. **Fonctionnalités Administrateur**  
   - Un administrateur peut créer et gérer les comptes utilisateurs (ajout, modification, suppression) ainsi que gérer l'ensemble des tickets.

## Contribuer

Les contributions sont les bienvenues !  
- Fork le dépôt  
- Crée une branche pour ta fonctionnalité : `git checkout -b ma-fonctionnalite`  
- Commit tes modifications : `git commit -m 'Ajout de ma fonctionnalité'`  
- Pousse la branche : `git push origin ma-fonctionnalite`  
- Crée une Pull Request

## Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

---

Ce README fournit une vue d'ensemble détaillée de l'architecture et des fonctionnalités principales de ton projet, en se concentrant uniquement sur les fonctionnalités de base (authentification, gestion des tickets et gestion des utilisateurs). N'hésite pas à ajuster le contenu selon l'évolution de ton projet.