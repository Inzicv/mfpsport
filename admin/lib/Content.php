<?php

declare(strict_types=1);

final class Content
{
    public const PROFESSIONALS_DIR = 'src/content/professionnels';
    public const NEWS_DIR = 'src/content/actualites';

    /** @return array{data:array<string,mixed>,body:string} */
    public static function parseMarkdown(string $source): array
    {
        if (!preg_match('/\A---\R(.*?)\R---\R?(.*)\z/s', $source, $matches)) {
            throw new RuntimeException('Le fichier de contenu a un format non reconnu.');
        }
        return ['data' => self::parseFrontMatter($matches[1]), 'body' => trim($matches[2])];
    }

    /** @param array<string,mixed> $data */
    public static function professionalMarkdown(array $data, string $body): string
    {
        $lines = [
            '---',
            'nom: ' . self::scalar($data['nom']),
            'role: ' . self::scalar($data['role']),
        ];
        if (($data['photo'] ?? '') !== '') {
            $lines[] = 'photo: ' . self::scalar($data['photo']);
        }
        self::appendArray($lines, 'diplomes', $data['diplomes'] ?? []);
        self::appendArray($lines, 'specialites', $data['specialites'] ?? []);
        self::appendOptional($lines, 'joursPresence', $data['joursPresence'] ?? '');
        self::appendOptional($lines, 'lienRdv', $data['lienRdv'] ?? '');
        self::appendOptional($lines, 'telephone', $data['telephone'] ?? '');
        self::appendOptional($lines, 'email', $data['email'] ?? '');
        self::appendOptional($lines, 'instagram', $data['instagram'] ?? '');
        self::appendOptional($lines, 'facebook', $data['facebook'] ?? '');
        self::appendOptional($lines, 'linkedin', $data['linkedin'] ?? '');
        $lines[] = 'ordre: ' . (int) ($data['ordre'] ?? 0);
        $lines[] = 'actif: ' . (($data['actif'] ?? false) ? 'true' : 'false');
        $lines[] = '---';
        $lines[] = '';
        $lines[] = trim($body);
        $lines[] = '';
        return implode("\n", $lines);
    }

    /** @param array<string,mixed> $data */
    public static function newsMarkdown(array $data, string $body): string
    {
        $lines = [
            '---',
            'titre: ' . self::scalar($data['titre']),
            'date: ' . self::scalar($data['date']),
        ];
        self::appendOptional($lines, 'image', $data['image'] ?? '');
        self::appendOptional($lines, 'imageAlt', $data['imageAlt'] ?? '');
        $lines[] = 'chapo: ' . self::scalar($data['chapo']);
        $lines[] = 'publie: ' . (($data['publie'] ?? false) ? 'true' : 'false');
        $lines[] = '---';
        $lines[] = '';
        $lines[] = trim($body);
        $lines[] = '';
        return implode("\n", $lines);
    }

