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
  futur micro back-office de Maxime modifiera (§D.7 de l'étude).
- `src/components/`, `src/layouts/`, `src/styles/` — présentation, séparée du contenu.
- `ovh/` — configuration Apache à déployer sur l'hébergement (`.htaccess` prod/préprod,
  redirection définitive `.fr` → `.com`).
- `scripts/refresh-instagram.mjs` — récupère les derniers posts Instagram (§D.8).
- `.github/workflows/` — build + déploiement automatique.

## Déploiement — secrets GitHub requis

À configurer dans *Settings → Secrets and variables → Actions* du repo :

| Secret | Description |
|---|---|
| `OVH_FTP_SERVER` | Hôte FTPS de l'hébergement OVH mutualisé |
| `OVH_FTP_USERNAME` | Identifiant FTP OVH |
| `OVH_FTP_PASSWORD` | Mot de passe FTP OVH |
| `INSTAGRAM_ACCESS_TOKEN` | Jeton longue durée de l'app Meta liée au compte Instagram pro de la salle (optionnel — en son absence, le fil Instagram n'est simplement pas rafraîchi) |
| `INSTAGRAM_USER_ID` | Identifiant du compte Instagram (optionnel, idem) |

Deux branches pilotent le déploiement :

- `main` → build → dépôt dans `www/` sur OVH → **mfpsport.com**
- `develop` → build (avec balise `noindex` et auth basique) → dépôt dans `preprod/` sur
  OVH → **mfpsport.fr**

Le fichier `.htpasswd` de la préproduction (couple identifiant/mot de passe pour l'auth
Apache de `ovh/preprod.htaccess`) doit être généré séparément (`htpasswd -c`) et déposé
manuellement sur le serveur — il ne doit jamais être committé dans ce repo.
