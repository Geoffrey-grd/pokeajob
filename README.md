# 🚀 PokeAJob

**Une plateforme web innovante pour connecter les étudiants avec leurs opportunités d'emploi.**

---

## 📋 Table des matières

- [À propos du projet](#-à-propos-du-projet)
- [Fonctionnalités](#-fonctionnalités)
- [Architecture](#-architecture)
- [Technologies](#-technologies)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Structure du projet](#-structure-du-projet)
- [Utilisation](#-utilisation)
- [Endpoints API](#-endpoints-api)
- [Contribution](#-contribution)
- [Licence](#-licence)

---

## 🎯 À propos du projet

**PokeAJob** est une plateforme de marché du travail (job marketplace) destinée à :
- **Les étudiants** : Découvrir et postuler à des offres d'emploi adaptées
- **Les entreprises** : Publier des offres d'emploi et recruter des talents

La plateforme facilite la mise en relation entre les entreprises et les étudiants selon leurs secteurs d'activité et leurs domaines de compétences.

---

## ⚡ Fonctionnalités

### 🔐 Authentification & Comptes
- ✅ Inscription et connexion (Étudiants & Entreprises)
- ✅ Gestion de profil personnalisé
- ✅ Suppression de compte sécurisée
- ✅ Système de session et CSRF protection

### 💼 Offres d'emploi
- ✅ Création d'offres d'emploi (Entreprises)
- ✅ Consultation des offres disponibles
- ✅ Recherche et filtrage avancés
- ✅ Gestion des offres personnelles
- ✅ Descriptions détaillées des postes

### 👥 Profils & Recherche
- ✅ Profils étudiants complets
- ✅ Profils entreprises avec logo et bannière
- ✅ Système de secteurs d'activité
- ✅ Recherche d'entreprises par secteur
- ✅ Gestion des compétences et domaines

### 📢 Pages générales
- ✅ Mentions légales
- ✅ Navigateur intuitif
- ✅ Responsive design adapté mobile

---

## 🏗️ Architecture

PokeAJob suit une **architecture MVC légère** :

```
Controllers (Entry points) → Models (Logique métier) → Views (Templates Twig)
         ↓
    Router (Dispatch)
         ↓
    Auth / Core (Services)
```

### Flux de requête
1. **Router** : Analyse la requête HTTP et route vers le contrôleur approprié
2. **Contrôleur** : Traite la logique, interroge les modèles
3. **Modèles** : Accèdent à la base de données via PDO
4. **Vue** : Rendu du template Twig avec les données

---

## 🛠️ Technologies

### Backend
- **PHP 8.0+** - Langage serveur
- **PDO** - Accès base de données
- **Twig 3.23** - Moteur de templates
- **Parsedown 1.8** - Parseur Markdown

### Frontend
- **HTML5** - Structure
- **CSS3** - Styling (composants & responsive)
- **JavaScript (Vue natif)** - Interactions client

### Outils & Infrastructure
- **Composer** - Gestion des dépendances
- **Git** - Versioning
- **MySQL/MariaDB** - Base de données

---

## 📥 Installation

### Prérequis
- PHP 8.0 ou supérieur
- Composer
- MySQL/MariaDB 5.7+
- Un serveur web (Apache/Nginx)

### 1. Cloner le repository
```bash
git clone https://github.com/YOUR_USERNAME/PokeAJob.git
cd PokeAJob
```

### 2. Installer les dépendances
```bash
composer install
```

### 3. Configurer la base de données
```bash
# Créer la base de données
mysql -u root -p < database.sql  # (à créer)
```

### 4. Configurer l'application
Modifier `config/config.php` avec vos identifiants :
```php
<?php
return [
    'host'     => 'localhost',
    'db_name'  => 'pokeajob',
    'username' => 'votre_user',
    'password' => 'votre_password',
];
```

### 5. Lancer le serveur
```bash
# Avec PHP built-in server
php -S localhost:8000 -t public

# Ou configurer Apache/Nginx pour pointer vers le dossier /public
```

Accédez à `http://localhost:8000`

---

## ⚙️ Configuration

### Variables d'environnement (Optionnel)
```bash
# À ajouter dans config/config.php ou en variables d'environnement
DB_HOST=localhost
DB_NAME=pokeajob
DB_USER=user
DB_PASSWORD=password
```

### Structure des répertoires des uploads
```
public/images/uploads/
├── company_banner/      # Bannières des entreprises
├── company_logo/        # Logos des entreprises
└── profile_img/         # Images de profil des utilisateurs
```

---

## 📁 Structure du projet

```
PokeAJob/
├── app/
│   ├── controllers/          # Contrôleurs (Points d'entrée)
│   │   ├── AuthController.php
│   │   ├── CreateOfferController.php
│   │   ├── ProfileController.php
│   │   ├── SearchPageController.php
│   │   └── ...
│   ├── models/               # Modèles (Logique métier & DB)
│   │   ├── User.php
│   │   ├── Student.php
│   │   ├── Company.php
│   │   ├── Offer.php
│   │   ├── Sector.php
│   │   └── BDDlink.php       # Base classe pour DB
│   └── views/                # Templates Twig
│       ├── login-register.twig
│       ├── profile.twig
│       ├── create_offer.twig
│       ├── search_page.twig
│       └── ...
├── core/                      # Services core
│   ├── Router.php            # Routeur personnalisé
│   ├── View.php              # Rendu des vues
│   ├── Auth.php              # Gestion authentification
│   └── Csrf.php              # Protection CSRF
├── config/
│   ├── config.php            # Configuration base données
│   └── BDD.php               # Connexion PDO
├── public/
│   ├── index.php             # Point d'entrée
│   ├── assets/
│   │   ├── base/
│   │   │   └── core.css      # Styles généraux
│   │   ├── components/       # Styles composants
│   │   │   ├── card.css
│   │   │   ├── form.css
│   │   │   └── navbar.css
│   │   ├── pages/            # Styles pages spécifiques
│   │   ├── js/               # JavaScript client
│   │   └── images/           # Ressources statiques
│   └── uploads/              # Fichiers uploadés
├── vendor/                    # Dépendances Composer
├── composer.json             # Fichier Composer
└── README.md                 # Ce fichier
```

---

## 🚀 Utilisation

### Pour les Étudiants

1. **S'inscrire**
   - Accéder à `/` 
   - Remplir le formulaire d'inscription étudiant
   - Sélectionner son secteur (Aviation, etc.)

2. **Rechercher des offres**
   - Aller à `/search_page`
   - Filtrer par entreprise ou secteur
   - Consulter les détails des offres

3. **Gérer son profil**
   - Accéder à `/profile`
   - Modifier informations et photo de profil

### Pour les Entreprises

1. **S'inscrire**
   - Accéder à `/`
   - Remplir le formulaire d'inscription entreprise
   - Ajouter logo et bannière

2. **Publier une offre**
   - Aller à `/create_offer`
   - Remplir les détails du poste
   - Valider la publication

3. **Gérer ses offres**
   - Accéder à `/my_offers`
   - Consulter, modifier ou supprimer les offres

---

## 🔌 Endpoints API

### Authentification
| Méthode | Route | Contrôleur | Description |
|---------|-------|-----------|-------------|
| GET | `/` | AuthController@renderLoginRegister | Affiche login/register |
| POST | `/loginregister` | AuthController@form_type | Traite login/register |
| GET | `/logout` | AuthController@logout | Déconnexion |

### Profil
| Méthode | Route | Contrôleur | Description |
|---------|-------|-----------|-------------|
| GET | `/profile` | ProfileController@renderProfile | Affiche le profil |
| GET | `/edit_profile` | ProfileController@editProfile | Affiche édition |
| POST | `/modify_profile` | ProfileController@modifyProfile | Modifie le profil |

### Offres
| Méthode | Route | Contrôleur | Description |
|---------|-------|-----------|-------------|
| GET | `/create_offer` | CreateOfferController@renderCreateOffer | Formulaire création |
| POST | `/create_offer` | CreateOfferController@createOffer | Crée une offre |
| GET | `/offer_description` | OfferDescriptionController@renderOfferDescription | Détails d'une offre |
| GET | `/my_offers` | MyOffersController@renderMyOffers | Liste ses offres |
| POST | `/my_offers` | MyOffersController@renderMyOffers | Gère ses offres |

### Recherche
| Méthode | Route | Contrôleur | Description |
|---------|-------|-----------|-------------|
| GET | `/search_page` | SearchPageController@renderingSearchPage | Affiche recherche |
| GET | `/search_companies` | SearchPageController@searchCompanies | Recherche entreprises |

### Autres
| Méthode | Route | Contrôleur | Description |
|---------|-------|-----------|-------------|
| GET | `/legal_mentions` | LegalMentionsController@renderLegalMentions | Mentions légales |
| POST | `/delete_account` | DelaccountController@deleteAccount | Supprime compte |
| GET | `/delete_account_page` | DelaccountController@renderDeleteAccount | Formulaire suppression |

---

## 🤝 Contribution

Les contributions sont bienvenues ! Pour contribuer :

1. **Fork** le repository
2. **Créer une branche** pour votre feature (`git checkout -b feature/AmazingFeature`)
3. **Commit** vos changements (`git commit -m 'Add some AmazingFeature'`)
4. **Push** vers la branche (`git push origin feature/AmazingFeature`)
5. **Ouvrir une Pull Request**

### Directives de contribution
- ✅ Respecter le style de code existant
- ✅ Ajouter des tests pour les nouvelles features
- ✅ Mettre à jour la documentation

---

## 🗂️ Modèles de données

### Utilisateurs
```php
User {
  id: int (PK)
  email: string (unique)
  password: string (hashed)
  account_type: enum('student', 'company')
  created_at: timestamp
}
```

### Étudiants
```php
Student extends User {
  first_name: string
  last_name: string
  profile_image: string
  sector_id: int (FK)
}
```

### Entreprises
```php
Company extends User {
  company_name: string
  logo: string
  banner: string
  description: text
}
```

### Offres d'emploi
```php
Offer {
  id: int (PK)
  title: string
  description: text
  company_id: int (FK)
  sector_id: int (FK)
  created_at: timestamp
}
```

### Secteurs
```php
Sector {
  id: int (PK)
  name: string
  description: string
}
```

---

## 🐛 Dépannage

### Erreur de connexion à la base de données
```
Solution: Vérifier les identifiants dans config/config.php
          Vérifier que MySQL est en cours d'exécution
```

### Fichiers non uploadés
```
Solution: Vérifier les permissions du dossier public/images/uploads/
          chmod 755 public/images/uploads/
```

### Templates Twig non trouvés
```
Solution: Vérifier que les fichiers .twig existent dans app/views/
          Vérifier le chemin dans View.php
```

---

## 📝 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

---

## 👤 Auteur

**Geoffrey** - Développeur Web
- GitHub: [@YOUR_USERNAME](https://github.com/YOUR_USERNAME)
- Email: your.email@example.com

---

## 🙏 Remerciements

- [Twig](https://twig.symfony.com/) - Moteur de templates
- [Parsedown](https://parsedown.org/) - Parseur Markdown
- [Symfony Polyfill](https://symfony.com/) - Polyfills PHP

---

## 📞 Support

Pour toute question ou problème :
- Ouvrir une [Issue](https://github.com/YOUR_USERNAME/PokeAJob/issues)
- Consulter la [Documentation](https://github.com/YOUR_USERNAME/PokeAJob/wiki)

---

**Dernière mise à jour** : Mars 2026
