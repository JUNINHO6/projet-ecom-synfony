Junior Koffi Arnold ATOHOUN
# Maison Boréal

Boutique en ligne de thés et d'infusions, réalisée avec Symfony.

Prérequis

- PHP 8.4 ou plus
- [Composer](https://getcomposer.org/)
- [Symfony CLI](https://symfony.com/download) (recommandé)

La base de données par défaut est un fichier SQLite. Aucun serveur MySQL n'est nécessaire.

Récupérer le projet

git clone URL
cd dossier_projet
composer install

Dupliquer .env en .env.local. Le fichier .env contient déjà une base SQLite :

DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"

Pour utiliser MySQL ou PostgreSQL à la place, modifier DATABASE_URL dans .env.local (des exemples sont commentés dans .env).

Créer la base, les tables, puis les données de démonstration :

php bin/console doctrine:database:create
php bin/console doctrine:schema:create
php bin/console doctrine:fixtures:load --no-interaction

Il n'y a pas de migrations. doctrine:schema:create crée les tables à partir des entités.

Lancer le site

symfony serve -d

Le site est alors disponible sur http://127.0.0.1:8000.

Sans la Symfony CLI :

php -S 127.0.0.1:8000 -t public

Comptes de démonstration

Mot de passe de tous les comptes : password

| Email | Rôle |
| --- | --- |
| client@boreal.test | Client |
| admin@boreal.test | Admin |
| super@boreal.test | Super admin |

Pages

- Accueil : liste des produits. Un admin peut y ajouter un produit.
- Fiche produit : détail, ajout au panier si l'utilisateur est connecté. Un admin peut modifier ou supprimer le produit.
- Panier : panier non payé de l'utilisateur connecté, avec suppression d'une ligne et achat fictif.
- Mon compte : modification du profil et historique des commandes.
- Contenu d'une commande : détail d'une commande payée.
- Paniers non payés et inscriptions du jour : réservés au super admin.

Le menu change selon le visiteur, le client, l'admin et le super admin. ROLE_SUPER_ADMIN possède les mêmes droits que ROLE_ADMIN, plus ces deux pages.

Langues

L'interface est en français et en anglais. Le choix se fait avec les liens FR et EN du menu.
=======

