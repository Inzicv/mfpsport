#!/usr/bin/env node
// Récupère les derniers posts du compte Instagram professionnel de MFP Sport
// et les écrit en JSON + images statiques dans le repo. Voir
// docs/etude-refonte.md §D.8 — exécuté par .github/workflows/refresh-instagram.yml.
//
// Variables d'environnement requises :
//   INSTAGRAM_ACCESS_TOKEN — jeton longue durée (compte pro + app Meta)
//   INSTAGRAM_USER_ID      — identifiant du compte Instagram
//
// En l'absence de ces variables, le script ne fait rien et sort en succès
// (dégradation gracieuse : le site garde les derniers posts déjà connus).

import { mkdir, writeFile, readdir, rm } from 'node:fs/promises';
import path from 'node:path';

const POSTS_LIMIT = 8;
const CONTENT_DIR = path.resolve('src/content/instagram');
const IMAGES_DIR = path.resolve('public/instagram');

const token = process.env.INSTAGRAM_ACCESS_TOKEN;
const userId = process.env.INSTAGRAM_USER_ID;

if (!token || !userId) {
  console.log('INSTAGRAM_ACCESS_TOKEN / INSTAGRAM_USER_ID absents — étape ignorée.');
  process.exit(0);
}

const fields = 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp';
const url = `https://graph.instagram.com/${userId}/media?fields=${fields}&limit=${POSTS_LIMIT}&access_token=${token}`;

const res = await fetch(url);
if (!res.ok) {
  console.error(`Échec de l'appel à l'API Instagram : ${res.status} ${await res.text()}`);
  // On ne fait pas échouer le build : le site garde les posts précédents.
  process.exit(0);
}

const { data: posts } = await res.json();

await mkdir(CONTENT_DIR, { recursive: true });
await mkdir(IMAGES_DIR, { recursive: true });

// On repart d'un dossier propre pour ne pas accumuler les posts retirés par
// Maxime depuis Instagram.
for (const dir of [CONTENT_DIR, IMAGES_DIR]) {
  for (const file of await readdir(dir)) {
    if (file !== '.gitkeep') await rm(path.join(dir, file));
  }
}

for (const post of posts ?? []) {
  const imageUrl = post.media_type === 'VIDEO' ? post.thumbnail_url : post.media_url;
  if (!imageUrl) continue;

  const ext = post.media_type === 'VIDEO' ? 'jpg' : 'jpg';
  const imageName = `${post.id}.${ext}`;

  const imageRes = await fetch(imageUrl);
  const buffer = Buffer.from(await imageRes.arrayBuffer());
  await writeFile(path.join(IMAGES_DIR, imageName), buffer);

  await writeFile(
    path.join(CONTENT_DIR, `${post.id}.json`),
    JSON.stringify(
      {
        permalink: post.permalink,
        image: `/instagram/${imageName}`,
        legende: post.caption ?? '',
        date: post.timestamp,
      },
      null,
      2
    )
  );
}

console.log(`${posts?.length ?? 0} post(s) Instagram synchronisé(s).`);
