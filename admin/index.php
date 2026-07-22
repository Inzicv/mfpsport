<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/View.php';

$action = isset($_GET['action']) && is_string($_GET['action']) ? $_GET['action'] : 'dashboard';

try {
    if ($action === 'login') {
        handle_login($config);
        exit;
    }

    if ($action === 'logout') {
        require_authentication();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('./');
        }
        verify_csrf();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
        }
        session_destroy();
        redirect('?action=login');
    }

    require_authentication();
    $repo = repository($config);

    if ($action === 'media') {
        serve_media($repo);
    }

    switch ($action) {
        case 'dashboard': render_dashboard(); break;
        case 'professionals': render_professionals($repo); break;
        case 'professional-new': render_professional_form([], '', '', true); break;
        case 'professional-edit': render_professional_edit($repo); break;
        case 'professional-save': save_professional($repo); break;
        case 'professional-delete': delete_professional($repo); break;
        case 'news': render_news($repo); break;
        case 'news-new': render_news_form([], '', '', true); break;
        case 'news-edit': render_news_edit($repo); break;
        case 'news-save': save_news($repo); break;
        case 'news-delete': delete_news($repo); break;
        case 'photos': render_photos(); break;
        case 'photo-replace': replace_photo($repo); break;
        default: render_not_found();
    }
} catch (Throwable $error) {
    error_log('MFP admin: ' . $error->getMessage());
    if (!headers_sent()) {
        http_response_code(500);
    }
    render_header('Un problème est survenu');
    ?>
      <div class="confirm-card">
        <p class="eyebrow">Publication interrompue</p>
        <h1>La modification n’a pas été enregistrée</h1>
        <p><?= e($error->getMessage()) ?></p>
        <a class="button" href="./">Revenir à l’accueil</a>
      </div>
    <?php
    render_footer();
}

function handle_login(array $config): void
{
    if (is_authenticated()) {
        redirect('./');
    }
    $error = '';
    $state = login_state($config);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();
        if ($state['locked_until'] > time()) {
            $minutes = max(1, (int) ceil(($state['locked_until'] - time()) / 60));
            $error = 'Trop de tentatives. Réessayez dans ' . $minutes . ' minute' . ($minutes > 1 ? 's' : '') . '.';
        } else {
            $username = post_string('username', 100);
            $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
            $auth = (array) ($config['auth'] ?? []);
            $validUser = hash_equals((string) ($auth['username'] ?? ''), $username);
            $validPassword = password_verify($password, (string) ($auth['password_hash'] ?? ''));
            if ($validUser && $validPassword) {
                clear_login_failures($config);
                session_regenerate_id(true);
                $_SESSION['authenticated'] = true;
                $_SESSION['last_activity'] = time();
                $_SESSION['csrf'] = bin2hex(random_bytes(32));
                redirect('./');
            }
            record_login_failure($config);
            usleep(random_int(250000, 550000));
            $error = 'Identifiant ou mot de passe incorrect.';
        }
    }

    render_header('Connexion');
    ?>
      <div class="login-wrap">
        <form class="login-card" method="post" action="?action=login">
          <p class="eyebrow">Espace privé</p>
          <h1>Bonjour Maxime</h1>
          <p>Connectez-vous pour modifier le contenu du site MFP Sport.</p>
          <?php if ($error !== ''): ?><div class="notice notice--error" role="alert"><?= e($error) ?></div><?php endif; ?>
          <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
          <div class="field">
            <label for="username">Identifiant</label>
            <input id="username" name="username" autocomplete="username" required autofocus>
          </div>
          <div class="field">
            <label for="password">Mot de passe</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
          </div>
          <button class="button" type="submit">Se connecter</button>
        </form>
      </div>
    <?php
    render_footer();
}

