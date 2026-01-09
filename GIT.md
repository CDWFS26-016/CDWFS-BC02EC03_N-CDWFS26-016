# Git Workflow Documentation

## Overview

Ce projet utilise un workflow basé sur **Gitflow** adapté pour un développement en solo (ou très petite équipe). Le processus garantit une gestion cohérente des branches, des versions et des déploiements.

---

## 🌳 Structure des Branches Primordiales

Le projet s'articule autour de 4 branches principales :

### 1. **main** (Production)
- **Rôle** : Miroir direct de l'environnement production
- **Contenu** : Code stable et déployé en production
- **Merges** : Uniquement à partir de `release` après tests et validation
- **Protection** : Branche critique, aucun commit direct

### 2. **release** (Recette PreProd)
- **Rôle** : Branche de staging pour les tests de sprint
- **Contenu** : Ensemble de features validées, prêtes pour QA/recette
- **Merges** : 
  - À partir de `develop` (nouvelles features d'un sprint)
  - Vers `main` (après tests réussis)
- **Version** : Incrément de version sémantique + tag Git créé ici

### 3. **develop** (Base Commune Dev)
- **Rôle** : Branche d'intégration principale pour le développement
- **Contenu** : Features complètes et testées localement
- **Merges** : 
  - À partir des branches de feature
  - Vers `release` (préparation de sprint)
  - Réception des backmerges depuis `release` et `hotfix`

### 4. **hotfix** (Corrections Production)
- **Rôle** : Miroir de `main` pour les corrections urgentes
- **Contenu** : Fixes critiques en production uniquement
- **Merges** :
  - À partir de branches `fromMain/fix/*`
  - Vers `main` (après fix appliqué)
  - Backmerge obligatoire vers `develop`
- **Usage** : Cas d'urgence production, parallèlement au développement normal

---

## 🔄 Branches de Travail

Les branches de travail suivent une convention de nommage stricte :

### Convention de Nommage
```
fromBranch/typeAction/nomAction
```

### Exemples
- `fromDevelop/feature/loginPage` — Nouvelle feature de page de connexion
- `fromDevelop/feature/userProfileForm` — Nouvelle feature de profil utilisateur
- `fromDevelop/fix/logoutRedirect` — Correction du bug de redirection logout
- `fromDevelop/refactor/authService` — Refactoring du service d'authentification
- `fromRelease/fix/paymentBug` — Fix critique trouvé en recette

### Origines Possibles
- **fromDevelop/** : Majorité des branches (features, fixes mineurs, refactoring)
- **fromRelease/** : Cas particulier - bugs découverts en recette
- **fromMain/** : Fixes critiques production (branche `hotfix`)

---

## � Convention des Messages de Commit

Chaque commit doit commencer par un **tag préfixe** qui clarifie la nature de la modification :

### Préfixes Disponibles
| Préfixe | Usage | Exemple |
|---------|--------|----------|
| **ADD:** | Ajout de nouvelles fonctionnalités / portions de code | `ADD: ajout du formulaire d'inscription` |
| **FIX:** | Correction de bugs / issues | `FIX: fixed memory leak in useEffect hook` |
| **CLEAN:** | Nettoyage du code (console.log, commentaires inutiles, etc) | `CLEAN: removal of old console.log statements` |
| **REFACTOR:** | Restructuration / amélioration de code existant | `REFACTOR: simplify authentication service` |
| **DOCS:** | Modifications de documentation / commentaires | `DOCS: update README with new API endpoints` |
| **TEST:** | Ajout ou modification de tests | `TEST: add unit tests for payment validation` |
| **PERF:** | Optimisations de performance | `PERF: optimize database queries in product list` |
| **STYLE:** | Changements de formatage / styles CSS | `STYLE: update button colors to match new design` |

### Règles Essentielles
- ✅ Toujours commencer par un préfixe en majuscules suivi de `:`
- ✅ Continuer avec un message descriptif en anglais ou français (cohérent avec le projet)
- ✅ Être spécifique : décrire **quoi** et **pourquoi**, pas seulement **quoi**
- ❌ Éviter les messages trop génériques : "fix stuff", "update code"

### Exemples Recommandés
```bash
git commit -m "ADD: implement dark mode toggle functionality"
git commit -m "FIX: corrected header z-index overlapping modals"
git commit -m "CLEAN: remove unused imports from userService"
git commit -m "REFACTOR: extract form validation logic into custom hook"
git commit -m "DOCS: add JSDoc comments to API utility functions"
git commit -m "TEST: add integration tests for checkout flow"
git commit -m "PERF: memoize expensive calculations in ProductCard"
```

---

## �📋 Workflow Complet

### Phase 1️⃣ : Développement de Feature

```
1. Créer une branche depuis develop
   git checkout develop
   git pull origin develop
   git checkout -b fromDevelop/feature/nomFeature

2. Développer et commiter régulièrement avec des messages taggés
   git add .
   git commit -m "ADD: ajout de la validation du formulaire"
   git commit -m "FIX: correction du bug de redirection"
   git commit -m "CLEAN: suppression des console.log de debug"

3. Pousser la branche
   git push origin fromDevelop/feature/nomFeature

4. Créer une Pull Request (PR) vers develop
   → Pas de code review (travail en solo)
   → Merge automatique après validation simple
   → Supprimer la branche après merge
```

### Phase 2️⃣ : Préparation Sprint (Develop → Release)

```
1. Regrouper les features terminées du sprint
   
2. Créer une PR : develop → release
   Titre : "Sprint X - Release v1.2.0"
   
3. Incrémenter la version sémantique (1.0.0)
   - 1er chiffre : version majeure (breaking changes)
   - 2e chiffre : version mineure (nouvelles features)
   - 3e chiffre : correction de bug (patches)
   
   Exemple : v1.2.5 → v1.3.0 (nouvelles features)

4. Créer un tag Git
   git tag v1.3.0
   git push origin v1.3.0

5. Phase de recette/QA
```

### Phase 3️⃣ : Bug en Recette (Cas Particulier)

```
1. Créer une branche depuis release
   git checkout release
   git pull origin release
   git checkout -b fromRelease/fix/nomFix

2. Appliquer le fix et tester

3. PR : fromRelease/fix/nomFix → release
   (Merge sur release pour retest)

4. Après validation, il faut aussi backmerger vers develop
   git checkout develop
   git merge fromRelease/fix/nomFix
```

### Phase 4️⃣ : Déploiement Production (Release → Main)

```
1. Tests réussis sur release ✓

2. Créer une PR : release → main
   
3. Merge vers main (devient la version production)

4. Déploiement automatique (Pipeline CI/CD)
   OU Déploiement manuel (scripts custom si besoin)
```

### Phase 5️⃣ : Hotfix Production (Main ↔ Hotfix)

```
1. Bug critique en production détecté

2. Créer une branche depuis main
   git checkout main
   git pull origin main
   git checkout -b fromMain/fix/nomFix

3. Appliquer et tester le fix localement

4. PR : fromMain/fix/nomFix → hotfix

5. Merge et test sur hotfix

6. PR : hotfix → main
   (Déploiement production auto/manuel)

7. ⚠️ IMPORTANT : Backmerge obligatoire vers develop
   git checkout develop
   git pull origin develop
   git merge hotfix
   git push origin develop
   (Cela garantit que le fix sera dans les prochains releases)
```

---

## 🏷️ Versioning Sémantique

Format : **MAJOR.MINOR.PATCH** (ex: 1.3.5)

| Segment | Cas d'Usage | Exemple |
|---------|-----------|---------|
| **MAJOR** | Breaking changes / changements incompatibles | v1.0.0 → v2.0.0 |
| **MINOR** | Nouvelles features / fonctionnalités | v1.2.0 → v1.3.0 |
| **PATCH** | Bug fixes / corrections | v1.3.0 → v1.3.1 |

### Quand incrémenter ?
- Sprint avec nouvelles features → **MINOR** ↑
- Sprint uniquement fixes → **PATCH** ↑
- Refactoring majeur/rewrite → **MAJOR** ↑

---

## 🔐 Accès et Sécurité

### Plateforme
- **GitHub** : Hosting et gestion centralisée

### Authentification
- **Clé SSH** : Seul mode d'authentification utilisé
  - Toutes les opérations Git (push/pull/clone) passent par SSH
  - Pas d'authentification HTTPS ou token basique

### Configuration SSH (si nécessaire)
```bash
# Générer une clé SSH (si pas déjà fait)
ssh-keygen -t ed25519 -C "your_email@example.com"

# Ajouter la clé à l'agent SSH
ssh-add ~/.ssh/id_ed25519

# Copier la clé publique dans GitHub
cat ~/.ssh/id_ed25519.pub
# → Ajouter dans GitHub Settings > SSH Keys
```

---

## 📌 Checklist - Avant de Push

- [ ] Code compilé / pas d'erreurs
- [ ] Tests locaux passés
- [ ] Pas de secrets/credentials en commit
- [ ] Message de commit avec tag préfixe (ADD:, FIX:, CLEAN:, REFACTOR:, etc)
- [ ] Branch à jour avec l'origine : `git pull origin <branch>`
- [ ] Branche suit la convention : `fromBranch/typeAction/nomAction`

---

## 🚀 Quick Reference

### Commandes Essentielles
```bash
# Cloner le projet
git clone git@github.com:user/project.git

# Créer une feature
git checkout develop
git pull origin develop
git checkout -b fromDevelop/feature/nomFeature

# Pousser les changements
git add .
git commit -m "ADD: ajout du système d'authentification"
git push origin fromDevelop/feature/nomFeature

# Synchroniser avec develop
git fetch origin
git rebase origin/develop

# Créer un tag
git tag v1.2.0
git push origin v1.2.0

# Voir les branches locales et distantes
git branch -a

# Supprimer une branche locale
git branch -d fromDevelop/feature/nomFeature

# Supprimer une branche distante
git push origin --delete fromDevelop/feature/nomFeature
```

---

## ⚠️ Points Critiques

1. **main** est sacré : aucun commit direct, merge only from `release`
2. **Backmerge obligatoire** : tout fix sur `release` ou `hotfix` doit revenir à `develop`
3. **Tagging** : un tag par release, format sémantique strict
4. **SSH obligatoire** : toute authentification passe par clé SSH
5. **PR avant merge** : même en solo, documenter via PR pour traçabilité

---

## 📞 Troubleshooting

**Q: Oups, j'ai commité sur develop par erreur**
```bash
git reset --soft HEAD~1  # Annule le commit, garde les changements
git checkout -b fromDevelop/fix/monFix
git commit -m "Message"
git push origin fromDevelop/fix/monFix
```

**Q: Besoin de fusionner develop dans ma branche**
```bash
git fetch origin
git rebase origin/develop
# ou
git merge origin/develop
```

**Q: Conflict lors du merge**
```bash
# Résoudre manuellement les fichiers en conflit
git add fichier_resolu.ts
git commit -m "Resolve merge conflict"
```

---

**Dernière mise à jour** : 9 Janvier 2026