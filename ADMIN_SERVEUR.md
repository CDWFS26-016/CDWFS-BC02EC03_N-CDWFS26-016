# Documentation Installation VM VirtualBox - Serveur Symfony LEMP

## 📋 Vue d'ensemble

Ce guide couvre l'installation et la configuration d'une machine virtuelle Ubuntu pour héberger un serveur web Symfony avec :
- **OS** : Ubuntu (dernière version LTS)
- **Serveur Web** : Nginx
- **Interpréteur** : PHP 8.4.16 avec PHP-FPM
- **Base de données** : MariaDB
- **Gestionnaire de paquets** : Composer
- **Certificat SSL** : Auto-signé (HTTPS)
- **Interface** : Aucune (headless/ligne de commande)

---

## 🖥️ Configuration VirtualBox Recommandée

| Paramètre | Valeur |
|-----------|--------|
| **RAM** | 4 Go |
| **Processeurs** | 2 |
| **Mémoire Vidéo** | 20 Mo |
| **Disque Dur** | 20 Go |
| **Mode Réseau** | Bridge (pont) |
| **Interface Graphique** | Désactivée (headless) |

### Étapes VirtualBox (déjà fait en amont par le correcteur)
1. Créer une nouvelle VM avec Ubuntu Server (sans interface graphique)
2. Allouer 4 Go de RAM et 2 processeurs
3. Allouer 20 Go d'espace disque
4. Configurer le réseau en mode **Bridge** (connexion par pont)
5. Configurer la mémoire vidéo à 20 Mo
6. Lancer la VM

---

## 📦 Phase 1️⃣ : Installation VirtualBox Guest Additions

Les Guest Additions permettent le copier-coller bidirectionnel et l'intégration avec l'hôte.

### Installation Initiale

```bash
# Mettre à jour les dépôts
sudo apt update

# Installer les dépendances nécessaires
sudo apt install -y build-essential dkms linux-headers-$(uname -r)

# Créer et monter le répertoire pour les additions
sudo mkdir -p /media/cdrom
sudo mount /dev/cdrom /media/cdrom

# Exécuter le script d'installation
sudo sh /media/cdrom/VBoxLinuxAdditions.run

# Redémarrer la VM
sudo reboot
```

### Activation Manuelle (si nécessaire après reboot)

Si certaines fonctionnalités ne fonctionnent pas :

```bash
# Copier-coller bidirectionnel
VBoxClient --clipboard

# Glisser-déposer (Drag & Drop)
VBoxClient --draganddrop
```

---

## 🔗 Phase 2️⃣ : Configuration SSH et Réseau

### Installation et Activation SSH

```bash
sudo apt update
sudo apt install openssh-server -y
sudo systemctl enable ssh
sudo systemctl start ssh
```

### Vérifier l'Adresse IP

```bash
ip a
```

Rechercher l'adresse dans la plage définie par votre réseau bridge (ex: `192.168.50.90`).

### Configuration du Host Windows

Sur la machine hôte Windows, modifier le fichier hosts :
- **Chemin** : `C:\Windows\System32\drivers\etc\hosts`
- **À ajouter** :
```
192.168.50.90 monserveur.local
```

Remplacer `192.168.50.90` par l'IP réelle de votre VM et `monserveur.local` par votre domaine.

---

## ⚙️ Phase 3️⃣ : Installation et Configuration Nginx

### Désinstaller Apache2 (si présent)

```bash
sudo systemctl stop apache2
sudo systemctl disable apache2
sudo apt remove apache2 -y
sudo apt autoremove -y
```

### Installer Nginx

```bash
sudo apt update
sudo apt install nginx -y
sudo systemctl enable nginx
sudo systemctl start nginx
```

**Vérification** : Visiter `http://192.168.50.90` depuis le navigateur → page Nginx par défaut

---

## 🐘 Phase 4️⃣ : Installation PHP 8.4 avec FPM

### Ajouter le PPA Ondrej

```bash
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
```

### Installer PHP 8.4 et Extensions

```bash
sudo apt install -y \
  php8.4 \
  php8.4-fpm \
  php8.4-mysql \
  php8.4-xml \
  php8.4-mbstring \
  php8.4-zip \
  php8.4-curl \
  unzip
```

### Activer les Extensions

```bash
sudo phpenmod mbstring xml zip curl
```

### Vérifier les Extensions Activées

```bash
php -m | grep -E 'mbstring|xml|zip|curl|mysql'
```

### Éditer les Fichiers de Configuration (si nécessaire)

Pour PHP-FPM :
```bash
sudo nano /etc/php/8.4/fpm/php.ini
```

Pour PHP CLI :
```bash
sudo nano /etc/php/8.4/cli/php.ini
```

---

## 🗄️ Phase 5️⃣ : Installation MariaDB

### Installation et Configuration

```bash
sudo apt install mariadb-server -y
sudo mysql_secure_installation
```

**Lors de l'exécution de `mysql_secure_installation`**, répondre aux questions :
- Change root password ? → `Y` (recommandé)
- Remove anonymous users ? → `Y`
- Disable root login remotely ? → `Y`
- Remove test database ? → `Y`
- Reload privilege tables ? → `Y`

### Vérification

```bash
sudo systemctl status mariadb
```

---

## 📦 Phase 6️⃣ : Installation Composer