function render_dashboard(): void
{
    render_header('Accueil');
    page_intro('Votre site', 'Que souhaitez-vous modifier ?', 'Choisissez une rubrique. Chaque écran vous guide jusqu’à la publication.');
    ?>
      <div class="dashboard-grid">
        <a class="dashboard-card dashboard-card--dark" href="?action=professionals">
          <span class="dashboard-card__number">01</span><span class="dashboard-card__arrow" aria-hidden="true">↗</span>
          <h2>L’équipe</h2><p>Ajouter, modifier ou retirer un professionnel.</p>
        </a>
        <a class="dashboard-card dashboard-card--accent" href="?action=news">
          <span class="dashboard-card__number">02</span><span class="dashboard-card__arrow" aria-hidden="true">↗</span>
          <h2>Les actualités</h2><p>Rédiger, corriger ou supprimer une publication.</p>
        </a>
        <a class="dashboard-card" href="?action=photos">
          <span class="dashboard-card__number">03</span><span class="dashboard-card__arrow" aria-hidden="true">↗</span>
          <h2>Les photos du site</h2><p>Voir les emplacements et remplacer une image en toute sécurité.</p>
        </a>
      </div>
    <?php
    render_footer();
}

function render_professionals(GitHubRepository $repo): void
{
    $entries = load_markdown_entries($repo, Content::PROFESSIONALS_DIR);
    usort($entries, static fn ($a, $b): int => ((int) ($a['data']['ordre'] ?? 0)) <=> ((int) ($b['data']['ordre'] ?? 0)));
    render_header('L’équipe', 'team');
    page_intro('Contenu', 'L’équipe', 'Les fiches visibles sur la page Équipe et dans les pôles du site.', '<a class="button" href="?action=professional-new">+ Ajouter un professionnel</a>');
    if ($entries === []) {
        echo '<div class="empty"><p>Aucun professionnel pour le moment.</p></div>';
    } else {
        echo '<ul class="item-list">';
        foreach ($entries as $entry) {
            $data = $entry['data'];
            $photo = (string) ($data['photo'] ?? '');
            ?>
              <li class="item-card">
                <div class="item-card__image">
                  <?php if ($photo !== ''): ?><img src="?action=media&amp;path=<?= rawurlencode(frontmatter_image_to_repo_path($photo)) ?>" alt=""><?php else: ?><?= e(initials((string) ($data['nom'] ?? ''))) ?><?php endif; ?>
                </div>
                <div><h2><?= e($data['nom'] ?? '') ?></h2><div class="item-card__meta"><p><?= e($data['role'] ?? '') ?></p><span class="status <?= ($data['actif'] ?? true) ? '' : 'status--muted' ?>"><?= ($data['actif'] ?? true) ? 'Visible' : 'Masqué' ?></span></div></div>
                <div class="item-card__actions">
                  <a class="button button--quiet" href="?action=professional-edit&amp;id=<?= e($entry['id']) ?>">Modifier</a>
                  <a class="button button--quiet" href="?action=professional-delete&amp;id=<?= e($entry['id']) ?>">Retirer</a>
                </div>
              </li>
            <?php
        }
        echo '</ul>';
    }
    render_footer();
}

function render_professional_edit(GitHubRepository $repo): void
{
    $id = is_string($_GET['id'] ?? null) ? $_GET['id'] : '';
    assert_safe_id($id);
    $file = $repo->getFile(Content::PROFESSIONALS_DIR . '/' . $id . '.md');
    $parsed = Content::parseMarkdown($file['content']);
    render_professional_form($parsed['data'], $parsed['body'], $file['sha'], false, [], $id);
}

