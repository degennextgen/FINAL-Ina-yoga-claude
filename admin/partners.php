<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

admin_require_login();

$full = site_load_full();
$partners = $full['partners'];
usort($partners, static function (array $a, array $b): int {
    return ((int) ($a['sort'] ?? 0)) <=> ((int) ($b['sort'] ?? 0));
});

$csrf = admin_csrf_token();
$saved = isset($_GET['saved']);
$deleted = isset($_GET['deleted']);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kooperationen · Admin · ina.YOGA</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
  <header class="admin-header">
    <h1>Kooperationen</h1>
    <nav class="admin-nav">
      <a href="partner-form.php">Neuer Partner</a>
      <a href="index.php">Events</a>
      <a href="pricing.php">Preise</a>
      <a href="../index.html" target="_blank">Website</a>
      <a href="logout.php">Abmelden</a>
    </nav>
  </header>

  <main class="admin-main">
    <?php if ($saved) : ?>
      <p class="admin-flash admin-flash--ok">Kooperation gespeichert.</p>
    <?php endif; ?>
    <?php if ($deleted) : ?>
      <p class="admin-flash admin-flash--ok">Kooperation gelöscht.</p>
    <?php endif; ?>

    <p class="admin-hint">Einträge erscheinen auf der Website in der Sektion „Kooperationen“ (mit Logo) und im Footer (nur Text + Link).</p>

    <table class="admin-table">
      <thead>
        <tr>
          <th>Reihenfolge</th>
          <th>Text</th>
          <th>Partner</th>
          <th>Logo</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($partners as $p) : ?>
          <tr>
            <td><?= (int) ($p['sort'] ?? 0) + 1 ?></td>
            <td><?= htmlspecialchars((string) ($p['prefix'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) ($p['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= ((string) ($p['logo'] ?? '')) !== '' ? 'ja' : '—' ?></td>
            <td class="admin-actions">
              <a href="partner-form.php?id=<?= urlencode((string) ($p['id'] ?? '')) ?>">Bearbeiten</a>
              <form method="post" action="partner-delete.php" class="admin-inline-form" onsubmit="return confirm('Kooperation wirklich löschen?');">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($p['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="admin-btn-danger">Löschen</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (count($partners) === 0) : ?>
          <tr><td colspan="5">Noch keine Kooperationen angelegt.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </main>
</body>
</html>
