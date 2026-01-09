# CDWFS-BC02EC03_N-CDWFS26-016 - Application Événements & Avis

Application Symfony pour la gestion d'événements avec système de validation d'avis et panneau d'administration EasyAdmin.

## Installation

### Prérequis
- PHP 8.4 ou supérieur
- Composer
- Une base de données MySQL/MariaDB

### Étapes d'installation

1. **Cloner le projet**
   ```bash
   git clone <repository-url>
   cd partielapp
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   ```

3. **Configurer l'environnement**
   - Copier `.env` en `.env.local` et configurer votre base de données
   - Exemple pour MySQL local :
     ```
     DATABASE_URL="mysql://root:password@127.0.0.1:3306/events_reviews?serverVersion=5.7"
     ```

4. **Créer la base de données**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. **Charger les données de test (optionnel)**
   ```bash
   php bin/console doctrine:fixtures:load
   ```

## Lancer l'application

### En développement
```bash
symfony serve
```
L'application sera accessible à : `http://127.0.0.1:8000`

## Identifiants de test

Utilisez ces comptes pour tester l'application :
(disponible dans le fichier .sql exporté à la racine)

| Email | Mot de passe | Rôles | Description |
|-------|-------------|-------|-------------|
| `miloud@lol.fr` | `lollol123` | Utilisateur, Responsable | Peut créer des événements et valider les avis |
| `lol@lol.lol` | `password123` | Admin, Responsable, Utilisateur | Accès complet - panneau d'administration |
| `jeanmi@lol.fr` | `password` | Utilisateur | Peut créer des avis |
| `jeanmi@lol.com` | `password` | Utilisateur | Peut créer des avis |

## Fonctionnalités par rôle

### Utilisateur Standard
- ✓ Voir tous les événements
- ✓ Créer des avis sur les événements
- ✓ Éditer ses propres avis
- ✓ Noter les événements (1-5 étoiles)

### Responsable d'Événement
- ✓ Toutes les permissions de l'utilisateur standard
- ✓ Créer des événements
- ✓ Éditer ses propres événements
- ✓ Valider/modérer les avis sur ses événements
- ✓ Ajouter des commentaires de modération

### Administrateur
- ✓ Accès au panneau d'administration EasyAdmin
- ✓ Gérer tous les utilisateurs (modifier les rôles)
- ✓ Gérer tous les événements
- ✓ Modérer tous les avis
- ✓ Voir les statistiques globales

## Accès à l'administration

**URL** : `http://127.0.0.1:8000/admin`

Utilisez le compte administrateur :
- **Email** : `lol@lol.lol`
- **Mot de passe** : `password123`

## Flux de travail - Avis

1. **Création d'avis** : Un utilisateur crée un avis (défaut : non validé)
2. **En attente** : L'avis apparaît dans la liste "En attente de validation"
3. **Modération** : Le responsable de l'événement ou admin valide l'avis
4. **Publication** : L'avis devient visible pour tous les utilisateurs

## Structure du projet

```
partielapp/
├── config/              # Configuration Symfony
├── public/              # Point d'entrée public
├── src/
│   ├── Controller/      # Contrôleurs
│   ├── Entity/          # Entités Doctrine
│   ├── Form/            # Types de formulaires
│   └── Repository/      # Requêtes de base de données
├── templates/           # Templates Twig
├── migrations/          # Migrations de base de données
└── tests/               # Tests unitaires
```

## Commandes utiles

```bash
# Vider le cache
php bin/console cache:clear

# Générer une migration après modification d'entité
php bin/console make:migration
php bin/console doctrine:migrations:migrate

# Lancer les tests
php bin/phpunit
```

## Technologies utilisées

- **Framework** : Symfony 8
- **ORM** : Doctrine
- **Admin** : EasyAdmin Bundle
- **Frontend** : Bootstrap 5, Twig
- **Base de données** : MySQL/MariaDB