function save_professional(GitHubRepository $repo): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('?action=professionals');
    }
    verify_csrf();
    $isNew = post_string('is_new', 5) === '1';
    $id = $isNew ? Content::slug(post_string('slug', 70)) : post_string('id', 70);
    assert_safe_id($id);
    $path = Content::PROFESSIONALS_DIR . '/' . $id . '.md';
    $expectedSha = post_string('source_sha', 100);
    $existingPhoto = '';
    $errors = [];

    if ($isNew && $repo->fileExists($path)) {
        $errors[] = 'Un professionnel utilise déjà cette adresse. Modifiez le nom court.';
    }
    if (!$isNew) {
        $current = $repo->getFile($path);
        if (!hash_equals($current['sha'], $expectedSha)) {
            throw new RuntimeException('Cette fiche a été modifiée entre-temps. Rechargez-la avant de recommencer.');
        }
        $currentParsed = Content::parseMarkdown($current['content']);
        $existingPhoto = (string) ($currentParsed['data']['photo'] ?? '');
    }

    $postedSpecialities = array_filter((array) ($_POST['specialites'] ?? []), 'is_string');
    $specialities = array_values(array_intersect($postedSpecialities, ['preparation-physique', 'nutrition', 'recuperation', 'mental']));
    $diplomas = array_values(array_filter(array_map('trim', preg_split('/\R/', post_string('diplomes', 4000)) ?: [])));
    $values = [
        'nom' => post_string('nom', 120), 'role' => post_string('role', 160), 'photo' => $existingPhoto,
        'diplomes' => $diplomas, 'specialites' => $specialities, 'joursPresence' => post_string('joursPresence', 180),
        'lienRdv' => post_string('lienRdv', 500), 'telephone' => post_string('telephone', 60), 'email' => post_string('email', 180),
        'instagram' => post_string('instagram', 500), 'facebook' => post_string('facebook', 500), 'linkedin' => post_string('linkedin', 500),
        'ordre' => max(0, min(999, (int) post_string('ordre', 4))), 'actif' => isset($_POST['actif']),
    ];
    $body = Content::safeBody(post_string('body', 25000));
    if ($values['nom'] === '') $errors[] = 'Le nom est obligatoire.';
    if ($values['role'] === '') $errors[] = 'Le rôle est obligatoire.';
    if ($body === '') $errors[] = 'La présentation est obligatoire.';
    if ($values['email'] !== '' && filter_var($values['email'], FILTER_VALIDATE_EMAIL) === false) $errors[] = 'L’adresse e-mail n’est pas valide.';
    foreach (['lienRdv' => 'lien de rendez-vous', 'instagram' => 'lien Instagram', 'facebook' => 'lien Facebook', 'linkedin' => 'lien LinkedIn'] as $field => $label) {
        if (!valid_web_url((string) $values[$field])) $errors[] = 'Le ' . $label . ' n’est pas valide.';
    }

    $changes = [];
    $upload = $_FILES['photo'] ?? ['error' => UPLOAD_ERR_NO_FILE];
    if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        try {
            $imagePath = 'src/assets/images/professionnels/portrait-' . $id . '.jpg';
            $changes[$imagePath] = ImageProcessor::fromUpload($upload, 'jpg');
            $values['photo'] = '../../assets/images/professionnels/portrait-' . $id . '.jpg';
        } catch (Throwable $error) {
            $errors[] = $error->getMessage();
        }
    }

    if ($errors !== []) {
        render_professional_form($values, $body, $expectedSha, $isNew, $errors, $id);
        return;
    }
    $changes[$path] = Content::professionalMarkdown($values, $body);
    $repo->commit($changes, 'Maxime : ' . ($isNew ? 'ajout de ' : 'mise à jour de ') . $values['nom']);
    flash('success', '✓ Enregistré — la fiche sera en ligne dans environ 3 minutes.');
    redirect('?action=professionals');
}

