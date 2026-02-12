# Mini Blog - Symfony 7.4

Un blog complet développé avec Symfony 7.4, incluant un système de gestion d'articles, de commentaires et d'utilisateurs.

## Fonctionnalités

### Pour les visiteurs
- Consultation de la page d'accueil avec la liste des articles
- Lecture des articles complets
- Visualisation des commentaires approuvés
- Inscription au site

### Pour les utilisateurs connectés
- Toutes les fonctionnalités des visiteurs
- Ajout de commentaires sur les articles (soumis à modération, avec limitation de débit)
- **Profil personnel** : 
  - Modifier les informations (prénom, nom, email)
  - Changer la photo de profil
  - Changer le mot de passe
  - Voir ses articles publiés
  - Voir ses commentaires avec leur statut

### Pour les administrateurs
- **Gestion des articles** : Création, modification et suppression d'articles
- **Gestion des catégories** : Création, modification et suppression de catégories
- **Gestion des utilisateurs** : Validation/désactivation des comptes utilisateurs, promotion en administrateur
- **Modération des commentaires** : Approbation ou rejet des commentaires
- **Dashboard** : Vue d'ensemble des statistiques du blog

## Technologies utilisées

- **Framework** : Symfony 7.4 (LTS)
- **Base de données** : MySQL 8.0
- **Frontend** : Bootstrap 5.3 avec Bootstrap Icons
- **Authentification** : Symfony Security Component
- **ORM** : Doctrine
- **Gestion BDD** : phpMyAdmin

## Installation

### Prérequis
- Docker
- Docker Compose

### Démarrage

1. **Cloner le dépôt**
```bash
git clone <repository-url>
cd mini_blog
```

2. **Configurer les variables d'environnement**
```bash
cp .env.example .env
```

Générez et configurez une clé `APP_SECRET` dans le fichier `.env` :
```bash
# Générer la clé
openssl rand -hex 32

# Copiez le résultat dans .env
APP_SECRET=votre_cle_generee_ici
```

> **Important** : La clé `APP_SECRET` est essentielle pour la sécurité (CSRF, sessions, cookies). Docker Compose lira automatiquement les variables depuis le fichier `.env`

3. **Construire et démarrer les conteneurs**
```bash
docker compose up -d --build
```

4. **Exécuter les migrations**
```bash
docker compose exec php php bin/console doctrine:migrations:migrate
```

5. **Charger les données de test**
```bash
docker compose exec php php bin/console doctrine:fixtures:load
```

Cela créera :
- Un administrateur : `admin@blog.com` / `admin123`
- Un utilisateur : `user@blog.com` / `user123`
- Un utilisateur en attente : `pending@blog.com` / `pending123`
- 5 articles avec catégories
- Plusieurs commentaires de test

6. **Accéder à l'application**
- Site : `http://localhost:8080`
- Administration : `http://localhost:8080/admin`
- phpMyAdmin : `http://localhost:8081` (root/root)
- MySQL : `localhost:3307` (root/root)

### Arrêter les conteneurs
```bash
docker compose down
```

### Supprimer les conteneurs et volumes
```bash
docker compose down -v
```

## Structure du projet

```
mini_blog/
├── config/             # Configuration Symfony
├── migrations/         # Migrations de base de données
├── public/            # Point d'entrée web
├── src/
│   ├── Controller/    # Contrôleurs
│   │   ├── Admin/    # Contrôleurs admin
│   │   ├── HomeController.php
│   │   ├── PostController.php
│   │   ├── RegistrationController.php
│   │   └── SecurityController.php
│   ├── Entity/       # Entités Doctrine
│   ├── Form/         # Formulaires
│   ├── Repository/   # Repositories Doctrine
│   ├── Security/     # Classes de sécurité
│   └── DataFixtures/ # Données de test
├── templates/        # Templates Twig
│   ├── admin/       # Templates admin
│   ├── home/        # Templates publics
│   ├── post/
│   ├── registration/
│   └── security/
├── var/             # Fichiers générés (cache, logs, db)
├── docker/          # Configuration Docker
├── Dockerfile       # Image Docker PHP
├── docker-compose.yml
└── README.md
```

## Comptes de test

Après avoir chargé les fixtures :

| Email | Mot de passe | Rôle | Statut |
|-------|--------------|------|--------|
| admin@blog.com | admin123 | ROLE_ADMIN | Actif |
| user@blog.com | user123 | ROLE_USER | Actif |
| pending@blog.com | pending123 | ROLE_USER | Inactif |

## Thème et Design

L'application utilise Bootstrap 5.3 pour un design moderne et responsive :
- Navigation responsive avec dropdown pour l'administration
- Cards pour l'affichage des articles
- Formulaires stylisés
- Messages flash colorés
- Dashboard avec statistiques visuelles
- Interface d'administration intuitive

## Fonctionnalités de sécurité

- Protection CSRF sur tous les formulaires
- Hashage sécurisé des mots de passe (bcrypt automatique)
- Validation des comptes par un administrateur
- Contrôle d'accès basé sur les rôles (ROLE_USER, ROLE_ADMIN)
- Protection des routes d'administration
- Modération des commentaires avant publication
- **Limitation de débit** : Maximum 3 commentaires par utilisateur toutes les 15 minutes

## Fonctionnalités avancées

### URLs SEO-friendly
- Slugs générés automatiquement à partir des titres d'articles
- URLs lisibles : `/post/mon-premier-article` au lieu de `/post/1`
- Redirection automatique si le slug change

### Gestion de profil utilisateur
- Modification des informations personnelles
- Upload de photo de profil (JPEG, PNG, GIF - max 2 Mo)
- Changement de mot de passe sécurisé
- Historique des articles et commentaires
- Indicateurs de statut des commentaires (approuvé, en attente, rejeté)

### Protection anti-spam
- Rate limiting sur les soumissions de commentaires
- Messages d'erreur clairs et en français
- Consommation de jetons par utilisateur

## Tests

Pour tester l'application :

1. **Accès public** : Visitez la page d'accueil et consultez les articles
2. **Inscription** : Créez un nouveau compte (il sera en attente de validation)
3. **Connexion utilisateur** : Connectez-vous avec `user@blog.com` / `user123`
4. **Profil utilisateur** : 
   - Accédez à votre profil via le menu utilisateur
   - Modifiez vos informations personnelles
   - Téléchargez une photo de profil
   - Changez votre mot de passe
   - Consultez vos articles et commentaires
5. **Ajout de commentaire** : Ajoutez un commentaire sur un article
6. **Rate limiting** : Essayez d'ajouter plus de 3 commentaires en 15 minutes
7. **Connexion admin** : Connectez-vous avec `admin@blog.com` / `admin123`
8. **Dashboard admin** : Consultez les statistiques
9. **Gestion des utilisateurs** : Activez le compte en attente
10. **Modération** : Approuvez ou rejetez les commentaires
11. **Gestion du contenu** : Créez, modifiez ou supprimez des articles et catégories
12. **URLs SEO** : Vérifiez que les URLs des articles utilisent des slugs lisibles

## Déploiement

Pour un déploiement en production :

1. Modifier `.env` avec les variables d'environnement de production
2. Utiliser une base de données MySQL ou PostgreSQL
3. Configurer un serveur web (Nginx/Apache)
4. Activer le cache de production
5. Configurer HTTPS
6. Utiliser Docker pour un déploiement containerisé

## Licence

Ce projet a été créé dans un cadre éducatif.

## Auteur

Développé en utilisant Symfony 7.4
