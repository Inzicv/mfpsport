// @ts-check
import { defineConfig } from 'astro/config';
import sitemap from '@astrojs/sitemap';

// Domaine de production ; la préprod (mfpsport.fr) surchargera SITE_URL et
// PREPROD au moment du build (voir .github/workflows/deploy-preprod.yml).
const site = process.env.SITE_URL ?? 'https://mfpsport.com';

// https://astro.build/config
export default defineConfig({
  site,
  trailingSlash: 'always',
  integrations: [sitemap()],
});