function render_professional_form(array $values, string $body, string $sourceSha, bool $isNew, array $errors = [], string $id = ''): void
{
    $title = $isNew ? 'Ajouter un professionnel' : 'Modifier la fiche';
    render_header($title, 'team');
    page_intro('L’équipe', $title, $isNew ? 'Créez une fiche claire. Vous pourrez la corriger à tout moment.' : 'Modifiez uniquement les informations nécessaires.');
    render_errors($errors);
    $specialities = (array) ($values['specialites'] ?? []);
    $photo = (string) ($values['photo'] ?? '');
    ?>
      <form method="post" action="?action=professional-save" enctype="multipart/form-data" class="editor-layout">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="is_new" value="<?= $isNew ? '1' : '0' ?>">
        <input type="hidden" name="id" value="<?= e($id) ?>"><input type="hidden" name="source_sha" value="<?= e($sourceSha) ?>">
        <div class="form-card">
          <section class="form-section"><h2>Informations principales</h2>
            <div class="field-grid">
              <div class="field"><label for="nom">Nom et prénom *</label><input id="nom" name="nom" value="<?= e(input_value($values, 'nom')) ?>" data-auto-slug="slug" maxlength="120" required></div>
              <div class="field"><label for="role">Rôle *</label><input id="role" name="role" value="<?= e(input_value($values, 'role')) ?>" maxlength="160" placeholder="Ex. Ostéopathe du sport" required></div>
            </div>
            <?php if ($isNew): ?><div class="field"><label for="slug">Nom court pour l’adresse *</label><input id="slug" name="slug" value="<?= e($id) ?>" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" maxlength="70" required><small>Exemple : maxime-favier. Ne sera pas visible sur le site.</small></div><?php endif; ?>
            <div class="field"><label for="body">Présentation *</label><textarea id="body" class="body-editor" name="body" maxlength="20000" required><?= e($body) ?></textarea><small>Écrivez simplement en paragraphes. Pour un sous-titre, commencez une ligne par ##.</small></div>
          </section>
          <section class="form-section"><h2>Expertise et présence</h2>
            <div class="field"><label for="diplomes">Diplômes</label><textarea id="diplomes" name="diplomes" rows="5" placeholder="Un diplôme par ligne"><?= e(implode("\n", (array) ($values['diplomes'] ?? []))) ?></textarea></div>
            <div class="field"><span class="field-label">Pôles associés</span><div class="checkboxes">
              <?php foreach (['preparation-physique' => ['Préparation physique', 'Entraînement et coaching'], 'nutrition' => ['Nutrition', 'Suivi alimentaire'], 'recuperation' => ['Récupération', 'Soins et protocoles'], 'mental' => ['Mental', 'Préparation mentale']] as $value => [$label, $help]): ?>
                <label class="check"><input type="checkbox" name="specialites[]" value="<?= e($value) ?>" <?= in_array($value, $specialities, true) ? 'checked' : '' ?>><span><?= e($label) ?><small><?= e($help) ?></small></span></label>
              <?php endforeach; ?>
            </div></div>
            <div class="field-grid"><div class="field"><label for="joursPresence">Jours de présence</label><input id="joursPresence" name="joursPresence" value="<?= e(input_value($values, 'joursPresence')) ?>" placeholder="Ex. Mardi et jeudi"></div><div class="field"><label for="ordre">Ordre d’affichage</label><input id="ordre" name="ordre" type="number" min="0" max="999" value="<?= e(input_value($values, 'ordre', 10)) ?>"></div></div>
            <label class="check"><input type="checkbox" name="actif" value="1" <?= ($values['actif'] ?? true) ? 'checked' : '' ?>><span>Afficher cette personne sur le site<small>Décochez pour masquer temporairement la fiche.</small></span></label>
          </section>
          <section class="form-section"><h2>Contact</h2>
            <div class="field-grid"><div class="field"><label for="telephone">Téléphone</label><input id="telephone" name="telephone" value="<?= e(input_value($values, 'telephone')) ?>"></div><div class="field"><label for="email">E-mail</label><input id="email" name="email" type="email" value="<?= e(input_value($values, 'email')) ?>"></div></div>
            <div class="field"><label for="lienRdv">Lien de prise de rendez-vous</label><input id="lienRdv" name="lienRdv" type="url" value="<?= e(input_value($values, 'lienRdv')) ?>" placeholder="https://..."></div>
            <div class="field-grid"><div class="field"><label for="instagram">Instagram</label><input id="instagram" name="instagram" type="url" value="<?= e(input_value($values, 'instagram')) ?>"></div><div class="field"><label for="facebook">Facebook</label><input id="facebook" name="facebook" type="url" value="<?= e(input_value($values, 'facebook')) ?>"></div></div>
            <div class="field"><label for="linkedin">LinkedIn</label><input id="linkedin" name="linkedin" type="url" value="<?= e(input_value($values, 'linkedin')) ?>"></div>
          </section>
          <div class="form-actions"><button class="button" type="submit">Enregistrer la fiche</button><a class="button button--secondary" href="?action=professionals">Annuler</a></div>
        </div>
        <aside class="side-stack">
          <div class="side-card"><h2>Photo du professionnel</h2><p>JPG, PNG ou WebP. Une photo horizontale d’au moins 1200 px est idéale.</p>
            <img id="professional-preview" class="current-image" src="<?= $photo !== '' ? '?action=media&amp;path=' . rawurlencode(frontmatter_image_to_repo_path($photo)) : '' ?>" alt="Aperçu" <?= $photo === '' ? 'hidden' : '' ?>>
            <div class="field"><label for="photo"><?= $photo === '' ? 'Ajouter une photo' : 'Remplacer la photo' ?></label><input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" data-preview="professional-preview"></div>
          </div>
          <?php render_publish_note(); ?>
        </aside>
      </form>
    <?php
    render_footer();
}

