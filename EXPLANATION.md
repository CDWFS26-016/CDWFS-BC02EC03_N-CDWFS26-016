# Documentation Technique - Choix et Difficultés

## 1. Choix Techniques

### 1.1 Framework : Symfony 8.x

**Justification :**
- Framework PHP de référence pour les applications d'entreprise
- Robustesse et maintenabilité
- Excellent système de sécurité intégré (Security Bundle)
- Large communauté et documentation complète
- Composants réutilisables et bien séparés
- Et surtout : Vu en cours donc "obligatoire"

**Avantages dans ce projet :**
- Gestion des rôles et permissions native
- Système de formulaires flexible
- Doctrine ORM pour abstraire la base de données

### 1.2 EasyAdmin Bundle

**Justification :**
- **Génération rapide d'interfaces CRUD** : Pas besoin de créer manuellement les pages index/show/edit/create
- **Configuration basée sur les annotations** : Définition simple via `configureFields()`, `configureCrud()`, etc.
- **Intégration native avec Doctrine** : Détection automatique des relations d'entités
- **Gestion des permissions** : Support natif des rôles Symfony
- **Responsive et moderne** : Bootstrap 5 intégré
- **Extensibilité** : Possibilité d'ajouter des actions, filtres, et personnalisations

**Alternative considérées et rejetées :**
- **AdminLTE** : Trop basique, pas de génération CRUD
- **API Platform** : Trop orienté API REST, overkill pour un panel d'admin
- **Créer un admin custom** : Trop chronophage, plus d'erreurs, moins maintenable

### 1.3 Doctrine ORM

**Justification :**
- Abstraction de la base de données (indépendant de MySQL/PostgreSQL)
- Mapping objet-relationnel robuste via annotations
- Migrations automatiques pour versionner le schéma
- Lazy loading et eager loading des relations

### 1.4 Bootstrap 5 + Twig

**Justification :**
- Bootstrap 5 : Framework CSS moderne et responsive
- Twig : Moteur de templates sécurisé et puissant
- Combinaison native dans Symfony

### 1.5 Système d'Authentification Form-Based + Session

**Justification :**
- Plus simple qu'un système JWT pour une application monolithique
- Sessions gérées automatiquement par Symfony
- Parfait pour une application web traditionnelle
- Auto-login après inscription via UsernamePasswordToken

---

## 2. Difficultés Rencontrées et Solutions

### 2.1 🔴 GRAVE : Problèmes de Connexion Réseau et Timeouts

**Problème :**
- Timeouts répétés sur port 22, idem pour installer des package que ce soit VM apt, composer, Chocolatey...
- Connexion instable à la CCI sur wifi et filaire
- Symfony serve crashing sans messages d'erreur clairs parfois

**Impact :**
- Retard dans le développement
- Frustration et redémarrages répétés du serveur

---

### 2.2 🔴 MAJEUR : Incompréhension d'EasyAdmin - Architecture Initiale Erronée

**Problème initial :**
Au démarrage, j'ai voulu mixer :
- **EasyAdmin** (censé faire tout l'admin)
- **Un AdminController custom** avec des templates manuels
- **Des routes spécifiques** (`/admin/reviews`, `/admin/users`)

Cela a créé une **architecture incohérente** :

```
❌ MAUVAISE APPROCHE :
├── /admin/                    (EasyAdmin Dashboard)
├── /admin/review              (EasyAdmin index)
├── /admin/reviews/create      (Template custom)
├── /admin/users/edit/{id}     (AdminController custom)
└── Conflits de routes et logique dupliquée
```

**Conséquences :**
- Confusion entre les deux systèmes
- Logique de validation dupliquée
- Templates qui n'appliquaient pas les permissions
- Erreurs "Route not found" aléatoires
- Difficultés à comprendre où était la logique

**Symptômes :**
```
- ROLE_USER voyait des champs qu'il ne devrait pas voir
- Valeurs de formulaires qui ne se mappaient pas
- 422 Unprocessable Entity errors
- Impossible de savoir quel système traitait la requête
```

**Solution appliquée :**

**Étape 1 : Abandon du AdminController custom**
```php
// ❌ ANCIEN : AdminController.php avec templates manuels
// → Complètement vidé/supprimé

// ✅ NOUVEAU : Trois CRUD Controllers dédiés
```

**Étape 2 : Migration vers EasyAdmin pur**

Création de 3 CRUD Controllers spécialisés :

#### a) UserCrudController
#### b) EventCrudController
#### c) ReviewCrudController

**Avantages :**
- Tri par défaut sur `isValidated` (avis non validés d'abord)
- Filtrage par événement/auteur
- Tri des avis en attente

**Étape 3 : Suppression des routes custom**

Avant :
```yaml
# ❌ ANCIEN routes.yaml
- /admin/reviews/validate/{id}
- /admin/reviews/create
- /admin/users/edit/{id}
```

Après :
```yaml
# ✅ NOUVEAU routes.yaml - Seulement les routes métier
- /review/create
- /review/{id}/edit
- /event/create
- /event/{id}/edit
```

### 2.3 🟢 MINEUR : Import incorrect des classes EasyAdmin

**Problème :**
```php
// ❌ Mauvais import
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

// Les actions attendues n'existent pas
Action::EDIT;  // ❌ Undefined
```

**Cause :**
Confusion entre les namespaces dans EasyAdmin

**Solution :**
```php
// ✅ Bon import
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

// Utiliser la classe Crud
Crud::PAGE_INDEX;
Crud::PAGE_EDIT;

// Utiliser Action pour les constantes
Action::EDIT;
Action::DELETE;
```

---

## 3. Architecture Finale - Graphique

```
┌─────────────────────────────────────────────────────────────┐
│                    APPLICATION WEB                          │
└─────────────────────────────────────────────────────────────┘
                            │
                ┌───────────┼───────────┐
                │           │           │
          ┌─────▼──┐   ┌───▼────┐  ┌──▼──────┐
          │  ADMIN │   │ PUBLIQ │  │ INSTALL │
          │ PANEL  │   │  SITE  │  │ & CONFIG│
          └─────┬──┘   └───┬────┘  └──┬──────┘
                │           │         │
        ┌───────▼───────┐   │         │
        │   EasyAdmin   │   │         │
        │   4.x Bundle  │   │         │
        │               │   │         │
        ├─UserCRUD───┐  │   │         │
        ├─EventCRUD──┤  │   │         │
        └─ReviewCRUD─┘  │   │         │
                │        │   │         │
        ┌───────▼──────┐ │   │         │
        │   Doctrine   │ │   │         │
        │     ORM      │ │   │         │
        └───────┬──────┘ │   │         │
                │        │   │         │
    ┌───────────┴────────┼───┴─────┬───┴─────────┐
    │                    │         │             │
┌───▼────┐  ┌────────────▼──┐  ┌──▼───┐   ┌────▼──────┐
│  User  │  │  Event/Review │  │ Auth │   │  Security │
│ Entity │  │   Entities    │  │ Ctrl │   │   Config  │
└────────┘  └───────────────┘  └──────┘   └───────────┘
    │                │              │            │
    └────────────────┴──────────────┴────────────┘
                     │
              ┌──────▼──────┐
              │   Database  │
              │  (MySQL)    │
              └─────────────┘
```