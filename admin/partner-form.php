<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

admin_require_login();

$id = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
$partner = null;
if ($id !== '') {
    foreach (site_load_full()['partners'] as $p) {
        if (is_array($p) && ($p['id'] ?? '') === $id) {
            $partner = $p;
            break;
        }
    }
    if ($partner === null) {
        http_response_code(404);
        exit('Kooperation nicht gefunden.');
    }
}

$csrf = admin_csrf_token();
$isEdit = $partner !== null;

$prefix = $isEdit ? (string) ($partner['prefix'] ?? '') : '';
$name = $isEdit ? (string) ($partner['name'] ?? '') : '';
$url = $isEdit ? (string) ($partner['url'] ?? '') : '';
$logo = $isEdit ? (string) ($partner['logo'] ?? '') : '';
$sort = $isEdit ? (int) ($partner['sort'] ?? 0) : count(site_load_full()['partners']);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $isEdit ? 'Kooperation bearbeiten' : 'Neue Kooperation' ?> · Admin</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
  <header class="admin-header">
    <h1><?= $isEdit ? 'Kooperation bearbeiten' : 'Neue Kooperation' ?></h1>
    <nav class="admin-nav">
      <a href="partners.php">← Übersicht</a>
      <a href="index.php">Events</a>
      <a href="pricing.php">Preise</a>
    </nav>
  </header>

  <main class="admin-main admin-main--form">
    <form method="post" action="partner-save.php" enctype="multipart/form-data" class="admin-form admin-form--wide">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
      <?php if ($isEdit) : ?>
        <input type="hidden" name="id" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">
      <?php endif; ?>

      <label>
        Text davor (z. B. „website by“) <span class="req">*</span>
        <input type="text" name="prefix" required value="<?= htmlspecialchars($prefix, ENT_QUOTES, 'UTF-8') ?>" placeholder="website by">
      </label>

      <label>
        Firmenname <span class="req">*</span>
        <input type="text" name="name" required value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" placeholder="HR CREATIVE LAB">
      </label>

      <label>
        Link (URL) <span class="req">*</span>
        <input type="url" name="url" required value="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" placeholder="https://…">
      </label>

      <label>
        Reihenfolge (0 = zuerst)
        <input type="number" name="sort" min="0" max="99" value="<?= (int) $sort ?>">
      </label>

      <label>
        Logo <?= $isEdit ? '(optional ersetzen)' : '(erforderlich für Kooperationen-Sektion)' ?>
        <input type="file" name="logo" accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml" <?= $isEdit ? '' : 'required' ?>>
      </label>
      <?php if ($isEdit && $logo !== '') : ?>
        <p class="admin-hint">Aktuelles Logo: <img src="../<?= htmlspecialchars($logo, ENT_QUOTES, 'UTF-8') ?>" alt="" style="max-height:48px;vertical-align:middle;margin-left:0.5rem"></p>
      <?php endif; ?>

      <button type="submit">Speichern</button>
      <a href="partners.php" class="admin-link-cancel">Abbrechen</a>
    </form>
  </main>
</body>
</html>