function delete_professional(GitHubRepository $repo): void
{
    $id = is_string($_GET['id'] ?? null) ? $_GET['id'] : post_string('id', 70);
    assert_safe_id($id);
    $path = Content::PROFESSIONALS_DIR . '/' . $id . '.md';
    $file = $repo->getFile($path);
    $parsed = Content::parseMarkdown($file['content']);
    $name = (string) ($parsed['data']['nom'] ?? $id);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();
        if (!hash_equals($file['sha'], post_string('source_sha', 100))) throw new RuntimeException('Cette fiche a été modifiée. Rechargez la page.');
        $repo->commit([$path => null], 'Maxime : retrait de ' . $name . ' de l’équipe');
        flash('success', '✓ La fiche de ' . $name . ' a été retirée. Le site sera actualisé dans environ 3 minutes.');
        redirect('?action=professionals');
    }
    render_delete_confirmation('Retirer ' . $name . ' ?', 'La fiche ne sera plus visible sur le site. La photo reste sauvegardée et l’opération pourra être annulée par l’administrateur technique.', 'professional-delete', $id, $file['sha'], '?action=professionals');
}

function render_news(GitHubRepository $repo): void
{
    $entries = load_markdown_entries($repo, Content::NEWS_DIR);
    usort($entries, static fn ($a, $b): int => strcmp((string) ($b['data']['date'] ?? ''), (string) ($a['data']['date'] ?? '')));
    render_header('Les actualités', 'news');
    page_intro('Contenu', 'Les actualités', 'Les nouvelles publiées sur la page Actualités et relayées sur l’accueil.', '<a class="button" href="?action=news-new">+ Écrire une actualité</a>');
    if ($entries === []) echo '<div class="empty"><p>Aucune actualité pour le moment.</p></div>';
    else {
        echo '<ul class="item-list">';
        foreach ($entries as $entry) {
            $data = $entry['data']; $image = (string) ($data['image'] ?? '');
            ?>
              <li class="item-card"><div class="item-card__image"><?php if ($image !== ''): ?><img src="?action=media&amp;path=<?= rawurlencode(frontmatter_image_to_repo_path($image)) ?>" alt=""><?php else: ?>ACTU<?php endif; ?></div>
                <div><h2><?= e($data['titre'] ?? '') ?></h2><div class="item-card__meta"><p><?= e(format_french_date((string) ($data['date'] ?? ''))) ?></p><span class="status <?= ($data['publie'] ?? true) ? '' : 'status--muted' ?>"><?= ($data['publie'] ?? true) ? 'Publiée' : 'Brouillon' ?></span></div></div>
                <div class="item-card__actions"><a class="button button--quiet" href="?action=news-edit&amp;id=<?= e($entry['id']) ?>">Modifier</a><a class="button button--quiet" href="?action=news-delete&amp;id=<?= e($entry['id']) ?>">Supprimer</a></div>
              </li>
            <?php
        }
        echo '</ul>';
    }
    render_footer();
}

function render_news_edit(GitHubRepository $repo): void
{
    $id = is_string($_GET['id'] ?? null) ? $_GET['id'] : '';
    assert_safe_id($id);
    $file = $repo->getFile(Content::NEWS_DIR . '/' . $id . '.md');
    $parsed = Content::parseMarkdown($file['content']);
    render_news_form($parsed['data'], $parsed['body'], $file['sha'], false, [], $id);
}