Composer est le gestionnaire de paquets PHP, essentiel pour Symfony.

```bash
# Télécharger le script d'installation
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

# Installer Composer globalement
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Nettoyer le script
php -r "unlink('composer-setup.php');"

# Vérifier l'installation
composer --version
```

---

## 🔐 Phase 7️⃣ : Configuration Nginx pour Symfony avec SSL Auto-Signé

### Créer le Certificat SSL Auto-Signé

```bash
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout /etc/ssl/private/selfsigned.key \
  -out /etc/ssl/certs/selfsigned.crt
```

**Fichiers générés** :
- **Clé privée** : `/etc/ssl/private/selfsigned.key`
- **Certificat public** : `/etc/ssl/certs/selfsigned.crt`

Répondre aux questions demandées (Pays, État, Ville, Organisation, Nom de domaine, etc.).

### Créer le Virtual Host Nginx

```bash
sudo nano /etc/nginx/sites-available/mon_domaine.fr
```

**Ma configuration complète** :

```nginx
# Redirection HTTP vers HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name mon_domaine.fr;
    return 301 https://$host$request_uri;
}

# Serveur HTTPS
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name mon_domaine.fr;
    root /var/www/mon_projet/public;
    index index.php;

    # SSL auto-signé
    ssl_certificate /etc/ssl/certs/selfsigned.crt;
    ssl_certificate_key /etc/ssl/private/selfsigned.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers off;

    # Logs
    access_log /var/log/nginx/mon_domaine.access.log;
    error_log /var/log/nginx/mon_domaine.error.log;

    # Headers sécurité
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    # Symfony routing
    location / {
        try_files $uri /index.php$is_args$args;
    }

    # PHP-FPM pour index.php uniquement
    location ~ ^/index\.php(/|$) {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        fastcgi_index index.php;
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        internal;
    }

    # Bloquer l'accès aux autres fichiers PHP
    location ~ \.php$ {
        return 404;
    }

    # Cache pour les fichiers statiques
    location ~* \.(?:css|js|jpg|jpeg|gif|png|ico|svg|woff2?)$ {
        expires 30d;
        access_log off;
        add_header Cache-Control "public";
    }
}
```

### Activer le Site

```bash
# Créer un lien symbolique
sudo ln -sf /etc/nginx/sites-available/mon_domaine.fr /etc/nginx/sites-enabled/

# Tester la configuration Nginx
sudo nginx -t

# Recharger Nginx
sudo systemctl reload nginx
```

**Résultat attendu** : `nginx: the configuration file /etc/nginx/nginx.conf syntax is ok`

---

## ✅ Résumé des Fonctionnalités Configurées

| Fonctionnalité | Statut |
|---|---|
| HTTP → HTTPS redirection | ✅ Active |
| SSL auto-signé | ✅ Configuré |
| Logs séparés par site | ✅ Configurés |
| Headers de sécurité | ✅ Présents |
| Cache pour fichiers statiques | ✅ 30 jours |
| Compatibilité Symfony complète | ✅ index.php + routing |
| PHP-FPM avec 8.4 | ✅ Configuré |
| MariaDB | ✅ Prêt |
| Composer | ✅ Installé |

---

## 🚀 Déploiement d'un Projet Symfony

```bash
# Se connecter à la VM via SSH
ssh user@192.168.50.90

# Naviguer vers le répertoire web
cd /var/www
sudo mkdir mon_projet
cd mon_projet

# Créer un nouveau projet Symfony
composer create-project symfony/skeleton .

# Ou cloner un projet existant
git clone https://github.com/yourrepo/project.git .

# Installer les dépendances
composer install

# Définir les permissions
sudo chown -R www-data:www-data /var/www/mon_projet
sudo chmod -R 755 /var/www/mon_projet
sudo chmod -R 775 /var/www/mon_projet/var

# Redémarrer PHP-FPM
sudo systemctl restart php8.4-fpm

# Visiter le site en HTTPS
https://monserveur.local
```

---

## 🐛 Troubleshooting

### PHP-FPM ne démarre pas
```bash
sudo systemctl restart php8.4-fpm
sudo systemctl status php8.4-fpm
```

### Nginx retourne une erreur 502 Bad Gateway
```bash
# Vérifier que le socket PHP existe
ls -la /var/run/php/php8.4-fpm.sock

# Redémarrer PHP-FPM
sudo systemctl restart php8.4-fpm
```

### Permission denied sur /var/www
```bash
# Vérifier l'utilisateur Nginx
ps aux | grep nginx

# Ajuster les permissions
sudo chown -R www-data:www-data /var/www
sudo chmod -R 755 /var/www
```

### Extensions PHP non chargées
```bash
# Lister les extensions disponibles
sudo phpenmod -l

# Activer une extension manquante
sudo phpenmod mbstring xml zip curl

# Redémarrer PHP-FPM
sudo systemctl restart php8.4-fpm
```

---

## 📞 Références Utiles

- **Documentation Nginx** : https://nginx.org/en/docs/
- **Documentation Symfony** : https://symfony.com/doc/current/index.html
- **Documentation PHP 8.4** : https://www.php.net/manual/en/index.php
- **MariaDB** : https://mariadb.org/documentation/

---

**Dernière mise à jour** : 9 Janvier 2026
