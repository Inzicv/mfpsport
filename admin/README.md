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

1. Créer le secret GitHub Actions `MFP_ADMIN_GITHUB_TOKEN` avec un jeton
   fine-grained limité au dépôt `Inzicv/mfpsport` et à la permission
   `Contents: Read and write`.
2. Vérifier que `OVH_ACCESS` contient le mot de passe SFTP choisi également pour
   la connexion de Maxime.
3. Faire pointer `admin.mfpsport.com` vers le dossier distant de l’application,
   activer le certificat SSL, puis lancer le workflow manuel « Déploiement du
   back-office ».

Le workflow génère le hash du mot de passe et le fichier
`/home/mfpspou/private/mfpsport-admin.php` dans son environnement temporaire,
puis le transfère hors de la racine web. Le jeton et le mot de passe ne sont
jamais affichés ni enregistrés dans Git.

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
