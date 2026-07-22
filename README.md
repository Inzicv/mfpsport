# MFP Sport — site vitrine

Refonte du site de MFP Sport (Arles) : Astro (statique) + contenu versionné dans ce
repo. Le document de cadrage complet est dans [`docs/etude-refonte.md`](docs/etude-refonte.md)
(architecture, arborescence, SEO, migration, roadmap) — à lire avant toute contribution.

## Développement

```sh
npm install
npm run dev        # http://localhost:4321
npm run build      # build de production dans ./dist
npm run astro check
```

## Structure

- `src/pages/` — une page par URL du site (voir `docs/etude-refonte.md` §E).
- `src/content/` — contenu administrable (professionnels, actualités, offres, infos
  pratiques, fil Instagram) ; schémas dans `src/content.config.ts`. C'est ce que le
  micro back-office de Maxime modifie (§D.7 de l'étude).
- `src/components/`, `src/layouts/`, `src/styles/` — présentation, séparée du contenu.
- `ovh/` — configuration Apache à déployer sur l'hébergement (`.htaccess` prod/préprod,
  redirection définitive `.fr` → `.com`).
- `scripts/refresh-instagram.mjs` — récupère les derniers posts Instagram (§D.8).
- `.github/workflows/` — build + déploiement automatique.
- `admin/` — micro back-office PHP pour gérer l'équipe, les actualités et les
  photos sans toucher au code ; installation et sécurité documentées dans
  [`admin/README.md`](admin/README.md).

## Déploiement — secret GitHub requis

Le déploiement se fait en **SFTP** (OVH mutualisé refuse le FTPS explicite). L'hôte
(`ftp.cluster127.hosting.ovh.net`) et l'identifiant (`mfpspou`) ne sont pas secrets et
sont écrits dans les workflows ; seul le mot de passe est un secret.

À configurer dans *Settings → Secrets and variables → Actions* du repo :

| Secret | Description |
|---|---|
| `OVH_FTP_PASSWORD` | Mot de passe du compte FTP/SFTP OVH `mfpspou` |
| `INSTAGRAM_ACCESS_TOKEN` | Jeton longue durée de l'app Meta liée au compte Instagram pro (optionnel — sans lui, le fil Instagram n'est pas rafraîchi) |
| `INSTAGRAM_USER_ID` | Identifiant du compte Instagram (optionnel, idem) |

Deux branches pilotent le déploiement :

- `develop` → build (`noindex` + auth basique) → dépôt SFTP dans `preprod/` → **mfpsport.fr**
  (automatique à chaque push).
- `main` → **déclenchement manuel uniquement** (`workflow_dispatch`) → dépôt SFTP dans `www/`
  → **mfpsport.com**. Ce dossier sert actuellement le WordPress : ne lancer ce workflow
  qu'au moment de la bascule finale, après validation en préproduction.

> ⚠️ Le multisite `mfpsport.fr` doit être mappé sur le dossier `preprod/` dans le manager
> OVH pour que la préproduction soit servie. L'upload dans `preprod/` est sans risque pour
> le WordPress de `www/` même avant ce mapping.

Le fichier `.htpasswd` de la préproduction (auth Apache de `ovh/preprod.htaccess`) doit être
généré séparément (`htpasswd -c`) et déposé manuellement sur le serveur — jamais committé.
