<?php

declare(strict_types=1);

if ($argc !== 2) {
    fwrite(STDERR, "Usage: php scripts/build-admin-config.php <fichier-sortie>\n");
    exit(2);
}

/**
 * Lit une variable sensible sans jamais afficher sa valeur.
 */
function required_environment_value(string $name): string
{
    $value = getenv($name);
    if (!is_string($value) || $value === '') {
        fwrite(STDERR, "La variable {$name} est absente.\n");
        exit(1);
    }

    return $value;
}

$githubToken = required_environment_value('ADMIN_GITHUB_TOKEN');
$adminPassword = required_environment_value('ADMIN_PASSWORD');
$adminUsername = getenv('ADMIN_USERNAME');
if (!is_string($adminUsername) || $adminUsername === '') {
    $adminUsername = 'contact.mfpsport@gmail.com';
}

$config = [
    'github' => [
        'owner' => 'Inzicv',
        'repository' => 'mfpsport',
        'branch' => 'main',
        'token' => $githubToken,
    ],
    'auth' => [
        'username' => $adminUsername,
        'password_hash' => password_hash($adminPassword, PASSWORD_DEFAULT),
    ],
    'session_lifetime' => 1800,
    'storage_path' => '/home/mfpspou/private/mfpsport-admin-state',
];

$contents = "<?php\n\ndeclare(strict_types=1);\n\nreturn "
    . var_export($config, true)
    . ";\n";

$outputPath = $argv[1];
if (file_put_contents($outputPath, $contents, LOCK_EX) === false) {
    fwrite(STDERR, "Impossible de générer la configuration privée.\n");
    exit(1);
}

chmod($outputPath, 0600);
fwrite(STDOUT, "Configuration privée générée.\n");
