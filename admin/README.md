# Micro back-office MFP Sport

Interface PHP autonome destinée à `admin.mfpsport.com`. Elle ne contient aucune
base de données et ne modifie jamais directement le site public : chaque action
crée un commit sur la branche configurée, puis le déploiement Astro habituel prend
le relais.

## Prérequis serveur

- PHP 8.2 ou plus récent avec `curl`, `gd`, `mbstring` et `fileinfo` ;
- HTTPS actif sur le sous-domaine d’administration ;
- un dossier privé inscriptible, hors de la racine web ;
- un jeton GitHub fine-grained limité au seul dépôt MFPsport, avec la permission
  `Contents: Read and write` et aucune autre permission d’écriture.

## Configuration

1. Copier `config.example.php` vers
   `/home/mfpspou/private/mfpsport-admin.php` sur OVH.
2. Générer un mot de passe fort et son hash :
   `php -r "echo password_hash('mot-de-passe', PASSWORD_DEFAULT), PHP_EOL;"`.
3. Renseigner le compte, le dépôt, la branche, le jeton et le hash dans le fichier
   privé. Le fichier réel ne doit jamais entrer dans Git ni dans la racine web.
4. Faire pointer `admin.mfpsport.com` vers le dossier distant de l’application,
   activer le certificat SSL, puis lancer le workflow manuel « Déploiement du
   back-office ».

Le chemin de configuration par défaut convient si `admin/` et `private/` sont
deux dossiers frères. Sinon, définir la variable serveur `MFP_ADMIN_CONFIG` avec
le chemin absolu du fichier privé.

## Mesures de sécurité intégrées

- session courte, cookie `HttpOnly`, `Secure` et `SameSite=Strict` ;
- jeton CSRF sur toutes les actions ;
- ralentissement puis verrouillage progressif après plusieurs échecs de connexion ;
- validation stricte des chemins, URLs, dates et identifiants ;
- images uniquement, décodées puis ré-encodées par GD, redimensionnées à 2400 px ;
- politique CSP, interdiction des iframes, absence d’indexation et absence de cache ;
- commits atomiques et contrôle de concurrence avant une modification ou suppression.

## Recette avant ouverture à Maxime

Avant la bascule, tester sur la branche `develop` : connexion, ajout/modification/retrait d’un
professionnel, brouillon puis publication d’une actualité, remplacement d’une
photo, déconnexion, cinq mots de passe erronés et expiration de session. Vérifier
ensuite le build et la préproduction `mfpsport.fr`.

Après la bascule, utiliser la branche `main` dans la configuration. Chaque commit
du back-office déclenche alors le déploiement automatique de production, tandis
que `develop` continue d’alimenter la préproduction avant validation.
