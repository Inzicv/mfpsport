# Étude de refonte — Site MFP Sport

> **Version 1.1 — 13 juillet 2026** *(v1.1 : points tarifaires 1, 2 et box tranchés ; back-office remplacé par un micro-admin sans compte GitHub ; ajout fil Instagram §D.8 et option assistant IA §D.9)*
> Document de cadrage : audit de l'existant, choix techniques, architecture, arborescence, parcours, direction artistique, SEO, migration et roadmap.
> Aucune ligne de code n'est produite à ce stade. Les décisions marquées ✅ sont des recommandations à valider ; les points marqués ⚠️ nécessitent une confirmation de Maxime.

---

## Sommaire

- [A. Audit de l'existant](#a-audit-de-lexistant)
- [B. Questions et informations manquantes](#b-questions-et-informations-manquantes)
- [C. Comparatif des solutions techniques](#c-comparatif-des-solutions-techniques)
- [D. Architecture recommandée](#d-architecture-recommandée)
- [E. Arborescence du site](#e-arborescence-du-site)
- [F. Parcours utilisateur](#f-parcours-utilisateur)
- [G. Direction artistique](#g-direction-artistique)
- [H. Stratégie SEO](#h-stratégie-seo)
- [I. Plan de migration](#i-plan-de-migration)
- [J. Roadmap du projet](#j-roadmap-du-projet)

---

## A. Audit de l'existant

### A.1 Méthodologie

L'audit s'appuie sur :

- l'export XML complet du site (`ressources site/mfpsport.WordPress.2026-07-13.xml`) — généré par WordPress 7.0.1 le 13/07/2026 ;
- les 603 fichiers médias de `ressources site/wp-content` ;
- les sitemaps Yoast SEO du site en production (`mfpsport.com/sitemap.xml`).

Contenu de l'export : **13 pages**, **1 article de blog**, **140 médias déclarés**, **53 templates Elementor**, 7 éléments de menu, 2 flux « Feed Them Social » (widget Instagram/Facebook).

### A.2 Arborescence actuelle (production)

| URL | Page | Statut | Rôle réel |
|---|---|---|---|
| `/` | Home | publiée | Accueil (slug interne : `mfp-sport-salle-de-sport-coaching-a-arles`) |
| `/notre-centre/` | Notre Centre | publiée | Présentation des 4 pôles |
| `/entrainement-sport-preparation-physique-arles/` | Entrainement | publiée | Pôle 1 : équipements + Slim Sonic + coach |
| `/nutrition-sport-arles/` | Nutrition | publiée | Pôle 2 : diététiciennes + conseils |
| `/recuperation/` | Récupération | publiée | Pôle 3 : cryo, presso, ostéo, kiné + praticiens |
| `/mental/` | Mental | publiée | Pôle 4 : préparation mentale (Tony Rousselot) |
| `/tarifs/` | Tarifs | publiée | Formules et packs |
| `/equipes/` | Equipes/pros | publiée | Offre B2B équipes/clubs |
| `/pros/` | Praticiens | publiée | ⚠️ **Page de recrutement de praticiens** (box à louer), pas une présentation de l'équipe |
| `/hall-of-fame/` | Hall Of Fame | publiée | Témoignages + galerie + news |
| `/contact/` | Contact | publiée | Formulaire + inscription |
| `/politique-de-confidentialite/` | Politique de confidentialité | publiée | RGPD |
| `/coach-maxime/` | Coach-Maxime | **privée** | Fiche de Maxime (non visible publiquement, contient du lorem ipsum) |
| `/ouverture/` | Ouverture le 9 Septembre ! | publiée | Unique article de blog (sept. 2024) |

### A.3 Contenus extraits (source pour le nouveau site)

**Les professionnels identifiés dans les contenus :**

| Nom | Rôle | Éléments biographiques disponibles |
|---|---|---|
| **Maxime Favier** | Directeur du centre, préparateur physique | Arlésien, 15 ans raseteur au plus haut niveau, BPJEPS (2015), formation Préparateur physique pour sportifs de haut niveau (CREPS Montpellier), FF de Course Camarguaise (suivi médico-sportif), Académie de Tennis du Pays d'Arles, Arles Youth Ballet Company, 3 ans au centre de kiné Neos Santé (réathlétisation) |
| **Alexandra Mlodzinski** | Diététicienne spécialisée nutrition du sportif | Reconversion, suivis de 1 à 6 mois, analyse composition corporelle Z-Métrix, pratique CrossFit et haltérophilie |
| **Charles Louvet** | Élève diététicien (stage BTS) | ⚠️ Contenu daté (stage 2024) — présence actuelle à confirmer |
| **Tony Rousselot** | Préparateur mental neuro-psychologique | 3 ans d'expérience, tronc commun BEES |
| **Elliot Mas** | Ostéopathe D.O. | Cabinet à Tarascon, présence le lundi après-midi, interventions sur compétitions (endurance moto, trail, DH) |
| **Anaïs Allard** | Praticienne massage bien-être et récupération | Certifiée, massages intégrés aux programmes de préparation/récupération |

**Les équipements documentés :**

- *Musculation* : Half Rack WIIT Pro, cage cross-training 3 stations, Kieser fonctionnel, barres Bamboo BANDBELL, sac de frappe, plyo box, corde ondulatoire.
- *Cardio* : SkiErg et RowErg Concept 2, Wattbike Pro, vélos spinning M3i, elliptiques Sparck.
- *Haute performance* : VertiMax, poulies magnétiques Voltra, Battle4Run The Crab, BlazePods, K Box4.
- *Mesure et tests* : capteurs Kinvent, encodeurs Vitruve.
- *Récupération* : bains froids + Cryo-Tank, bain chaud, pressothérapie Normatec.
- *Autres* : ceinture Slim Sonic (ultrasons basse fréquence).
- *Locaux* : ~300 m² en sol souple, piste d'athlétisme de 24 m, zone fonctionnelle de 57 m² en pelouse synthétique, **trois box praticiens** (deux de 14 m², un de 10 m²) — ✅ confirmé le 13/07/2026 (la page Mental qui en mentionnait 4 était erronée).
- *Horaires mentionnés* : 6 j/7, 8 h–20 h en semaine, 8 h–13 h le samedi (page Praticiens — à confirmer comme horaires actuels).

**Références clientèle citées** (fort potentiel de réassurance) : Arles Youth Ballet Company (30 danseurs, 18 nationalités), Académie de Tennis du Pays d'Arles, rugbymen du club local, artistes de cirque, boxeurs, marathoniens, golfeurs.

### A.4 Points forts de l'existant

1. **Un positionnement déjà structuré autour des « 4 pôles »** (Entraînement, Nutrition, Récupération, Mental) — c'est un vrai différenciateur, cohérent et réutilisable tel quel.
2. **Des contenus biographiques authentiques et crédibles** (parcours de Maxime, fiches praticiens à la première personne) — exactement le ton « humain » recherché.
3. **Un inventaire d'équipements précis et haut de gamme** — rare pour une salle indépendante, très exploitable en SEO et en réassurance.
4. **Des références clientèle concrètes et locales.**
5. **Un fonds photographique réel** (603 fichiers) : vraies photos de la salle, des équipements et des séances — pas de banques d'images.
6. **Yoast SEO installé** : les sitemaps existent, les URLs sont propres, deux slugs sont déjà localisés (`...-arles`).

### A.5 Points faibles

1. **Aucune page publique présentant l'équipe** : la fiche de Maxime est *privée* (avec du lorem ipsum), les praticiens sont éparpillés sur les pages de pôles, et `/pros/` est en réalité une annonce de recrutement. Un visiteur ne peut pas répondre à « qui va s'occuper de moi ? ».
2. **Aucun parcours de conversion** : pas de page ni de bloc dédié à la séance d'essai gratuite, CTA hétérogènes (« Découvrez », « Prendre RDV », « Reserve ta séance », « Rejoins la team », « S'inscrire », « Inscrivez-vous »).
3. **Page Tarifs pauvre en texte** : les tarifs sont essentiellement des images/tableaux Elementor, invisibles pour Google et peu explicites pour le visiteur.
4. **SEO quasi absent** : un seul article de blog (l'ouverture, sept. 2024), pas de contenu local structuré, pas de données structurées métier, titres Hn incohérents (fragments de phrases éclatés en plusieurs widgets).
5. **Mentions légales absentes** : seule la politique de confidentialité existe. ⚠️ Les mentions légales sont **obligatoires** (LCEN) — à créer.
6. **Contenus résiduels de template** : lorem ipsum sur `/coach-maxime/`, titres anglais (« OUR NEWS », « LATEST BLOG POSTS »), « Hall Of Fame » au titre anglophone.
7. **Dépendances fragiles** : Elementor Pro (payant), Feed Them Social pour Instagram, formulaires non identifiés.
8. **Incohérences éditoriales** : nombre de box (3 vs 4), Charles Louvet probablement parti, fautes (« Rejoingnez-Nous », « acc*è*s sur le développement »).
9. **Allégation publicitaire risquée** : « −3 cm de tour de taille garanti ou remboursé » (Slim Sonic). ⚠️ Ce type de garantie chiffrée sur un dispositif « minceur » est juridiquement sensible (pratiques commerciales trompeuses) — à reformuler ou à encadrer par des conditions écrites.
10. **Politique de confidentialité inachevée** : contient encore le placeholder « [votre email de contact] ».

### A.6 Devenir des contenus

| Contenu actuel | Décision | Justification |
|---|---|---|
| Concept « 4 pôles / 4 piliers » | **Conserver** | Colonne vertébrale du positionnement |
| Bios des professionnels | **Conserver + enrichir** | Regrouper sur une page Équipe publique ; ajouter photos, diplômes, spécialités |
| Inventaire équipements | **Conserver + restructurer** | Réécrire en bénéfices client, pas en catalogue |
| Page Récupération (cryo/presso/ostéo/kiné) | **Conserver + réécrire** | Bon fond, à enrichir pour le SEO (pressothérapie, bain chaud/froid) |
| Page Équipes/pros | **Conserver + professionnaliser** | Base correcte, à renforcer (installations, privatisation, formulaire devis) |
| Page Tarifs | **Réécrire entièrement** | Passer d'images à du HTML structuré + explications + « idéal pour » |
| Page Contact | **Réécrire** | Ajouter NAP complet, carte, horaires, accès |
| Hall Of Fame | **Fusionner** | Témoignages → accueil + pages de pôles ; galerie → page Le centre ; news → Actualités |
| `/pros/` (recrutement praticiens) | **Conserver à part** | Page utile mais B2B interne : la sortir du parcours visiteur (footer), la renommer |
| `/coach-maxime/` (privée) | **Supprimer** | Contenu repris dans la fiche Équipe de Maxime |
| Article « Ouverture » | **Conserver** | Premier élément des Actualités (valeur historique) |
| Flux `fts` (Instagram) | **Supprimer** | Remplacer par liens sociaux + galerie statique (pas d'API fragile) |
| Politique de confidentialité | **Réécrire** | Compléter les placeholders, adapter au nouveau site |
| Mentions légales | **Créer** | Obligation légale, absente aujourd'hui |
| Templates Elementor (53) | **Abandonner** | Sans objet hors WordPress |

### A.7 Ressources médias

- 603 fichiers, mais l'essentiel est constitué de **déclinaisons de tailles WordPress** (`-150x150`, `-300x200`…) : le fonds utile réel est d'environ **100–130 originaux**.
- Formats : JPG/JPEG (499), PNG (79), WebP (17), vidéos MOV/MP4 (5), SVG (3).
- ⚠️ Noms de fichiers majoritairement non descriptifs (`IMG_7712.jpeg`, UUID, captures d'écran) : un **renommage SEO systématique** sera nécessaire (ex. `preparation-physique-vertimax-arles.webp`).
- Le logo existe en plusieurs déclinaisons propres (`logo_MFP_valide*`, version inversée `logo_MFP-inv*`).

---

## B. Questions et informations manquantes

### B.1 Incohérences commerciales — points tranchés et points restants

**✅ Tranchés le 13/07/2026 :**

1. **Packs Bronze / Argent** : le Bronze (149 €/2 mois) comprend bien **2 séances de coaching par mois** ; l'Argent (259 €/2 mois) comprend **1 séance de coaching par semaine** (et non par mois — coquille de l'offre initiale). La progression Bronze (≈ 4 séances) → Argent (≈ 8 séances + récupération 2×/mois) → Or (≈ 16 séances + nutrition + récupération illimitée) est désormais cohérente.
2. **Séance de coaching supplémentaire** : **40 € pour tous les abonnés** (le « 50 € » de l'abonnement 6 mois était une coquille).
3. **Nombre de box praticiens** : **3** (deux de 14 m², un de 10 m²).

**⚠️ Restants à confirmer par Maxime (bloquant avant publication de la page Tarifs) :**

4. **Option coaching de la formule 1 mois à 75 €** alors qu'elle vaut 40 € pour les abonnés : écart important à confirmer (et à justifier sur la page si volontaire).
5. **« Valeur d'une séance » variable** entre packs (75 € / 65 € / 52,50 €) : quelle est la logique à afficher ? (dégressivité au volume ? types de séances différents ?)
6. **Séance d'essai gratuite vs Bilan corporel complet à 120 €** : les deux comprennent une « évaluation ». Il faut une distinction claire et affichable (ex. essai = découverte + mini-bilan ; bilan complet = batterie de tests instrumentés Kinvent/Z-Métrix + restitution écrite) — sinon la gratuité de l'un cannibalise ou décrédibilise l'autre.
7. **Garantie « −3 cm de tour de taille garanti ou remboursé »** (Slim Sonic) : conditions écrites ? durée ? On recommande une reformulation prudente ; en l'état, l'allégation est risquée.
8. **Cours collectifs** : les prix (5/10/20 séances à 22/20/15 €) sont cohérents entre eux ✅, mais quels cours, quels créneaux, quelle durée de validité des packs ?

### B.2 Indispensable avant le développement

| Élément | Statut |
|---|---|
| Hébergement | ✅ Confirmé : OVH mutualisé (100 Go, BDD 1 Go, déploiement Git possible) |
| Adresse | ✅ 15 Chem. des Moines, 13200 Arles |
| Zone de chalandise | ✅ 18 communes (Arles, Alpilles, Crau, Tarascon/Beaucaire, Saint-Gilles…) |
| Liens Sportigo (espace client, achat, widget planning) | ✅ Fournis (clé widget : `fbdc1b68-…5045c6`) |
| Accès OVH (manager) + accès DNS des deux domaines | ❌ À réunir pour la préprod et la bascule |
| Back-office | ✅ Contrainte actée : **aucun compte GitHub pour Maxime**, interface ultra-simple (voir §C/§D) |
| Compte Instagram de la salle en compte **professionnel** + accès | ❌ Requis pour l'intégration automatique du fil (voir §D.8) |
| Numéro de téléphone, e-mail public, horaires actuels | ❌ À confirmer (les horaires trouvés datent de 2024) |
| SIRET / forme juridique / responsable de publication | ❌ Indispensable pour les mentions légales |

### B.3 Nécessaire avant la rédaction finale

- Confirmation de la liste **actuelle** des professionnels (Charles Louvet ? kinésithérapeute effectivement présent ? nouveaux arrivants ?) + photos portrait de chacun.
- Réponses aux 8 points tarifaires ci-dessus.
- 3 à 6 **témoignages réels** nominatifs (avec accord écrit) — la section « Ils parlent de nous » actuelle est vide de contenu réel dans l'export.
- Choix des photos « héro » (accueil, pôles) parmi le fonds existant ; identification d'éventuels manques (photos récupération, accueil, vestiaires ?).
- Détail des protocoles de récupération inclus dans les abonnements (que recouvre exactement « un protocole de récupération par mois » ?).
- Modalités de la séance d'essai (durée, sur RDV via Sportigo ? quel lien exact ?).

### B.4 Améliorations pouvant venir après le lancement

- Fiches détaillées par discipline accueillie (danse, tennis, rugby, course camarguaise…).
- Articles de fond SEO (récupération, reprise du sport, préparation physique).
- FAQ structurée (schema FAQPage).
- Témoignages vidéo.
- Version anglaise éventuelle (clientèle internationale du ballet / tourisme sportif).

---

## C. Comparatif des solutions techniques

### C.0 Cadre de décision

Contraintes déterminantes : **0 € de coût récurrent additionnel**, hébergement **OVH mutualisé conservé** (PHP natif, pas de Node.js en production fiable sur cette gamme), code versionné GitHub, préprod sur `mfpsport.fr`, site vitrine ~15 pages + 2 collections (actualités, professionnels), intégration du **fil Instagram** de la salle.

Contrainte back-office renforcée (13/07/2026) : **aucun compte GitHub pour Maxime** et une interface **encore plus simple que WordPress**. Le périmètre réel de Maxime est étroit : gérer les membres de l'équipe (ajouter / modifier / supprimer), remplacer une photo, publier une actualité, corriger une info pratique. Il n'a besoin ni d'un éditeur de pages, ni d'une médiathèque complète, ni de réglages — chaque écran en plus est un risque d'erreur en plus.

Écartées d'office (avec justification) :

- **WordPress sans Elementor** : exclu par le brief (dépendance WP, maintenance sécurité, BDD).
- **CMS headless SaaS (Contentful, Sanity, Strapi Cloud, DatoCMS)** : les offres « gratuites » sont des quotas d'appel/entrées susceptibles d'évoluer — contraire à la règle « pas de gratuit sous quota fragile ». De plus, dépendance externe forte pour un besoin très simple.
- **Next.js / Nuxt SSR** : nécessite un runtime Node en production (impossible proprement sur mutualisé OVH sans surcoût) ; surdimensionné pour un site vitrine.

Trois architectures réalistes comparées :

### C.1 Option 1 — Astro (statique) + contenu Git + micro back-office dédié sur OVH ✅ recommandée

Le site reste 100 % statique et versionné ; Maxime passe par une **petite interface sur mesure** (`admin.mfpsport.com`), hébergée sur l'OVH existant, limitée à ses cas d'usage réels. Elle se connecte avec un simple **identifiant + mot de passe** (aucun compte GitHub) et enregistre chaque modification en écrivant dans le repo GitHub via l'API (jeton serveur détenu par vous, invisible pour Maxime).

| Critère | Détail |
|---|---|
| Frontend | **Astro** : générateur de site statique, HTML pur par défaut (0 JS inutile), composants, très bon SEO/Core Web Vitals natifs |
| Backend / CMS | **Micro back-office PHP sur mesure** (~4 écrans : Équipe, Actualités, Photos, Infos pratiques) hébergé sur l'OVH ; il écrit les fichiers de contenu dans le repo GitHub via l'API (commit signé « Maxime ») |
| Base de données | **Aucune** — le contenu est constitué de fichiers versionnés (la BDD OVH reste inutilisée) |
| Images | Téléversées via le back-office (recadrage/compression côté navigateur), stockées dans le repo ; optimisation automatique au build (`astro:assets` → WebP/AVIF responsive) |
| Auth back-office | Identifiant + mot de passe (hachage fort, anti-bruteforce, session) ; le jeton GitHub reste côté serveur, hors webroot |
| Hébergement | OVH mutualisé existant : le site produit est un dossier de fichiers HTML/CSS/JS/images |
| Déploiement | GitHub Actions (gratuit pour repo public ; 2 000 min/mois pour repo privé — un build ≈ 1–2 min) : build Astro → dépôt SFTP sur OVH. Branche `main` → mfpsport.com, branche `develop` → préprod mfpsport.fr |
| Sauvegardes | Git = historique complet de chaque contenu et de chaque image ; chaque action de Maxime est un commit annulable en un clic |
| Performances | Excellentes par nature (statique) : LCP < 1,5 s atteignable sur mutualisé |
| SEO | Contrôle total du HTML (Hn, meta, Schema.org, sitemap généré au build) |
| Facilité pour Maxime | **Maximale — c'est le but de l'option** : il ne voit que ses 4 cas d'usage, sous forme de formulaires (« Ajouter un membre », « Remplacer cette photo »…). Aucune notion de page, de thème, de plugin ou de mise à jour. Publication effective en ~2–3 min avec message « en ligne dans quelques minutes » |
| Complexité de développement | Moyenne : templating Astro + une petite application PHP (~400 lignes, zéro dépendance) + workflow CI |
| Complexité de maintenance | Faible : le back-office est minuscule, sans framework ni base ; à réviser à chaque évolution du modèle de contenu. En cas de panne, repli immédiat : vous éditez le repo directement, le site public n'est jamais affecté |
| Coût immédiat | 0 € |
| Coût long terme | 0 € (GitHub Actions : quota largement suffisant ; sinon repo public ou build local en secours) |
| Risque de dépendance | Très faible : contenu = fichiers texte dans votre repo ; le back-office est votre code, remplaçable sans toucher au contenu |

**Avantages** : la seule option qui satisfait « plus simple que WordPress » à la lettre ; coût nul ; sécurité maximale côté site public (statique) ; surface d'attaque du back-office minime (4 formulaires, pas de plugins) et dégâts bornés (au pire, des commits de contenu — tous réversibles via Git) ; sauvegarde/restauration triviales ; contenu pérenne et portable.
**Inconvénients** : publication non instantanée (~2–3 min) ; c'est du code maison — assumé parce que le périmètre est volontairement minuscule et gelé (ce n'est **pas** un « faux WordPress » : pas d'éditeur de pages, pas de médiathèque générale, pas de réglages) ; toute nouvelle capacité d'édition demande une petite évolution du back-office.

> **Extension prévue (votre idée d'agent)** : cette architecture est exactement celle qui permet d'ajouter plus tard un **assistant IA** pour Maxime — voir §D.9. Les formulaires restent le canal principal (déterministes, instantanés à comprendre) ; l'agent devient le canal « demande libre » pour tout ce qui sort des 4 écrans.

### C.2 Option 2 — Astro (statique) + CMS git-based (Sveltia / Pages CMS)

Même frontend et même déploiement que l'option 1 ; l'édition passe par un CMS open source générique (Sveltia CMS auto-configuré, ou le service gratuit Pages CMS) branché sur le repo.

| Critère | Détail (différences vs option 1) |
|---|---|
| Backend / CMS | Sveltia CMS (fichiers statiques + passerelle OAuth PHP sur l'OVH) ou Pages CMS (service hébergé gratuit) |
| Auth back-office | **Connexion via compte GitHub obligatoire** — c'est le principe même de ces outils |
| Facilité pour Maxime | Bonne, mais interface générique (collections, brouillons, médiathèque) plus riche que nécessaire |
| Complexité de développement | La plus faible (rien à développer côté admin) |
| Coût | 0 € |
| Risque de dépendance | Faible (Sveltia) à moyen (Pages CMS, service tiers gratuit « sous conditions ») ; le contenu reste dans le repo dans tous les cas |

**Avantages** : zéro code d'admin à écrire ni à maintenir ; outils éprouvés.
**Inconvénients rédhibitoires ici** : **impossible sans compte GitHub pour l'éditeur** — contrainte explicitement refusée ; et l'interface, quoique correcte, reste plus complexe que le besoin réel de Maxime. Option conservée comme **solution de repli documentée** : si un jour le micro back-office devenait un fardeau, une bascule vers Sveltia se fait en une demi-journée sans toucher au contenu.

### C.3 Option 3 — Grav CMS (flat-file PHP, tout sur OVH)

| Critère | Détail |
|---|---|
| Frontend | Templates Twig rendus par PHP à chaque requête (avec cache) |
| Backend / CMS | **Grav + plugin Admin** (open source, gratuit) : back-office PHP complet hébergé sur l'OVH |
| Base de données | Aucune (fichiers Markdown/YAML) |
| Images | Uploads via l'admin, redimensionnement PHP à la volée |
| Auth back-office | Comptes locaux Grav (login/mot de passe + 2FA plugin) |
| Déploiement | Git possible (Grav est versionnable), mais uploads et config vivent sur le serveur → synchronisation bidirectionnelle plus délicate |
| Sauvegardes | Plugin de backup + copie du dossier ; moins naturel que Git pur |
| Performances | Bonnes avec cache, mais inférieures au statique sur mutualisé ; PHP exécuté à chaque visite |
| SEO | Bon (contrôle des templates), plugins sitemap/meta |
| Facilité pour Maxime | Bonne (admin visuel, publication instantanée) mais interface CMS complète — **plus complexe que WordPress simplifié, pas moins** |
| Maintenance | **Moyenne+** : c'est une application PHP exposée publiquement → mises à jour de sécurité régulières de Grav, de l'admin et des plugins (le travers WordPress à échelle réduite) |
| Coût | 0 € |
| Risque de dépendance | Faible (open source, contenu en fichiers) mais couplage fort front/back |

**Avantages** : tout vit sur l'OVH, publication instantanée, pas de GitHub dans la boucle d'édition.
**Inconvénients** : on réintroduit exactement ce qu'on quitte — une application serveur complète à maintenir et sécuriser, un admin générique exposé au web, un couplage contenu/thème ; performances et Core Web Vitals moins bons ; le versioning Git devient partiel ; et l'admin Grav ne remplit pas la contrainte « encore plus simple que WordPress ».

### C.4 Recommandation

**Option 1 — Astro + contenu Git + micro back-office dédié**, avec l'option 2 (Sveltia) comme repli documenté.

Argumentaire :

1. **C'est la seule option qui satisfait littéralement le besoin exprimé** : pas de compte GitHub, pas de concepts CMS — quatre écrans-formulaires qui correspondent aux quatre gestes réels de Maxime. La simplicité vient de la réduction du périmètre, pas d'un habillage.
2. **Rien à attaquer ni à maintenir côté site public** : le site est un dossier de fichiers statiques. Le seul code serveur est un back-office de ~400 lignes sans dépendance, aux dégâts intrinsèquement bornés (des commits de contenu, tous réversibles).
3. **Coût récurrent : zéro, sans astérisque.** Tous les composants sont open source ou déjà payés (OVH, domaines). Aucun quota critique.
4. **La sauvegarde est un non-sujet** : chaque modification de Maxime est un commit horodaté et signé ; restaurer = redéployer.
5. **Le SEO et les performances sont structurellement optimaux** (HTML statique maîtrisé), là où Grav demanderait du réglage et WordPress des plugins.
6. **La réversibilité est totale** : contenu en Markdown lisible ; le back-office, le CMS ou même le générateur peuvent changer sans rien détruire.
7. **C'est le socle naturel de l'agent envisagé** (§D.9) : contenu en fichiers + API GitHub + CI = exactement l'environnement dans lequel un agent IA opère de façon sûre et auditable.
8. Le seul vrai compromis — publication en 2–3 minutes au lieu d'instantanée — est sans enjeu pour un site vitrine dont le contenu change quelques fois par mois.

---

## D. Architecture recommandée

### D.1 Vue d'ensemble

```
┌────────────────┐  login simple  ┌───────────────────────────────┐
│    Maxime      │ ─────────────▶ │  Micro back-office (PHP, OVH) │
└────────────────┘                │  admin.mfpsport.com           │
                                  │  Équipe · Actus · Photos ·    │
                                  │  Infos · [Demande libre → IA] │
                                  └───────────┬───────────────────┘
                                              │ commits via API GitHub
                                              │ (jeton serveur, invisible pour Maxime)
                                              ▼
                                  ┌──────────────────────────┐
                                  │   GitHub — Inzicv/mfpsport│◀── cron hebdo :
                                  │  main ──────── develop    │    fil Instagram
                                  └──────┬────────────┬──────┘    rafraîchi au build
                                         │ build Astro │ build Astro (noindex)
                                         ▼             ▼
                                  ┌───────────┐  ┌─────────────┐
                                  │ SFTP OVH  │  │  SFTP OVH   │
                                  │ www/      │  │  preprod/   │
                                  │ mfpsport  │  │ mfpsport.fr │
                                  │ .com      │  │ (auth+noidx)│
                                  └─────┬─────┘  └─────────────┘
                                        │
                             visiteur ──┘──▶ CTA / widget planning ──▶ Sportigo
                                             (réservation, achat, espace client)
```

Échanges avec Sportigo — trois points de contact, tous côté client, aucun couplage serveur :

1. **Widget planning** intégré sur la page Planning (et en bas de Tarifs) via le script standalone fourni (`initComponent("Appointment", …)`), chargé en différé pour ne pas dégrader les Core Web Vitals.
2. **Liens sortants** : « Réserver mon bilan gratuit » et boutons d'achat → `https://mfpsport.sportigo.fr/buy` ; « Espace client » (header/footer) → `https://mfpsport.sportigo.club/public/auth/login`.
3. Aucun échange de données entrant : le site reste 100 % statique.

### D.2 Arborescence technique du repo

```
mfpsport/
├── .github/workflows/
│   ├── deploy-prod.yml        # main → build → SFTP OVH (www/)
│   ├── deploy-preprod.yml     # develop → build (noindex) → SFTP OVH (preprod/)
│   └── refresh-instagram.yml  # cron hebdo : fil Instagram + rebuild
├── docs/                      # ce document + décisions (ADR)
├── ressources site/           # archives WordPress (hors build)
├── admin/                     # micro back-office PHP (déployé sur admin.mfpsport.com,
│   │                          #   HORS du site public)
│   ├── index.php              # login + tableau de bord (4 tuiles)
│   ├── equipe.php             # CRUD professionnels
│   ├── actus.php              # CRUD actualités
│   ├── photos.php             # remplacement des photos à emplacements nommés
│   ├── infos.php              # horaires, téléphone, bandeau
│   └── lib/github.php         # écriture dans le repo via l'API (jeton hors webroot)
├── public/
│   ├── robots.txt
│   └── favicon, manifest…
├── src/
│   ├── components/            # Header, Footer, CTA, CarteTarif, FichePro…
│   ├── layouts/               # Layout de base (SEO, OG, Schema.org)
│   ├── pages/                 # 1 fichier = 1 URL
│   ├── content/               # ← LE CONTENU ADMINISTRABLE
│   │   ├── professionnels/    #   1 fichier .md par professionnel
│   │   ├── actualites/        #   1 fichier .md par actualité
│   │   ├── offres/            #   1 fichier .md par formule/pack
│   │   ├── infos/             #   infos-pratiques.json, temoignages.json…
│   │   └── instagram/         #   derniers posts (JSON + images), écrit par le cron
│   ├── assets/                # images optimisées au build
│   └── styles/
├── ovh/
│   └── .htaccess              # redirections 301, cache, sécurité
└── astro.config.mjs, package.json…
```

### D.3 Modèle de données (collections administrables par Maxime)

| Collection | Champs | Utilisation |
|---|---|---|
| **Professionnel** | nom, rôle/titre, photo, bio, diplômes/certifications (liste), spécialités (liste), jours de présence, lien de prise de RDV (Sportigo ou externe), ordre d'affichage, actif (oui/non) | Page Équipe + encarts sur les pages de pôles |
| **Actualité** | titre, date, image de couverture, chapô, corps (texte riche), brouillon/publié | Liste + pages détail `/actualites/…` |
| **Offre** | nom, catégorie (abonnement / cours collectifs / préparation individuelle / prestation), prix, unité (mois, pack…), durée/engagement, liste des inclusions, « idéal pour », lien d'achat Sportigo, mise en avant (oui/non), ordre | Page Tarifs (cartes générées) |
| **Infos pratiques** (fichier unique) | adresse, téléphone, e-mail, horaires par jour, liens réseaux sociaux, lien espace client, texte bandeau éventuel | Footer, page Contact, Schema.org LocalBusiness |
| **Témoignages** (fichier unique ou collection) | auteur, discipline/profil, texte, photo (optionnelle), note | Accueil + pages de pôles |

Textes de pages « stables » (accueil, pôles, B2B) : gérés en fichiers de contenu également, mais **hors du périmètre du back-office de Maxime** (son besoin réel se limite à l'équipe, aux photos, aux actualités et aux infos pratiques). Ces textes sont modifiés par vous directement dans le repo — ou, plus tard, via l'assistant IA (§D.9) sur demande libre de Maxime.

### D.4 Gestion des images

- Téléversement via le back-office → l'image entre dans le repo (`src/assets/uploads/`), recadrée/compressée côté navigateur avant envoi.
- Au build, Astro génère automatiquement les variantes responsive WebP/AVIF avec `width/height` (zéro CLS) et lazy-loading.
- Convention de nommage SEO imposée à la migration (`{sujet}-{lieu}.{ext}`) ; champ « texte alternatif » obligatoire dans les formulaires du back-office.
- Poids du repo maîtrisé : seuls les ~100–130 originaux utiles sont migrés, recompressés (< 400 Ko chacun) ; les milliers de miniatures WordPress ne sont pas reprises.

### D.5 Redirections et configuration serveur

- Fichier `ovh/.htaccess` versionné, déployé avec le site : table de 301 (voir §H.8), HTTPS forcé, en-têtes de cache longs pour les assets fingerprintés, en-têtes de sécurité (CSP adaptée au widget Sportigo, X-Content-Type-Options…).
- `mfpsport.fr` (préprod) : `.htaccess` spécifique avec auth basique + `X-Robots-Tag: noindex` + meta noindex dans le build préprod.
- Au lancement : le multisite OVH `mfpsport.fr` passe en **redirection 301 domaine → domaine** vers `mfpsport.com`.

### D.6 Stratégie de sauvegarde et restauration

| Actif | Sauvegarde | Restauration |
|---|---|---|
| Contenus + images + code | Git (GitHub) — historique intégral | `git revert` ou redéploiement d'un commit antérieur (5 min) |
| Site en production | Régénérable à 100 % depuis le repo | Relancer le workflow de déploiement |
| Ancien site WordPress | Export XML + `wp-content` déjà archivés dans le repo ; **⚠️ faire une sauvegarde complète OVH (fichiers + BDD) avant toute bascule** | Ré-import WordPress (plan de secours pendant 3 mois) |
| Config OVH/DNS | Documentée dans `docs/` | Manuel, guidé par la doc |

### D.7 Le micro back-office de Maxime en détail

Objectif d'expérience : **ouvrir une page, cliquer une tuile, remplir un formulaire, valider.** Rien d'autre n'existe.

- **Accès** : `admin.mfpsport.com` (sous-domaine OVH, HTTPS, hors du site public, `noindex` + interdit dans robots.txt). Identifiant + mot de passe fort ; verrouillage progressif après échecs ; session courte.
- **Tableau de bord = 4 tuiles** :
  1. **L'équipe** — liste des professionnels en cartes avec photo ; boutons *Ajouter*, *Modifier*, *Retirer*. Formulaire : nom, rôle, photo, bio, diplômes, jours de présence, lien RDV.
  2. **Les actualités** — liste antichronologique ; *Écrire une actualité* (titre, photo, texte), *Modifier*, *Supprimer*.
  3. **Les photos du site** — les emplacements de photos sont **nommés et illustrés** (« Photo d'accueil », « Photo pôle Récupération »…) : Maxime voit la photo actuelle et clique *Remplacer*. Impossible de casser une mise en page.
  4. **Infos pratiques** — horaires, téléphone, bandeau d'annonce éventuel (« fermeture exceptionnelle… »).
- **Mécanique invisible** : chaque validation devient un commit GitHub (`Maxime : ajout d'Elliot à l'équipe`) via un jeton *fine-grained* limité au repo, stocké côté serveur hors webroot. Le build se déclenche, l'écran affiche « ✔ Enregistré — en ligne dans ~3 minutes ».
- **Sécurité et réversibilité** : CSRF, validation stricte des entrées, aucun exécutable téléversable (images uniquement, ré-encodées) ; en cas de compromission du back-office, l'attaquant ne peut *que* committer du contenu — chaque commit est notifié et réversible en un clic. Le site public, statique, reste inattaquable par ce canal.
- **Repli** : si le back-office est indisponible, le site public fonctionne normalement et vous gardez la main via le repo.

### D.8 Intégration du fil Instagram

Besoin confirmé : afficher le fil Instagram de la salle sur le site. Trois voies existent ; recommandation en premier :

1. ✅ **Récupération au build (recommandée)** : un workflow GitHub Actions planifié (cron, ex. 2×/semaine) interroge l'API Instagram du compte de la salle, enregistre les N derniers posts (images + légendes + liens) dans `src/content/instagram/`, puis reconstruit le site.
   - *Avantages* : affichage **statique et auto-hébergé** → aucune dégradation de performance, aucun script tiers, aucune bannière de consentement nécessaire pour l'afficher, et si l'API tombe en panne le site garde simplement les derniers posts connus (dégradation gracieuse).
   - *Prérequis* : le compte Instagram de la salle en **compte professionnel**, et la création d'une petite app Meta (gratuite) ; le jeton longue durée (60 jours) est rafraîchi automatiquement par le même workflow.
   - *Limite honnête* : dépendance aux règles de l'API Meta, qui évoluent régulièrement — d'où l'importance de la dégradation gracieuse et de l'alternative 2 en secours.
2. **Embeds officiels de posts choisis** : coller l'embed officiel de 2–3 posts marquants (gratuit, sans app Meta), mis à jour manuellement — simple mais non automatique.
3. **Widgets SaaS (Elfsight, SnapWidget…)** : ❌ écartés — versions gratuites avec badge publicitaire, script tiers lent, cookies tiers (bannière de consentement requise).

Emplacement : section « La vie du centre » sur l'accueil (grille de 4–8 posts, lien « Suivre @mfpsport ») et/ou sur `/actualites/`.

### D.9 Option d'évolution — l'assistant IA de Maxime (votre idée d'agent)

L'idée d'un agent qui exécute les demandes de Maxime (« retire Charles de l'équipe », « remplace la photo d'accueil par celle-ci ») est **réaliste et bien servie par cette architecture**, précisément parce que le contenu est en fichiers versionnés : un agent peut agir de façon bornée et 100 % auditable/réversible.

- **Positionnement recommandé** : les 4 formulaires restent le canal principal (déterministes, immédiats). L'agent est le canal « **demande libre** » — une 5ᵉ tuile avec un champ texte + envoi de fichiers — pour tout ce qui déborde des formulaires (« corrige le texte de la page nutrition », « ajoute les horaires d'été »).
- **Mécanique proposée** : la demande crée une issue GitHub → un workflow **Claude Code GitHub Action** traite la demande dans le repo → ouvre une *pull request* avec aperçu en préprod → validation en un clic (par vous au début, auto-approbation possible plus tard pour les changements limités à `src/content/`). Maxime n'interagit jamais avec GitHub : il écrit sa demande et reçoit un e-mail « c'est en ligne ».
- **Coûts, honnêtement** : ce n'est pas gratuit en absolu — l'exécution consomme votre abonnement Claude existant (jeton d'abonnement utilisable par l'Action) ou des crédits API (ordre de grandeur : quelques centimes par demande). Aucun coût fixe.
- **Garde-fous indispensables** : périmètre d'écriture restreint aux dossiers de contenu, revue humaine au début, tarifs exclus du périmètre de l'agent (règle « ne jamais modifier un tarif silencieusement »).
- **Calendrier** : à construire **après** le lancement (phase 12+), une fois le socle stable — le back-office couvre déjà l'essentiel du besoin au jour 1.

---

## E. Arborescence du site

Principes : une page = une intention de recherche + une étape du parcours ; pas de page « mot-clé » artificielle ; conservation des slugs existants quand ils sont bons ; le CTA « **Réserver mon bilan gratuit** » présent sur toutes les pages (header + fin de page).

```
mfpsport.com
│
├── /                              Accueil — positionnement, 4 pôles, preuves, CTA
├── /bilan-gratuit/                ★ Landing conversion : la séance d'essai expliquée
│                                    (déroulé, pour qui, 0 engagement) → Sportigo
├── /le-centre/                    La salle : 300 m², piste, zone fonctionnelle,
│                                    équipements par familles, galerie photos
├── /preparation-physique/         Pôle Entraînement : coaching, préparation physique,
│                                    tests (Kinvent/Vitruve), Slim Sonic
├── /nutrition/                    Pôle Nutrition : diététicienne du sport, bilans,
│                                    suivis, Z-Métrix
├── /recuperation/                 Pôle Récupération : bain chaud/froid, Cryo-Tank,
│                                    pressothérapie Normatec, ostéo, kiné, massages
├── /preparation-mentale/          Pôle Mental : gestion du stress, compétition
├── /equipe/                       ★ NOUVEAU — tous les professionnels, bios, diplômes
├── /tarifs/                       Formules expliquées par profil + widget planning
├── /equipes-clubs/                B2B : stages, préparation collective, privatisation,
│                                    tests, demande de devis
├── /actualites/                   Liste des actualités
│   └── /actualites/{slug}/        Détail
├── /planning/                     Planning cours collectifs (widget Sportigo) + réservation
├── /contact/                      NAP, carte, horaires, accès, formulaire
│
├── /praticiens/                   Recrutement de praticiens (lien footer uniquement)
├── /mentions-legales/             ★ À créer (obligatoire)
└── /politique-de-confidentialite/ Réécrite
```

Navigation principale (7 entrées max) : **Le centre · Nos pôles (menu : 4 pôles) · L'équipe · Tarifs · Équipes & clubs · Actualités · Contact** + bouton permanent `Réserver mon bilan gratuit` + lien discret `Espace client` (Sportigo).

Anti-doublons assumés : pas de page « coach sportif Arles » séparée de « préparation physique » (une seule page pôle, riche) ; pas de page « pressothérapie » isolée tant que `/recuperation/` peut porter la requête (une ancre par méthode, extraction en page dédiée seulement si la demande le justifie plus tard).

---

## F. Parcours utilisateur

CTA final commun : **Réserver mon bilan gratuit** (sauf B2B : **Demander un devis / Être rappelé**).

### F.1 Personne qui reprend le sport après une longue interruption

- **Besoin** : reprendre sans se blesser, sans jugement, être guidé.
- **Objections** : « je ne suis pas au niveau », « les salles c'est pour les sportifs », peur du regard, peur de l'engagement.
- **Parcours** : Accueil (bloc « pour qui ? ») → `/bilan-gratuit/` → éventuellement `/equipe/`.
- **Informations** : déroulé exact de la première séance, accompagnement systématique, profils variés accueillis.
- **Réassurance** : « aucun niveau requis », bilan = point de départ personnalisé, photos de pratiquants ordinaires (pas de culturistes), témoignage de reprise, gratuité sans engagement.
- **CTA** : Réserver mon bilan gratuit.

### F.2 Personne avec un objectif de remise en forme

- **Besoin** : résultat visible et durable (poids, tonus, énergie).
- **Objections** : « j'ai déjà essayé seul·e sans résultat », coût, régularité.
- **Parcours** : Accueil → `/preparation-physique/` (+ `/nutrition/`) → `/tarifs/` → `/bilan-gratuit/`.
- **Informations** : méthode (bilan → programme → suivi → mesure des progrès), complémentarité entraînement/nutrition, formules 1 mois sans engagement.
- **Réassurance** : suivi mesurable (tests, Z-Métrix), encadrement diplômé, témoignage transformation.
- **CTA** : Réserver mon bilan gratuit.

### F.3 Sportif amateur

- **Besoin** : progresser dans sa discipline (course, rugby, tennis, trail…), passer un cap.
- **Objections** : « la préparation physique c'est pour les pros », doublon avec le club, planning chargé.
- **Parcours** : Accueil ou SEO → `/preparation-physique/` → `/recuperation/` → `/tarifs/` (packs) → `/bilan-gratuit/`.
- **Informations** : tests physiques, programmation par cycles, équipements spécifiques (VertiMax, Wattbike, encodeurs), récupération intégrée.
- **Réassurance** : disciplines déjà accompagnées (rugby, tennis, danse, boxe, marathon…), parcours de Maxime (haut niveau + CREPS).
- **CTA** : Réserver mon bilan gratuit.

### F.4 Sportif de haut niveau

- **Besoin** : structure sérieuse, données, suivi médico-sportif, discrétion.
- **Objections** : « une salle de plus », compétence réelle de l'encadrement, qualité du matériel.
- **Parcours** : `/preparation-physique/` → `/equipe/` → `/le-centre/` → contact direct ou bilan corporel complet.
- **Informations** : CV détaillé de Maxime (FFCC, CREPS, réathlétisation), plateau technique complet, bilan corporel complet (120 €), réseau pluridisciplinaire sur place.
- **Réassurance** : références nominatives (Académie de Tennis, Arles Youth Ballet Company), outils de mesure (Kinvent, Vitruve), confidentialité.
- **CTA** : Réserver mon bilan gratuit (ou prise de contact directe).

### F.5 Équipe professionnelle ou semi-professionnelle (B2B)

- **Besoin** : stage ou cycle de préparation clé en main, tests collectifs, privatisation.
- **Objections** : capacité d'accueil, logistique, budget, sérieux administratif.
- **Parcours** : `/equipes-clubs/` (entrée directe SEO/bouche-à-oreille) → formulaire devis.
- **Informations** : installations et capacités, formats (journée, stage, suivi saison), tests individuels et collectifs, tarifs préférentiels partenariat, interlocuteur unique.
- **Réassurance** : équipes et structures déjà accueillies, encadrement diplômé, souplesse de privatisation.
- **CTA** : Demander un devis / organiser une visite.

### F.6 Personne intéressée par la récupération (pressothérapie, bain froid…)

- **Besoin** : récupérer d'efforts intenses, soulager douleurs/jambes lourdes, prévenir les blessures — parfois sans vouloir s'entraîner sur place.
- **Objections** : « réservé aux abonnés ? », prix à l'unité, efficacité réelle.
- **Parcours** : SEO local (« pressothérapie Arles », « bain froid ») → `/recuperation/` → `/tarifs/` ou RDV praticien.
- **Informations** : chaque méthode expliquée (bénéfices, déroulé, durée), accès via packs/abonnements, praticiens santé sur place.
- **Réassurance** : matériel professionnel (Normatec, Cryo-Tank), encadrement, praticiens diplômés (ostéo D.O., kiné).
- **CTA** : Réserver mon bilan gratuit (ou réserver un protocole récupération via Sportigo).

### F.7 Personne cherchant uniquement les tarifs

- **Besoin** : un prix clair, vite.
- **Objections** : opacité, frais cachés, engagement forcé, comparaison avec le low cost.
- **Parcours** : arrivée directe sur `/tarifs/` (SEO ou menu).
- **Informations** : cartes par profil (« autonome », « accompagné », « cours collectifs », « performance »), prix TTC affichés, ce qui est inclus, engagement/durée, « idéal pour ».
- **Réassurance** : bandeau comparatif de valeur (ce qu'inclut MFP vs une salle classique : bilan, suivi, récupération), sans frais de dossier ⚠️ (à confirmer), essai gratuit mis en avant en tête de page.
- **CTA** : Réserver mon bilan gratuit (avant tout achat direct Sportigo).

---

## G. Direction artistique

### G.1 Direction visuelle

**« La performance accompagnée »** : un design sobre, aéré et technique, où la lumière et les vraies personnes de la salle tiennent le premier rôle. On s'éloigne du double cliché : ni le noir/rouge agressif des salles de musculation, ni le pastel générique du bien-être. Référentiel d'ambiance : cabinet de performance sportive plus que fitness club.

- Photographie réelle uniquement (le fonds existant est bon : séances encadrées, matériel, VertiMax, piste) ; retouches légères harmonisées (température froide légère, contraste doux).
- Beaucoup de blanc/gris clair, typographie forte, touches de bleu — le bleu du logo devient le fil conducteur (il évoque aussi l'eau : bains, récupération).
- Motif graphique dérivé du logo (les « barres arrondies » en dégradé de bleus) utilisable en séparateurs, puces et fonds de section — identité propriétaire sans coût.

### G.2 Palette (dérivée du logo existant)

| Rôle | Couleur | Usage |
|---|---|---|
| Bleu profond (primaire) | `#005CA9` | Titres forts, liens, éléments de marque |
| Bleu nuit (encre) | `#0B2740` | Texte de titrage, footer, fonds de section sombres |
| Bleu clair (accent) | `#4FB8E7` | Icônes, hover, graphiques, motif logo |
| Quasi-noir | `#111417` | Texte courant |
| Blanc cassé | `#F6F9FC` | Fonds de page |
| Gris ardoise | `#5B6B7A` | Texte secondaire, légendes |
| **Accent conversion** | `#FF8A3D` (orange chaud) — *unique élément hors logo* | **Réservé exclusivement au CTA « Réserver mon bilan gratuit »** et aux badges « Offert » |

Règle : l'orange n'apparaît jamais ailleurs — l'œil apprend que « orange = réserver ». Contrastes vérifiés WCAG AA (texte sur fonds listés ≥ 4,5:1).

### G.3 Typographie

- **Titres : Archivo** (ou Space Grotesk) — géométrique, sportive sans agressivité, graisse SemiBold/Bold, capitalisation normale (pas de TOUT-MAJUSCULES criard).
- **Texte : Inter** — lisibilité maximale, 16–18 px base, interlignage 1,6.
- Deux familles maximum, auto-hébergées (woff2, `font-display: swap`) — 0 requête Google Fonts (RGPD + performance).
- Échelle modulaire : H1 40–56 px / H2 32 px / H3 24 px / corps 17 px ; chiffres tabulaires pour les tarifs.

### G.4 Composants clés

| Composant | Traitement |
|---|---|
| **Header** | Fond blanc, logo à gauche, nav centre, CTA orange à droite ; devient compact au scroll ; « Espace client » en lien discret |
| **Hero d'accueil** | Photo réelle plein écran légèrement voilée de bleu nuit, H1 clair (« Votre préparation physique, encadrée par des professionnels — à Arles »), CTA orange + CTA secondaire fantôme (« Découvrir le centre ») |
| **Cartes de tarifs** | Fond blanc, ombre douce, nom + prix en chiffres forts + « /mois » discret, liste d'inclusions avec coches bleues, ligne « Idéal pour : … », bouton ; la formule recommandée : bordure bleu profond + badge « La plus choisie » ⚠️ (véracité à confirmer) ; l'essai gratuit toujours rappelé au-dessus de la grille |
| **Fiches professionnels** | Portrait carré N&B→couleur au survol, nom, rôle, badges de diplômes, 3 lignes de bio + « Lire son parcours » (dépliable), bouton prise de RDV le cas échéant |
| **Témoignages** | Citations courtes en grande typo, nom + discipline + photo miniature ; jamais de carrousel automatique (accessibilité) ; 2–3 par page maximum |
| **Blocs « 4 pôles »** | 4 cartes numérotées 01–04 reprenant le motif logo, pictos linéaires cohérents, un lien précis par carte |
| **CTA de fin de page** | Bandeau bleu nuit systématique : « Prêt·e à faire le point ? Votre bilan est offert. » + bouton orange + mention « Sans engagement — 45 min avec un coach » ⚠️ (durée à confirmer) |
| **Widget Sportigo** | Encapsulé dans un cadre aux couleurs du site, chargé au scroll (facade pattern : image cliquable → chargement du script), pour préserver LCP/INP |

### G.5 Structure de la page d'accueil

1. **Hero** — promesse + CTA (photo coaching réel).
2. **Barre de confiance** — 4 chiffres : 300 m² · 4 pôles · X professionnels · disciplines accompagnées.
3. **« Pour qui ? »** — 4 profils cliquables (reprise / remise en forme / sportif / équipe) → chaque profil renvoie vers sa page.
4. **Les 4 pôles** — cartes 01–04.
5. **La méthode** — 3 étapes : Bilan offert → Programme personnalisé → Suivi & résultats.
6. **L'équipe** — 3–4 visages + lien `/equipe/`.
7. **Témoignages** (réels, nominatifs).
8. **Le centre en images & la vie du centre** — mini-galerie + fil Instagram (grille statique 4–8 posts, lien « Suivre @mfpsport ») + lien `/le-centre/`.
9. **Actualités** — 2 dernières.
10. **CTA final** + bloc infos pratiques (adresse, horaires, carte).

---

## H. Stratégie SEO

### H.1 Intentions de recherche prioritaires

| Intention | Requêtes types | Page cible |
|---|---|---|
| Salle + coaching local | salle de sport Arles, salle de sport avec coach Arles, coaching sportif Arles | Accueil |
| Coach / préparation physique | coach sportif Arles, préparateur physique Arles / Saint-Martin-de-Crau / Tarascon, préparation physique | `/preparation-physique/` |
| Reprise & remise en forme | reprise du sport après arrêt, remise en forme encadrée Arles, sport santé | Accueil + `/bilan-gratuit/` |
| Récupération | récupération sportive Arles, pressothérapie Arles, bain froid / bain chaud sportif, cryothérapie Arles ⚠️ (terme à manier avec précision : bains froids + Cryo-Tank, pas de cryothérapie corps entier) | `/recuperation/` |
| Bilan | bilan physique, bilan corporel, test physique sportif, composition corporelle Arles | `/preparation-physique/` (section bilan) + `/bilan-gratuit/` |
| Nutrition | diététicienne du sport Arles, nutritionniste sportif, suivi nutritionnel sportif | `/nutrition/` |
| B2B | stage préparation physique équipe, préparation physique club rugby/football, privatisation salle sport équipe | `/equipes-clubs/` |
| Marque | MFP Sport, MFP Sport Arles, MFP Sport tarifs, MFP Sport planning | Accueil, `/tarifs/`, `/planning/` |

Zone géographique travaillée dans les contenus (naturellement, sans bourrage) : Arles en priorité, puis Saint-Martin-de-Crau, Tarascon, Beaucaire, Saint-Rémy-de-Provence, Fontvieille et la mention « Alpilles / Camargue / pays d'Arles » dans les textes d'accès et la page Contact.

### H.2 Structure sémantique type (exemple `/recuperation/`)

- `title` : « Récupération sportive à Arles — pressothérapie, bain froid, ostéopathie | MFP Sport » (< 60 car.)
- H1 unique : « La récupération sportive, pilier de votre progression »
- H2 par méthode (Pressothérapie Normatec / Bains chaud & froid / Ostéopathie / Kinésithérapie / Massages), H3 pour bénéfices & déroulé ;
- meta description rédigée orientée bénéfice + lieu ; canonical auto ; maillage : vers `/tarifs/` (protocoles inclus), `/equipe/` (praticiens), `/bilan-gratuit/`.

Le même gabarit s'applique à chaque page pôle. Fini les titres éclatés en fragments Elementor : chaque page a un plan Hn logique et complet.

### H.3 Pages prioritaires (ordre d'effort de contenu)

1. Accueil — 2. `/preparation-physique/` — 3. `/recuperation/` — 4. `/tarifs/` — 5. `/bilan-gratuit/` — 6. `/equipes-clubs/` — 7. `/nutrition/` — 8. `/equipe/` — 9. `/le-centre/` — 10. `/preparation-mentale/`.

### H.4 Contenus locaux nécessaires

- Bloc « Venir au centre » sur `/contact/` : adresse complète, accès depuis Arles centre / Saint-Martin-de-Crau / Tarascon (temps de trajet réels), parking, carte OpenStreetMap ou Google Maps embarquée en façade.
- NAP (nom, adresse, téléphone) strictement identique partout : footer, Contact, Schema.org, Google Business Profile.
- 2–3 actualités locales par trimestre (événements, partenariats clubs, stages) — signal de vitalité locale.

### H.5 Maillage interne

- Chaque page pôle pointe vers : les 3 autres pôles (bloc « approche globale »), `/tarifs/`, `/equipe/`, `/bilan-gratuit/`.
- `/tarifs/` renvoie vers les pôles pour « comprendre ce qui est inclus ».
- Les actualités taguées renvoient vers la page pôle concernée.
- Fil d'Ariane sur toutes les pages profondes (avec `BreadcrumbList`).

### H.6 Données structurées (JSON-LD)

- `HealthClub` (sous-type LocalBusiness) sur tout le site : nom, NAP, géo, horaires, `priceRange`, `sameAs` (réseaux, Sportigo), photos.
- `Person` pour chaque professionnel (jobTitle, memberOf).
- `Service`/`Offer` sur `/tarifs/` (prix affichés = prix confirmés par Maxime uniquement).
- `Article` sur les actualités ; `BreadcrumbList` global ; `FAQPage` plus tard si une FAQ est rédigée.

### H.7 Google Business Profile (chantier parallèle indispensable)

- Créer/revendiquer la fiche « MFP Sport », catégorie principale **Salle de sport**, secondaires : Coach sportif / Centre de remise en forme.
- NAP exact, lien site + lien RDV (Sportigo), horaires, description 750 car. reprenant le positionnement, 15–20 photos réelles (salle, équipe, matériel), posts mensuels (relayer les actualités du site).
- Stratégie d'avis : QR code à l'accueil de la salle + demande post-bilan ; réponses systématiques.

### H.8 Table de redirections 301 (`.htaccess`)

| Ancienne URL | Nouvelle URL | Note |
|---|---|---|
| `/notre-centre/` | `/le-centre/` | |
| `/entrainement-sport-preparation-physique-arles/` | `/preparation-physique/` | |
| `/nutrition-sport-arles/` | `/nutrition/` | |
| `/mental/` | `/preparation-mentale/` | |
| `/recuperation/` | *(inchangée)* | aucune redirection |
| `/tarifs/` | *(inchangée)* | aucune redirection |
| `/contact/` | *(inchangée)* | aucune redirection |
| `/equipes/` | `/equipes-clubs/` | |
| `/pros/` | `/praticiens/` | |
| `/hall-of-fame/` | `/actualites/` | contenu fusionné |
| `/ouverture/` | `/actualites/ouverture-9-septembre/` | article migré |
| `/politique-de-confidentialite/` | *(inchangée)* | réécrite |
| `/coach-maxime/` | `/equipe/` | page privée, par précaution |
| `/fts/*` | `410 Gone` | flux techniques sans valeur |
| `mfpsport.fr/*` | `https://mfpsport.com/$1` | 301 domaine entier après lancement |

### H.9 Checklist SEO avant mise en production

- [ ] `title` + meta description uniques sur chaque page (longueurs vérifiées)
- [ ] 1 seul H1 par page, hiérarchie Hn sans saut
- [ ] Toutes les images : alt rédigé, dimensions déclarées, WebP/AVIF, < 200 Ko au-dessus de la ligne de flottaison
- [ ] JSON-LD validé (Rich Results Test) sur les 5 gabarits
- [ ] `sitemap.xml` généré + `robots.txt` propre (préprod : disallow ; prod : allow + lien sitemap)
- [ ] Canonicals absolus en https://mfpsport.com
- [ ] Table 301 testée URL par URL (les 13 anciennes URLs répondent 301 → 200)
- [ ] Aucune chaîne de redirections (max 1 saut)
- [ ] Core Web Vitals labo : LCP < 2 s, CLS < 0,05, INP < 200 ms (mobile, réseau simulé)
- [ ] Widget Sportigo en chargement différé, sans dégradation CLS
- [ ] Fil Instagram servi en statique (images auto-hébergées, aucun script Meta côté visiteur)
- [ ] Open Graph + Twitter Cards avec visuels dédiés (1200×630)
- [ ] Accessibilité : contrastes AA, navigation clavier, focus visibles, labels de formulaire
- [ ] Search Console : propriété vérifiée pour `.com` (et `.fr`), sitemap soumis le jour J
- [ ] Analytics respectueux (Matomo auto-hébergé OVH ou Plausible self-host léger — pas de coût, pas de bannière si config exemptée CNIL) ⚠️ choix à valider

---

## I. Plan de migration

**Étape 0 — Sauvegarde de sécurité.** Sauvegarde complète OVH (fichiers WordPress + export BDD) conservée hors du repo. Point de non-retour documenté.

**1. Récupérer et trier les contenus WordPress.** Extraction des textes depuis l'export XML (fait pour l'audit — scripts conservés dans `docs/`). Constitution d'un document de contenu par page (texte actuel → texte réécrit → statut de validation Maxime).

**2. Récupérer et optimiser les images.** Sélection des ~100–130 originaux utiles, renommage SEO, recompression (WebP source ou JPG qualité 82), rangement par thème dans `src/assets/`. Les vidéos MOV : conversion MP4/H.264 ou dépôt YouTube (à décider selon usage).

**3. Identifier les anciennes URLs.** Fait (sitemaps Yoast + export) : 13 URLs publiques + `/fts/*`. Table 301 figée en §H.8 ; vérification finale des URLs réellement indexées via `site:mfpsport.com` et Search Console avant bascule.

**4. Créer les nouveaux modèles de contenu.** Collections §D.3 ; saisie des professionnels, offres (avec les tarifs **validés** par Maxime), infos pratiques. Le micro back-office (§D.7) est développé sur ces mêmes collections.

**5. Développer le site sur `mfpsport.fr`.** Multisite OVH `preprod/` + DNS `.fr` ; auth basique + noindex ; déploiement continu depuis `develop`.

**6. Intégrer Sportigo.** Widget planning sur `/planning/` et `/tarifs/` (chargement différé), liens d'achat par offre, lien espace client ; test complet du tunnel réservation sur mobile.

**7. Effectuer les tests.** Lighthouse mobile sur les 5 gabarits, test des 301 en préprod (simulation), navigateurs (Chrome/Safari/Firefox + iOS/Android réels), formulaire de contact/devis, accessibilité (axe + clavier), relecture orthographique intégrale.

**8. Faire valider le contenu par Maxime.** Recette guidée sur `.fr` : checklist page par page (textes, tarifs, photos, liens Sportigo, horaires). Validation **écrite** des 8 points tarifaires de §B.1. Formation back-office (30–45 min, aide-mémoire PDF d'une page).

**9. Préparer les redirections.** `.htaccess` final (301 + sécurité + cache) prêt dans le repo ; répétition de bascule documentée.

**10. Basculer le `.com`.** Créneau creux (dimanche soir). Le multisite `.com` OVH pointe vers le dossier du nouveau site ; certificat SSL vérifié ; WordPress conservé en dossier inactif 3 mois (plan de retour en < 15 min : repointer le multisite).

**11. Rediriger le `.fr` vers le `.com`.** Redirection 301 domaine entier (le `.fr` cesse d'être une préprod publique ; les préprods suivantes vivront sur un sous-domaine protégé, ex. `preprod.mfpsport.com`).

**12. Suivre les erreurs et le référencement.** J0 : soumission sitemap, test des 13 redirections, crawl complet (Screaming Frog gratuit < 500 URLs). Semaines 1–8 : Search Console (couverture, 404), corrections, suivi des positions sur 10 requêtes cibles, suivi GBP. Rapport à 1 mois.

---

## J. Roadmap du projet

| # | Phase | Objectifs | Livrables | Dépendances | Risques principaux | Critères de validation |
|---|---|---|---|---|---|---|
| 1 | **Audit** ✅ | Comprendre l'existant, inventorier contenus/URLs/médias | Ce document (§A) | Export WP, accès au site | — | Audit relu, constats partagés |
| 2 | **Cadrage** | Trancher les 8 points tarifaires, infos légales, liste des pros, horaires | §B complété et signé ; accès OVH/DNS/GitHub réunis | Disponibilité de Maxime | Réponses tardives → gel de la page Tarifs | Toutes les questions B.1/B.2 répondues par écrit |
| 3 | **Architecture** | Poser le socle technique | Repo structuré, Astro initialisé, CI GitHub Actions, préprod `.fr` en ligne (noindex, auth) | Cadrage (accès) | Spécificités OVH (SFTP, multisite) | Un « Hello world » se déploie automatiquement sur `.fr` en < 5 min |
| 4 | **Préparation du contenu** | Réécrire tous les textes, préparer les images | Docs de contenu par page ; bibliothèque d'images renommées/optimisées | Cadrage (tarifs validés) | Sous-estimation du temps de rédaction | Textes validés par Maxime page par page |
| 5 | **Design** | Décliner la DA en maquettes des 5 gabarits clés (accueil, pôle, tarifs, équipe, B2B) | Maquettes + design tokens (couleurs, typo, composants) | DA (§G) validée | Dérive « template fitness » | Maquettes approuvées ; contrastes AA vérifiés |
| 6 | **Développement** | Intégrer gabarits et pages | Site complet en préprod, données réelles | Phases 3–5 | Périmètre qui gonfle | Toutes les pages de §E fonctionnelles sur `.fr` |
| 7 | **Back-office** | Développer le micro back-office (§D.7) : 4 tuiles, login simple, écriture via API GitHub | Back-office opérationnel sur admin.mfpsport.com ; guide PDF « Modifier mon site en 4 cas » | Phase 3 | Périmètre qui gonfle (rester à 4 tuiles) ; sécurité du formulaire d'upload | Maxime ajoute seul un pro, remplace une photo et publie une actu de test — sans aide |
| 8 | **Intégrations externes** | Sportigo (planning + achats + espace client) ; fil Instagram (app Meta, cron de rafraîchissement) | Tunnel Sportigo testé mobile/desktop ; grille Instagram statique alimentée automatiquement | Phase 6 ; compte Instagram professionnel | Widget lent → CWV ; jeton Meta expiré (rafraîchissement à automatiser dès le départ) | Réservation test réussie ; fil Instagram à jour après un post de test |
| 9 | **SEO on-page** | Meta, Schema.org, sitemap, maillage, 301 | Checklist §H.9 exécutée | Phases 4, 6 | Oublis de meta sur pages secondaires | 100 % de la checklist cochée |
| 10 | **Tests & recette** | Qualité transverse | Rapport Lighthouse, test navigateurs, recette Maxime signée | Phases 6–9 | Regressions de dernière minute | LCP < 2 s mobile ; zéro lien cassé ; recette signée |
| 11 | **Mise en production** | Bascule `.com` + redirections `.fr` | Site en prod, WordPress archivé, DNS propres | Phases 2 (accès) + 10 | Erreur DNS/SSL ; 301 manquantes | 13/13 anciennes URLs → 301 → 200 ; SSL A ; rollback documenté |
| 12 | **Suivi post-lancement** | Indexation, corrections, mesure ; étude de l'assistant IA (§D.9) si le besoin se confirme | Rapports S+1, S+4 ; GBP actif ; formation consolidée | Phase 11 | 404 résiduelles ; chute temporaire de positions | 0 erreur de couverture SC à S+4 ; Maxime autonome sur ses 4 cas d'usage |

Jalonnement indicatif (charge, pas calendrier) : phases 3+6+7 ≈ le gros du développement ; la phase 4 (contenu) est le vrai chemin critique — elle dépend de Maxime, il faut la démarrer **immédiatement après le cadrage**, en parallèle de la technique.

---

## Décisions actées & compromis assumés

1. **Statique + Git plutôt que CMS serveur** : on échange la publication instantanée (perdue, ~2–3 min de build) contre zéro maintenance de sécurité côté site public, zéro coût et une sauvegarde parfaite. Compromis favorable pour un site vitrine.
2. **Micro back-office sur mesure plutôt qu'un CMS générique** (décision du 13/07/2026) : contrainte actée « aucun compte GitHub pour Maxime, plus simple que WordPress ». On assume ~400 lignes de PHP maison en échange d'une interface réduite aux 4 gestes réels de Maxime ; ce n'est pas un « faux WordPress » précisément parce que le périmètre est gelé. Sveltia CMS reste le repli documenté (bascule en une demi-journée, contenu intact).
3. **Fil Instagram récupéré au build** plutôt qu'un widget tiers : performance et RGPD préservés, dégradation gracieuse si l'API Meta casse ; en contrepartie, fraîcheur limitée au rythme du cron (2×/semaine) — suffisant pour un site vitrine.
4. **Assistant IA pour Maxime = évolution post-lancement** (§D.9), pas un composant du socle : les formulaires couvrent le besoin au jour 1 ; l'agent ajoutera le canal « demande libre » quand le socle sera stable, avec revue humaine et tarifs exclus de son périmètre.
5. **Slugs partiellement renommés** (perte des slugs `...-arles`) : les 301 conservent le peu d'acquis SEO, et des URLs courtes/propres + un vrai travail on-page rapportent plus que deux slugs sur-optimisés. Le site a très peu d'autorité à préserver — c'est le bon moment pour assainir.
6. **Pas de page dédiée par requête** (pressothérapie, coach sportif…) : pages pôles riches d'abord ; extraction en pages dédiées seulement si les données Search Console le justifient après 3–6 mois.
7. **L'orange CTA hors logo** : entorse contrôlée à la charte pour la conversion, limitée à un seul usage.

---

*Document rédigé le 13/07/2026 (v1.1 le même jour). Prochaines actions : réponses de Maxime aux points restants §B.1 (4–8) et informations §B.2 (accès OVH/DNS, SIRET, téléphone/horaires, compte Instagram professionnel), puis lancement des phases 3 et 4 en parallèle.*
