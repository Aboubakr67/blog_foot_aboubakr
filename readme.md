# Blog Football Aboubakr

Bienvenue sur le projet **Blog Football Aboubakr** ! Ce projet est une plateforme dédiée aux passionnés de football, où vous pouvez exprimer vos opinions, discuter des matchs récents, et consulter les dernières nouvelles et analyses sur le football.

## Motivations Principales et Objectifs

- **Passion pour le football** : Fournir une plateforme pour les passionnés de football où ils peuvent exprimer leurs opinions et échanger des idées.
- **Facilitation de la discussion** : Créer un espace communautaire pour discuter des matchs récents, des performances des équipes et des joueurs.
- **Centralisation des informations** : Offrir une source centralisée pour les dernières nouvelles et analyses de football.
- **Engagement des utilisateurs** : Encourager l'interaction des utilisateurs par le biais de commentaires et d'avis.

## Technologies Choisies et Raisons

### Front-end

- **HTML/CSS** : Structure et style des pages web.
- **JavaScript** : Interactivité et dynamisme des pages.
- **Twig** : Moteur de templates intégré à Symfony, permettant une séparation claire entre logique et présentation.

### Back-end

- **Symfony** : Framework PHP robuste et flexible, offrant une architecture bien structurée et une multitude de composants réutilisables.
- **Doctrine ORM** : Gestion des données relationnelles, facilitant les opérations CRUD avec la base de données.

## Arborescence du Site

![Arborescence du site](public/images-illustration/arborescence.png)

## Procédure d’Installation du Projet

1. **Cloner le projet depuis GitHub**

   ```bash
   git clone https://github.com/Aboubakr67/blog_foot_aboubakr.git
   cd blog_foot_aboubakr
   ```

2. **Installer les dépendances**

   ```bash
   composer install
   composer require fakerphp/faker
   composer require --dev doctrine/doctrine-fixtures-bundle
   composer require knplabs/knp-paginator-bundle
   composer require symfonycasts/verify-email-bundle
   composer require symfony/mailer
   ```

3. **Configuration de la base de données**

   Configurez les paramètres de connexion à la base de données dans le fichier `.env` :

   ```dotenv
   DATABASE_URL=mysql://votre_utilisateur:votre_mot_de_passe@127.0.0.1:3306/blog_foot
   MAILER_DSN=smtp://localhost:1025
   ```

4. **Créer la base de données**

   Pour créer la base de données, utilisez la commande suivante :

   ```bash
   php bin/console doctrine:database:create
   ```

   Si des migrations existent déjà dans le projet, vous avez deux options :

   - Supprimer les fichiers de migration existants et les régénérer :

     ```bash
     php bin/console make:migration
     php bin/console doctrine:migrations:migrate
     ```

   - Migrer directement avec la commande suivante :

     ```bash
     php bin/console doctrine:migrations:migrate
     ```

5. **Charger les fixtures (données de test)**

   ```bash
   php bin/console doctrine:fixtures:load
   ```

6. **Lancer le serveur**

   ```bash
   symfony server:start
   ```

## API Disponibles

Le projet expose les API suivantes pour interagir avec les données :

- **Équipes** : [http://127.0.0.1:8000/api/teams](http://127.0.0.1:8000/api/teams)
- **Jeux** : [http://127.0.0.1:8000/api/games](http://127.0.0.1:8000/api/games)
- **Avis** : [http://127.0.0.1:8000/api/avis](http://127.0.0.1:8000/api/avis)

## Aperçu du Projet

### Non Connecté

- **Page d’accueil** :
  ![Page d'accueil](public/images-illustration/accueil-non-connecter.png)

- **Inscription** :
  ![Inscription](public/images-illustration/inscription.png)

- **Connexion** :
  ![Connexion](public/images-illustration/connexion.png)

- **Listes des avis** :
  ![Listes des avis](public/images-illustration/listes-avis.png)

- **Listes des équipes** :
  ![Listes des équipes](public/images-illustration/listes-teams.png)

- **Listes des matchs** :
  ![Listes des matchs](public/images-illustration/listes-matchs.png)

- **Détails d’un avis** :
  Possibilité de modifier ou de supprimer un avis uniquement pour la personne qui l’a créé et l'admin.
    ![Listes des matchs](public/images-illustration/details-avis.png)

- **Détails des équipes et des matchs** :
  Possibilité de modifier ou de supprimer uniquement pour la personne qui les a créés et l'admin.

### Inscrit
- **Créer un avis** :
  ![Créer un avis](public/images-illustration/new-avis.png)


### Admin

- **Page d’accueil** :
  ![Page d'accueil Admin](public/images-illustration/accueil-admin.png)

- **Dashboard** :
  ![Dashboard](public/images-illustration/dashboard.png)

- **Créer une nouvelle équipe** :
  ![Créer une équipe](public/images-illustration/new-team.png)

- **Créer un nouveau match** :
  ![Créer un match](public/images-illustration/new-match.png)

- **Détails d’un avis** :
  L’admin peut supprimer ou modifier les avis.
   ![Profile](public/images-illustration/details-avis-admin.png)

- **Profile** :
  ![Profile](public/images-illustration/profile.png)

## Améliorations Possibles

- **CSS du site** : Améliorer le design et le style visuel du site.
- **Profil** : Ajouter des fonctionnalités pour modifier et supprimer son profil (conformité RGPD).
- **Suppression d’équipes et de matchs** : Résoudre les problèmes liés aux clés étrangères pour permettre la suppression (facile à faire mais manque de temps).
- **Gestion des utilisateurs** : Ajouter des fonctionnalités pour mieux gérer les utilisateurs.
