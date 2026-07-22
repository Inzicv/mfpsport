<?php

declare(strict_types=1);

function render_header(string $title, string $section = ''): void
{
    $flash = pull_flash();
    ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex,nofollow,noarchive">
  <title><?= e($title) ?> · MFP Sport</title>
  <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
  <a class="skip-link" href="#contenu">Aller au contenu</a>
  <header class="topbar">
    <a class="brand" href="./" aria-label="Accueil de l’administration MFP Sport">
      <span class="brand-mark">MFP</span><span>Administration</span>
    </a>
    <?php if (is_authenticated()): ?>
      <nav aria-label="Navigation principale">
        <a class="<?= $section === 'team' ? 'active' : '' ?>" href="?action=professionals">L’équipe</a>
        <a class="<?= $section === 'news' ? 'active' : '' ?>" href="?action=news">Actualités</a>
        <a class="<?= $section === 'photos' ? 'active' : '' ?>" href="?action=photos">Photos</a>
      </nav>
      <form action="?action=logout" method="post" class="logout-form">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <button class="link-button" type="submit">Se déconnecter</button>
      </form>
    <?php endif; ?>
  </header>
  <main id="contenu" class="shell">
    <?php if ($flash): ?>
      <div class="notice notice--<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
    <?php endif; ?>
<?php
}

function render_footer(): void
{
    ?>
  </main>
  <footer class="footer"><span>MFP Sport</span><span>Chaque modification est sauvegardée et réversible.</span></footer>
  <script src="assets/admin.js" defer></script>
</body>
</html>
<?php
}

function page_intro(string $eyebrow, string $title, string $description, string $actionHtml = ''): void
{
    ?>
  <div class="page-head">
    <div><p class="eyebrow"><?= e($eyebrow) ?></p><h1><?= e($title) ?></h1><p><?= e($description) ?></p></div>
    <?php if ($actionHtml !== ''): ?><div class="page-actions"><?= $actionHtml ?></div><?php endif; ?>
  </div>
<?php
}

function render_errors(array $errors): void
{
    if ($errors === []) {
        return;
    }
    ?>
  <div class="notice notice--error" role="alert">
    <strong>Quelques informations sont à corriger :</strong>
    <ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
  </div>
<?php
}

function input_value(array $values, string $key, mixed $fallback = ''): string
{
    $value = $values[$key] ?? $fallback;
    return is_array($value) ? '' : (string) $value;
}

function render_publish_note(): void
{
    ?>
  <aside class="publish-note">
    <span aria-hidden="true">✓</span>
    <div><strong>Publication automatique</strong><p>Après validation, la modification sera visible sur le site dans environ 3 minutes.</p></div>
  </aside>
<?php
}