function save_news(GitHubRepository $repo): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('?action=news');
    verify_csrf();
    $isNew = post_string('is_new', 5) === '1';
    $id = $isNew ? Content::slug(post_string('slug', 70)) : post_string('id', 70);
    assert_safe_id($id);
    $path = Content::NEWS_DIR . '/' . $id . '.md';
    $expectedSha = post_string('source_sha', 100);
    $existingImage = '';
    $errors = [];
    if ($isNew && $repo->fileExists($path)) $errors[] = 'Une actualité utilise déjà cette adresse. Modifiez le nom court.';
    if (!$isNew) {
        $current = $repo->getFile($path);
        if (!hash_equals($current['sha'], $expectedSha)) throw new RuntimeException('Cette actualité a été modifiée entre-temps. Rechargez-la.');
        $currentParsed = Content::parseMarkdown($current['content']);
        $existingImage = (string) ($currentParsed['data']['image'] ?? '');
    }
    $values = [
        'titre' => post_string('titre', 180), 'date' => post_string('date', 10), 'image' => $existingImage,
        'imageAlt' => post_string('imageAlt', 220), 'chapo' => post_string('chapo', 500), 'publie' => isset($_POST['publie']),
    ];
    $body = Content::safeBody(post_string('body', 25000));
    if ($values['titre'] === '') $errors[] = 'Le titre est obligatoire.';
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $values['date']);
    if (!$date || $date->format('Y-m-d') !== $values['date']) $errors[] = 'La date n’est pas valide.';
    if ($values['chapo'] === '') $errors[] = 'Le résumé est obligatoire.';
    if ($body === '') $errors[] = 'Le texte de l’actualité est obligatoire.';
    $changes = [];
    $upload = $_FILES['image'] ?? ['error' => UPLOAD_ERR_NO_FILE];
    if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        try {
            $imagePath = 'src/assets/images/actualites/' . $id . '.jpg';
            $changes[$imagePath] = ImageProcessor::fromUpload($upload, 'jpg');
            $values['image'] = '../../assets/images/actualites/' . $id . '.jpg';
        } catch (Throwable $error) { $errors[] = $error->getMessage(); }
    }
    if ($values['image'] !== '' && $values['imageAlt'] === '') $errors[] = 'Décrivez brièvement l’image pour les personnes qui ne peuvent pas la voir.';
    if ($errors !== []) { render_news_form($values, $body, $expectedSha, $isNew, $errors, $id); return; }
    $changes[$path] = Content::newsMarkdown($values, $body);
    $repo->commit($changes, 'Maxime : ' . ($isNew ? 'publication de ' : 'mise à jour de ') . $values['titre']);
    flash('success', '✓ Enregistré — l’actualité sera en ligne dans environ 3 minutes.');
    redirect('?action=news');
}

