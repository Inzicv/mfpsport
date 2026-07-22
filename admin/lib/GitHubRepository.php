<?php

declare(strict_types=1);

final class GitHubRepository
{
    private string $owner;
    private string $repository;
    private string $branch;
    private string $token;
    private string $baseUrl = 'https://api.github.com';

    public function __construct(array $config)
    {
        $this->owner = (string) ($config['owner'] ?? '');
        $this->repository = (string) ($config['repository'] ?? '');
        $this->branch = (string) ($config['branch'] ?? 'develop');
        $this->token = (string) ($config['token'] ?? '');

        if ($this->owner === '' || $this->repository === '' || $this->token === '') {
            throw new RuntimeException('La connexion au contenu du site n\'est pas configuree.');
        }
        if (!preg_match('/^[A-Za-z0-9_.-]+$/', $this->owner) || !preg_match('/^[A-Za-z0-9_.-]+$/', $this->repository)) {
            throw new RuntimeException('Configuration du depot invalide.');
        }
    }

    public function getBranch(): string
    {
        return $this->branch;
    }

    public function listDirectory(string $path): array
    {
        $response = $this->request('GET', '/repos/' . rawurlencode($this->owner) . '/' . rawurlencode($this->repository) . '/contents/' . $this->encodePath($path), null, ['ref' => $this->branch]);
        if (!is_array($response)) {
            return [];
        }
        return array_values(array_filter($response, static fn ($item): bool => is_array($item) && ($item['type'] ?? '') === 'file'));
    }

    public function fileExists(string $path): bool
    {
        try {
            $this->getFile($path);
            return true;
        } catch (GitHubNotFoundException) {
            return false;
        }
    }

    /** @return array{content:string,sha:string} */
    public function getFile(string $path): array
    {
        $response = $this->request('GET', '/repos/' . rawurlencode($this->owner) . '/' . rawurlencode($this->repository) . '/contents/' . $this->encodePath($path), null, ['ref' => $this->branch]);
        if (!is_array($response) || !isset($response['sha'])) {
            throw new RuntimeException('Reponse inattendue lors de la lecture du contenu.');
        }

        $encoded = (string) ($response['content'] ?? '');
        if ($encoded === '' && isset($response['sha'])) {
            $blob = $this->request('GET', '/repos/' . rawurlencode($this->owner) . '/' . rawurlencode($this->repository) . '/git/blobs/' . rawurlencode((string) $response['sha']));
            $encoded = is_array($blob) ? (string) ($blob['content'] ?? '') : '';
        }

        $decoded = base64_decode(str_replace(["\r", "\n"], '', $encoded), true);
        if ($decoded === false) {
            throw new RuntimeException('Le contenu recu est illisible.');
        }
        return ['content' => $decoded, 'sha' => (string) $response['sha']];
    }

    /**
     * Cree un unique commit atomique. La valeur null supprime le fichier.
     * @param array<string, string|null> $changes
     */
    public function commit(array $changes, string $message): void
    {
        if ($changes === []) {
            return;
        }
        foreach (array_keys($changes) as $path) {
            if (!$this->isSafeRepositoryPath($path)) {
                throw new RuntimeException('Chemin de contenu refuse.');
            }
        }

        $repo = '/repos/' . rawurlencode($this->owner) . '/' . rawurlencode($this->repository);
        $ref = $this->request('GET', $repo . '/git/ref/heads/' . $this->encodePath($this->branch));
        $headSha = (string) ($ref['object']['sha'] ?? '');
        if ($headSha === '') {
            throw new RuntimeException('La branche de publication est introuvable.');
        }

        $headCommit = $this->request('GET', $repo . '/git/commits/' . rawurlencode($headSha));
        $baseTree = (string) ($headCommit['tree']['sha'] ?? '');
        $tree = [];

        foreach ($changes as $path => $content) {
            if ($content === null) {
                $tree[] = ['path' => $path, 'mode' => '100644', 'type' => 'blob', 'sha' => null];
                continue;
            }
            $blob = $this->request('POST', $repo . '/git/blobs', [
                'content' => base64_encode($content),
                'encoding' => 'base64',
            ]);
            $tree[] = ['path' => $path, 'mode' => '100644', 'type' => 'blob', 'sha' => (string) ($blob['sha'] ?? '')];
        }

        $newTree = $this->request('POST', $repo . '/git/trees', ['base_tree' => $baseTree, 'tree' => $tree]);
        $newCommit = $this->request('POST', $repo . '/git/commits', [
            'message' => mb_substr($message, 0, 120),
            'tree' => (string) ($newTree['sha'] ?? ''),
            'parents' => [$headSha],
        ]);
        $this->request('PATCH', $repo . '/git/refs/heads/' . $this->encodePath($this->branch), [
            'sha' => (string) ($newCommit['sha'] ?? ''),
            'force' => false,
        ]);
    }

    private function isSafeRepositoryPath(string $path): bool
    {
        return !str_contains($path, '..')
            && (str_starts_with($path, 'src/content/professionnels/')
                || str_starts_with($path, 'src/content/actualites/')
                || str_starts_with($path, 'src/assets/images/'));
    }

    private function encodePath(string $path): string
    {
        return implode('/', array_map('rawurlencode', explode('/', trim($path, '/'))));
    }

    private function request(string $method, string $path, ?array $body = null, array $query = []): mixed
    {
        $url = $this->baseUrl . $path;
        if ($query !== []) {
            $url .= '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        }

        $curl = curl_init($url);
        if ($curl === false) {
            throw new RuntimeException('Impossible d\'initialiser la connexion.');
        }
        $headers = [
            'Accept: application/vnd.github+json',
            'Authorization: Bearer ' . $this->token,
            'X-GitHub-Api-Version: 2022-11-28',
            'User-Agent: MFP-Sport-Admin',
        ];
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_FOLLOWLOCATION => false,
        ];
        if ($body !== null) {
            $payload = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
            $options[CURLOPT_POSTFIELDS] = $payload;
            $headers[] = 'Content-Type: application/json';
            $options[CURLOPT_HTTPHEADER] = $headers;
        }
        curl_setopt_array($curl, $options);
        $raw = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($raw === false) {
            error_log('MFP admin cURL: ' . $error);
            throw new RuntimeException('Le service de publication est momentanément injoignable.');
        }
        $decoded = json_decode($raw, true);
        if ($status === 404) {
            throw new GitHubNotFoundException('Contenu introuvable.');
        }
        if ($status < 200 || $status >= 300) {
            $message = is_array($decoded) ? (string) ($decoded['message'] ?? '') : '';
            if ($status === 409 || $status === 422) {
                throw new RuntimeException('Le site a change pendant votre modification. Rechargez la page puis recommencez.');
            }
            error_log('MFP admin GitHub API ' . $status . ': ' . $message);
            throw new RuntimeException('La publication n\'a pas abouti. Reessayez dans quelques instants.');
        }
        return $decoded;
    }
}

final class GitHubNotFoundException extends RuntimeException
{
}
