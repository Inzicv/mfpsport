<?php

declare(strict_types=1);

// Copier ce fichier HORS de la racine web, par exemple :
// /home/mfpspou/private/mfpsport-admin.php
// Ne jamais committer le fichier rempli.
return [
    'github' => [
        'owner' => 'organisation-ou-compte-github',
        'repository' => 'MFPsport',
        'branch' => 'main',
        // Jeton fine-grained limite a ce depot, permission Contents: Read and write.
        'token' => 'github_pat_xxxxxxxxxxxxxxxxxxxx',
    ],
    'auth' => [
        'username' => 'maxime',
        // Generer avec : php -r "echo password_hash('mot-de-passe', PASSWORD_DEFAULT), PHP_EOL;"
        'password_hash' => '$2y$10$remplacer.par.un.vrai.hash.securise',
    ],
    'session_lifetime' => 1800,
    // Doit etre hors de la racine web et accessible en ecriture par PHP.
    'storage_path' => '/home/mfpspou/private/mfpsport-admin-state',
];