function render_news_form(array $values, string $body, string $sourceSha, bool $isNew, array $errors = [], string $id = ''): void
{
    $title = $isNew ? 'Écrire une actualité' : 'Modifier l’actualité';
    render_header($title, 'news');
    page_intro('Actualités', $title, $isNew ? 'Une photo, un titre, un résumé et votre texte : c’est tout.' : 'Corrigez le contenu puis enregistrez.');
    render_errors($errors);
    $image = (string) ($values['image'] ?? '');
    ?>
      <form method="post" action="?action=news-save" enctype="multipart/form-data" class="editor-layout">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="is_new" value="<?= $isNew ? '1' : '0' ?>"><input type="hidden" name="id" value="<?= e($id) ?>"><input type="hidden" name="source_sha" value="<?= e($sourceSha) ?>">
        <div class="form-card">
          <section class="form-section"><h2>La publication</h2>
            <div class="field"><label for="titre">Titre *</label><input id="titre" name="titre" value="<?= e(input_value($values, 'titre')) ?>" data-auto-slug="slug" maxlength="180" required></div>
            <?php if ($isNew): ?><div class="field"><label for="slug">Nom court pour l’adresse *</label><input id="slug" name="slug" value="<?= e($id) ?>" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" maxlength="70" required><small>Exemple : portes-ouvertes-septembre. Non visible dans l’article.</small></div><?php endif; ?>
            <div class="field-grid"><div class="field"><label for="date">Date *</label><input id="date" name="date" type="date" value="<?= e(input_value($values, 'date', date('Y-m-d'))) ?>" required></div><div class="field"><span>État</span><label class="check"><input type="checkbox" name="publie" value="1" <?= ($values['publie'] ?? true) ? 'checked' : '' ?>><span>Publier sur le site<small>Décochez pour conserver un brouillon.</small></span></label></div></div>
            <div class="field"><label for="chapo">Résumé *</label><textarea id="chapo" name="chapo" rows="4" maxlength="500" required><?= e(input_value($values, 'chapo')) ?></textarea><small>Deux ou trois phrases visibles sur la carte de l’actualité.</small></div>
            <div class="field"><label for="body">Texte de l’actualité *</label><textarea id="body" class="body-editor" name="body" maxlength="20000" required><?= e($body) ?></textarea><small>Séparez les paragraphes par une ligne vide. Pour un sous-titre, commencez une ligne par ##.</small></div>
          </section>
          <div class="form-actions"><button class="button" type="submit">Enregistrer l’actualité</button><a class="button button--secondary" href="?action=news">Annuler</a></div>
        </div>
        <aside class="side-stack"><div class="side-card"><h2>Photo de l’actualité</h2><p>Une image horizontale, nette et lumineuse fonctionne le mieux.</p>
          <img id="news-preview" class="current-image" src="<?= $image !== '' ? '?action=media&amp;path=' . rawurlencode(frontmatter_image_to_repo_path($image)) : '' ?>" alt="Aperçu" <?= $image === '' ? 'hidden' : '' ?>>
          <div class="field"><label for="image"><?= $image === '' ? 'Ajouter une image' : 'Remplacer l’image' ?></label><input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" data-preview="news-preview"></div>
          <div class="field"><label for="imageAlt">Description de l’image</label><textarea id="imageAlt" name="imageAlt" rows="3" maxlength="220"><?= e(input_value($values, 'imageAlt')) ?></textarea><small>Exemple : « Groupe pendant un cours collectif chez MFP Sport ».</small></div>
        </div><?php render_publish_note(); ?></aside>
      </form>
    <?php
    render_footer();
}

function delete_news(GitHubRepository $repo): void
{
    $id = is_string($_GET['id'] ?? null) ? $_GET['id'] : post_string('id', 70); assert_safe_id($id);
    $path = Content::NEWS_DIR . '/' . $id . '.md'; $file = $repo->getFile($path); $parsed = Content::parseMarkdown($file['content']); $title = (string) ($parsed['data']['titre'] ?? $id);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf(); if (!hash_equals($file['sha'], post_string('source_sha', 100))) throw new RuntimeException('Cette actualité a été modifiée. Rechargez la page.');
        $repo->commit([$path => null], 'Maxime : suppression de l’actualité ' . $title);
        flash('success', '✓ L’actualité a été supprimée. Le site sera actualisé dans environ 3 minutes.'); redirect('?action=news');
    }
    render_delete_confirmation('Supprimer cette actualité ?', '« ' . $title . ' » ne sera plus visible sur le site. L’historique permet une restauration par l’administrateur technique.', 'news-delete', $id, $file['sha'], '?action=news');
}

function render_photos(): void
{
    render_header('Les photos du site', 'photos');
    page_intro('Images', 'Les photos du site', 'Chaque emplacement est nommé. Remplacer une photo ne peut pas modifier la mise en page.');
    echo '<div class="photos-grid">';
    foreach (Content::sitePhotos() as $photo) {
        ?>
          <article class="photo-card"><img src="?action=media&amp;path=<?= rawurlencode($photo['path']) ?>" alt="Photo actuelle : <?= e($photo['title']) ?>"><div><h2><?= e($photo['title']) ?></h2><p><?= e($photo['description']) ?></p><a class="button button--quiet" href="?action=photo-replace&amp;key=<?= e($photo['key']) ?>">Remplacer</a></div></article>
        <?php
    }
    echo '</div>';
    render_footer();
}

