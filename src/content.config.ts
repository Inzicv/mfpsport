import { defineCollection, z } from 'astro:content';
import { glob, file } from 'astro/loaders';

// Modèle de données — voir docs/etude-refonte.md §D.3.
// Ces collections sont celles administrées via le micro back-office (§D.7),
// à l'exception des textes de pages « stables » qui restent dans src/pages.

const professionnels = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/professionnels' }),
  schema: z.object({
    nom: z.string(),
    role: z.string(),
    photo: z.string().optional(),
    diplomes: z.array(z.string()).default([]),
    specialites: z.array(z.string()).default([]),
    joursPresence: z.string().optional(),
    lienRdv: z.string().url().optional(),
    ordre: z.number().default(0),
    actif: z.boolean().default(true),
  }),
});

const actualites = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/actualites' }),
  schema: z.object({
    titre: z.string(),
    date: z.coerce.date(),
    image: z.string().optional(),
    chapo: z.string(),
    publie: z.boolean().default(true),
  }),
});

const offres = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/offres' }),
  schema: z.object({
    nom: z.string(),
    categorie: z.enum([
      'abonnement',
      'cours-collectifs',
      'preparation-individuelle',
      'prestation',
    ]),
    prix: z.number(),
    unite: z.string(),
    dureeEngagement: z.string().optional(),
    inclusions: z.array(z.string()).default([]),
    idealPour: z.string().optional(),
    lienAchat: z.string().url().optional(),
    miseEnAvant: z.boolean().default(false),
    ordre: z.number().default(0),
  }),
});

// Fichiers uniques (pas de collection à entrées multiples).
const infos = defineCollection({
  loader: file('./src/content/infos/infos-pratiques.json'),
  schema: z.object({
    adresse: z.string(),
    telephone: z.string(),
    email: z.string().email(),
    horaires: z.array(z.object({ jour: z.string(), plage: z.string() })),
    reseaux: z.object({
      instagram: z.string().url().optional(),
      facebook: z.string().url().optional(),
    }),
    lienEspaceClient: z.string().url(),
    lienReservationBilan: z.string().url(),
    bandeauAnnonce: z.string().optional(),
  }),
});

const temoignages = defineCollection({
  loader: file('./src/content/infos/temoignages.json'),
  schema: z.object({
    auteur: z.string(),
    profil: z.string(),
    texte: z.string(),
    photo: z.string().optional(),
  }),
});

// Alimenté par le workflow refresh-instagram.yml (§D.8) — jamais édité à la main.
const instagram = defineCollection({
  loader: glob({ pattern: '**/*.json', base: './src/content/instagram' }),
  schema: z.object({
    permalink: z.string().url(),
    image: z.string(),
    legende: z.string().optional(),
    date: z.coerce.date(),
  }),
});

export const collections = {
  professionnels,
  actualites,
  offres,
  infos,
  temoignages,
  instagram,
};
