# Bascule de production MFP Sport

## Principe retenu

La bascule ne supprime ni n’écrase le WordPress historique. Le workflow de
production envoie d’abord le site Astro dans un dossier temporaire, puis :

1. renomme `www` en `wordpress-offline` lors de la première bascule ;
2. renomme la version Astro temporaire en `www` ;
3. remplace uniquement le `.htaccess` de `preprod` par la redirection permanente
   de `mfpsport.fr` vers `mfpsport.com` ;
4. vérifie le nouveau site et la redirection.

Le déplacement de dossiers n’est effectué qu’après un upload complet. Si la
seconde opération échoue, le workflow tente immédiatement de remettre le dossier
précédent en `www`.

## Retour arrière WordPress

Depuis un accès SFTP OVH, et uniquement après avoir vérifié que les dossiers
`www` et `wordpress-offline` sont bien ceux attendus :

1. renommer `www` en `site-astro-offline` ;
2. renommer `wordpress-offline` en `www` ;
3. rétablir le `.htaccess` de préproduction si `mfpsport.fr` doit redevenir la
   préproduction ;
4. vérifier la page d’accueil et `/wp-admin/` avant de considérer le retour
   arrière terminé.

Les déploiements statiques ultérieurs conservent eux aussi la version précédente
dans un dossier `site-offline-<identifiant du workflow>`.