function replace_photo(GitHubRepository $repo): void
{
    $key = is_string($_GET['key'] ?? null) ? $_GET['key'] : post_string('key', 80);
    $photo = null;
    foreach (Content::sitePhotos() as $candidate) if ($candidate['key'] === $key) $photo = $candidate;
    if ($photo === null) throw new RuntimeException('Emplacement de photo introuvable.');
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();
        try {
            $extension = strtolower(pathinfo($photo['path'], PATHINFO_EXTENSION));
            $binary = ImageProcessor::fromUpload($_FILES['image'] ?? ['error' => UPLOAD_ERR_NO_FILE], $extension);
            $repo->commit([$photo['path'] => $binary], 'Maxime : remplacement de ' . $photo['title']);
            flash('success', '✓ Photo remplacée — elle sera visible sur le site dans environ 3 minutes.'); redirect('?action=photos');
        } catch (Throwable $error) { $errors[] = $error->getMessage(); }
    }
    render_header('Remplacer une photo', 'photos'); page_intro('Photos du site', 'Remplacer la photo', $photo['title']); render_errors($errors);
    ?>
      <form method="post" action="?action=photo-replace" enctype="multipart/form-data" class="editor-layout">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="key" value="<?= e($photo['key']) ?>">
        <div class="form-card"><div class="field"><label for="image">Nouvelle photo *</label><input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" data-preview="photo-preview" required><small>JPG, PNG ou WebP, 10 Mo maximum. Une image d’au moins 1200 px est recommandée.</small></div><div class="form-actions"><button class="button" type="submit">Remplacer cette photo</button><a class="button button--secondary" href="?action=photos">Annuler</a></div></div>
        <aside class="side-stack"><div class="side-card"><h2>Aperçu</h2><p><?= e($photo['description']) ?></p><img id="photo-preview" class="current-image" src="?action=media&amp;path=<?= rawurlencode($photo['path']) ?>" alt="Aperçu de la photo"></div><?php render_publish_note(); ?></aside>
      </form>
    <?php
    render_footer();
}

function render_delete_confirmation(string $title, string $description, string $action, string $id, string $sha, string $cancel): void
{
    render_header($title);
    ?>
      <div class="confirm-card"><p class="eyebrow">Confirmation</p><h1><?= e($title) ?></h1><p><?= e($description) ?></p>
        <form method="post" action="?action=<?= e($action) ?>"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= e($id) ?>"><input type="hidden" name="source_sha" value="<?= e($sha) ?>"><div class="form-actions"><button class="button button--danger" type="submit">Oui, confirmer</button><a class="button button--secondary" href="<?= e($cancel) ?>">Annuler</a></div></form>
      </div>
    <?php
    render_footer();
}

function serve_media(GitHubRepository $repo): never
{
    $path = is_string($_GET['path'] ?? null) ? rawurldecode($_GET['path']) : '';
    if (!preg_match('#^src/assets/images/[A-Za-z0-9_./-]+\.(?:jpe?g|png|webp)$#i', $path) || str_contains($path, '..')) { http_response_code(404); exit; }
    try { $file = $repo->getFile($path); } catch (Throwable) { http_response_code(404); exit; }
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    header('Content-Type: ' . match ($extension) { 'png' => 'image/png', 'webp' => 'image/webp', default => 'image/jpeg' });
    header('Content-Length: ' . strlen($file['content'])); header('Cache-Control: private, max-age=300'); echo $file['content']; exit;
}

function frontmatter_image_to_repo_path(string $path): string
{
    $basename = ltrim(str_replace('\\', '/', $path), '/');
    if (str_starts_with($basename, '../../assets/images/')) return 'src/assets/images/' . substr($basename, strlen('../../assets/images/'));
    return $basename;
}

function initials(string $name): string
{
    $words = preg_split('/\s+/', trim($name)) ?: []; $initials = '';
    foreach (array_slice($words, 0, 2) as $word) $initials .= mb_strtoupper(mb_substr($word, 0, 1));
    return $initials;
}

function format_french_date(string $value): string
{
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', substr($value, 0, 10));
    if (!$date) return $value;
    $months = [1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    return $date->format('j') . ' ' . $months[(int) $date->format('n')] . ' ' . $date->format('Y');
}

function render_not_found(): void
{
    http_response_code(404); render_header('Page introuvable');
    echo '<div class="confirm-card"><h1>Cette page n’existe pas</h1><a class="button" href="./">Revenir à l’accueil</a></div>'; render_footer();
}
