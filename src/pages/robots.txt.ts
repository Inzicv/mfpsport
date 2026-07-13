import type { APIRoute } from 'astro';

// La préprod (mfpsport.fr) est buildée avec PREPROD=true (voir
// .github/workflows/deploy-preprod.yml) : elle interdit tout crawl.
const isPreprod = process.env.PREPROD === 'true';

export const GET: APIRoute = ({ site }) => {
  const body = isPreprod
    ? 'User-agent: *\nDisallow: /\n'
    : `User-agent: *\nAllow: /\n\nSitemap: ${new URL('sitemap-index.xml', site).toString()}\n`;

  return new Response(body, { headers: { 'Content-Type': 'text/plain' } });
};