    public static function slug(string $value): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        $ascii = $ascii === false ? $value : $ascii;
        $slug = strtolower((string) preg_replace('/[^a-zA-Z0-9]+/', '-', $ascii));
        return trim(substr($slug, 0, 70), '-');
    }

    public static function safeBody(string $value): string
    {
        $value = str_replace(["\r\n", "\r"], "\n", $value);
        $value = preg_replace('/<[^>]*>/', '', $value) ?? $value;
        // Neutralise aussi les protocoles actifs dans les liens Markdown.
        $value = preg_replace('/\]\(\s*(?:javascript|data|vbscript)\s*:[^)]*\)/iu', '](#)', $value) ?? $value;
        $value = preg_replace('/^\s*\[[^]]+\]:\s*(?:javascript|data|vbscript)\s*:.*$/imu', '', $value) ?? $value;
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? $value;
        return trim(mb_substr($value, 0, 20000));
    }

    /** @return array<int,array{key:string,title:string,description:string,path:string}> */
    public static function sitePhotos(): array
    {
        return [
            ['key' => 'accueil-principale', 'title' => 'Accueil — photo principale', 'description' => 'La grande photo visible dès l’arrivée sur le site.', 'path' => 'src/assets/images/communaute-adherents-mfp-sport.jpg'],
            ['key' => 'accueil-methode', 'title' => 'Accueil — notre méthode', 'description' => 'La photo près des étapes Évaluer, Construire, Mesurer.', 'path' => 'src/assets/images/coaching-analyse-donnees-vitruve.jpg'],
            ['key' => 'centre-plan', 'title' => 'Le centre — plan de la salle', 'description' => 'Le plan affiché sur la page de présentation du centre.', 'path' => 'src/assets/images/plan-salle-mfp-sport-arles.jpg'],
            ['key' => 'centre-plateau', 'title' => 'Le centre — plateau technique', 'description' => 'La photo principale des équipements VertiMax.', 'path' => 'src/assets/images/entrainement-vertimax-mfp-sport.jpg'],
            ['key' => 'preparation-test', 'title' => 'Préparation physique — test', 'description' => 'Le test de force présenté sur la page Préparation physique.', 'path' => 'src/assets/images/preparateur-physique-test-force.jpg'],
            ['key' => 'preparation-coaching', 'title' => 'Préparation physique — coaching', 'description' => 'L’image de coaching et d’étirement guidé.', 'path' => 'src/assets/images/coaching-etirement-elastique.jpg'],
            ['key' => 'recuperation-cryo', 'title' => 'Récupération — bains froids', 'description' => 'La photo de la Cryo-Tank sur la page Récupération.', 'path' => 'src/assets/images/cryotank-bain-froid-duo.jpg'],
            ['key' => 'recuperation-presso', 'title' => 'Récupération — pressothérapie', 'description' => 'La photo des bottes Normatec sur la page Récupération.', 'path' => 'src/assets/images/pressotherapie-jambes-recuperation.jpg'],
            ['key' => 'bilan', 'title' => 'Bilan gratuit', 'description' => 'La photo d’accompagnement du premier bilan.', 'path' => 'src/assets/images/bilan-corporel-consultation.jpg'],
            ['key' => 'salle-arles', 'title' => 'Salle de sport à Arles', 'description' => 'La vue de la zone cardio sur la page locale.', 'path' => 'src/assets/images/zone-cardio-mfp-sport.jpg'],
            ['key' => 'nutrition-demarche', 'title' => 'Nutrition — démarche', 'description' => 'Le grand visuel Tester, Comprendre, Ajuster, Suivre de la page Nutrition.', 'path' => 'src/assets/images/partenariat-zinzino-mfp-sport.png'],
            ['key' => 'equipes-collectif', 'title' => 'Équipes & clubs — entraînement collectif', 'description' => 'La grande photo de préparation physique collective.', 'path' => 'src/assets/images/lunges-elastique-duo.jpg'],
            ['key' => 'equipes-agilite', 'title' => 'Équipes & clubs — agilité', 'description' => 'La seconde photo de travail des appuis et de l’agilité.', 'path' => 'src/assets/images/agilite-piste-interieure.jpg'],
        ];
    }

    /** @return array<string,mixed> */
    private static function parseFrontMatter(string $yaml): array
    {
        $result = [];
        $lines = preg_split('/\R/', $yaml) ?: [];
        $count = count($lines);
        for ($i = 0; $i < $count; $i++) {
            $line = $lines[$i];
            if (trim($line) === '' || str_starts_with(ltrim($line), '#')) {
                continue;
            }
            if (!preg_match('/^([A-Za-z][A-Za-z0-9]*):(?:\s*(.*))?$/', $line, $match)) {
                continue;
            }
            $key = $match[1];
            $raw = trim((string) ($match[2] ?? ''));
            if ($raw === '') {
                $items = [];
                while ($i + 1 < $count && preg_match('/^\s+-\s+(.*)$/', $lines[$i + 1], $item)) {
                    $items[] = self::decodeScalar(trim($item[1]));
                    $i++;
                }
                $result[$key] = $items;
                continue;
            }
            if ($raw === '>' || $raw === '>-' || $raw === '|' || $raw === '|-') {
                $parts = [];
                while ($i + 1 < $count && (trim($lines[$i + 1]) === '' || preg_match('/^\s{2,}/', $lines[$i + 1]))) {
                    $parts[] = trim($lines[++$i]);
                }
                $result[$key] = trim(implode($raw[0] === '>' ? ' ' : "\n", $parts));
                continue;
            }
            $result[$key] = self::decodeScalar($raw);
        }
        return $result;
    }

    private static function decodeScalar(string $value): mixed
    {
        if ($value === 'true' || $value === 'false') {
            return $value === 'true';
        }
        if (preg_match('/^-?\d+(?:\.\d+)?$/', $value)) {
            return str_contains($value, '.') ? (float) $value : (int) $value;
        }
        if (($value[0] ?? '') === '"') {
            $decoded = json_decode($value, true);
            if (is_string($decoded)) {
                return $decoded;
            }
        }
        if (($value[0] ?? '') === "'" && str_ends_with($value, "'")) {
            return str_replace("''", "'", substr($value, 1, -1));
        }
        return $value;
    }

    private static function scalar(mixed $value): string
    {
        return json_encode((string) $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }

    private static function appendOptional(array &$lines, string $key, mixed $value): void
    {
        if (trim((string) $value) !== '') {
            $lines[] = $key . ': ' . self::scalar($value);
        }
    }

    private static function appendArray(array &$lines, string $key, array $values): void
    {
        $lines[] = $key . ':';
        foreach ($values as $value) {
            if (trim((string) $value) !== '') {
                $lines[] = '  - ' . self::scalar($value);
            }
        }
    }
}
