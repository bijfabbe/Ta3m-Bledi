# Taam Bledi – Préservation des variétés agricoles tunisiennes

## Présentation

Ce projet a été développé dans le cadre du **Projet Technologies Web** à **l’École Supérieure Privée d’Ingénierie et de Technologies (Esprit)** – Tunisie, durant l’année universitaire 2026–2027.

**Ta3m Bledi** est une plateforme web permettant de découvrir, préserver, cultiver et échanger des variétés agricoles traditionnelles tunisiennes menacées par le changement climatique.

## Fonctionnalités

- Comptes utilisateurs et badges
- Musée des variétés disparues et témoignages
- Mes plantations&#x20;
- Tounes 2050&#x20;
- Échange de récoltes
- Ateliers&#x20;

## Technologies utilisées

### Frontend

- HTML5, CSS, JavaScript


### Backend

- PHP&#x20;
- MySQL
- XAMPP / Apache

## Architecture

Le projet suit le modèle **MVC (Modèle – Vue – Contrôleur)** :

- `Controllers/` : gèrent les requêtes et choisissent la vue à afficher
- `Models/` : contiennent les classes et assurent l'accès aux données
- `Views/` : contiennent les pages du Front Office et du Back Office, avec `includes/` et `partials/`

## Contributeurs

- Yasmine Neffeti
- Yomna Ghannem
- Lemis Zmander
- Meriam Guerrida
- Youssef Trabelsi
- Belhassen Ghaleb

## Contexte académique

Développé à **l’École Supérieure Privée d’Ingénierie et de Technologies (Esprit)** – Tunisie.

Projet Technologies Web | 2026–2027

## Démarrage du projet

1. Cloner le repository :

```bash
git clone https://github.com/Neff016/ta3m-bledi-project.git
```

2. Placer le projet dans le dossier `htdocs` de XAMPP.
3. Démarrer **Apache** et **MySQL** depuis le panneau de contrôle XAMPP.
4. Ouvrir dans le navigateur :

```text
http://localhost/ta3m-bledi-project//index.php
```

## Remerciements

Nous remercions nos tuteurs à **l’École Supérieure Privée d’Ingénierie et de Technologies (Esprit)** pour leur accompagnement et leurs conseils.


## Sécurité et authentification (mise à jour)

- Session PHP commune (`TA3M_BLEDI_SESSION`) afin de permettre la redirection d’un administrateur connecté du Front Office vers les pages Back Office ; les autorisations sont contrôlées par rôle côté serveur.
- Déconnexion automatique après 20 minutes d'inactivité.
- Cookies de session `HttpOnly`, `SameSite=Lax` et `Secure` lorsque HTTPS est activé.
- Renouvellement de l'identifiant de session après connexion.
- Jeton CSRF vérifié sur les requêtes POST des pages protégées.
- Protection des pages Back Office : seuls les rôles autorisés (`admin` / `farmer`) peuvent y accéder ; le rôle `user` est refusé.
- L'inscription publique depuis l'écran Back Office crée uniquement un compte `user` ; elle ne permet pas de s'attribuer le rôle `farmer` ou `admin`.
- Mot de passe : 8 caractères minimum, au moins une lettre et un chiffre pour les nouveaux comptes.

Le délai de session est défini dans `Controller/Security.php` (`TA3M_SESSION_IDLE_TIMEOUT`).


## Lancement local avec XAMPP

1. Extraire le dépôt dans `C:\xampp\htdocs\ta3m-bledi-project-main`.
2. Démarrer Apache et MySQL.
3. Importer les scripts SQL nécessaires depuis `SQL/` dans phpMyAdmin (la base utilisée est `ta3m_bledi`).
4. Ouvrir le Front Office : `http://localhost/ta3m-bledi-project-main/View/FrontOffice/index.html`.
5. Ouvrir la connexion : `http://localhost/ta3m-bledi-project-main/View/FrontOffice/connexion.php`.
6. Ouvrir le Back Office MVC : `http://localhost/ta3m-bledi-project-main/View/BackOffice/index.php?page=login`.

Attention : la récupération de mot de passe affiche actuellement un lien de démonstration pour XAMPP ; aucun serveur SMTP n'est configuré pour l'envoi réel d'e-mails.
