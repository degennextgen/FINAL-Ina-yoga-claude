<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

admin_require_login();

$raw = events_load_raw();
$events = $raw['events'];
$today = events_today_vienna();

usort($events, static function (array $a, array $b): int {
    $as = trim((string) ($a['date_start'] ?? ''));
    $bs = trim((string) ($b['date_start'] ?? ''));
    if ($as === '' && $bs === '') {
        return strcmp((string) ($a['title'] ?? ''), (string) ($b['title'] ?? ''));
    }
    if ($as === '') {
        return 1;
    }
    if ($bs === '') {
        return -1;
    }
    return strcmp($as, $bs);
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
  <title>Events · Admin · ina.YOGA</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
  <header class="admin-header">
    <h1>Events</h1>
    <nav class="admin-nav">
      <a href="event-form.php">Neues Event</a>
      <a href="pricing.php">Preise</a>
      <a href="../index.html" target="_blank">Website</a>
      <a href="logout.php">Abmelden</a>
    </nav>
  </header>

  <main class="admin-main">
    <?php if ($saved) : ?>
      <p class="admin-flash admin-flash--ok">Gespeichert.</p>
    <?php endif; ?>
    <?php if ($deleted) : ?>
      <p class="admin-flash admin-flash--ok">Gelöscht.</p>
    <?php endif; ?>
    <table class="admin-table">
      <thead>
        <tr>
          <th>Datum</th>
          <th>Titel</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($events as $e) :
            $ds = trim((string) ($e['date_start'] ?? ''));
            $de = trim((string) ($e['date_end'] ?? ''));
            $dateLabel = $ds === '' ? '— (auf Anfrage)' : htmlspecialchars($ds . ($de !== '' && $de !== $ds ? ' – ' . $de : ''), ENT_QUOTES, 'UTF-8');
            $past = events_is_past_event($e, $today);
            ?>
          <tr class="<?= $past ? 'admin-row--past' : '' ?>">
            <td><?= $dateLabel ?></td>
            <td><?= htmlspecialchars((string) ($e['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= $past ? '<span class="admin-badge admin-badge--past">Vergangen</span>' : '<span class="admin-badge admin-badge--live">Sichtbar*</span>' ?></td>
            <td class="admin-actions">
              <a href="event-form.php?id=<?= urlencode((string) ($e['id'] ?? '')) ?>">Bearbeiten</a>
              <form method="post" action="event-delete.php" class="admin-inline-form" onsubmit="return confirm('Event wirklich löschen?');">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($e['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="admin-btn-danger">Löschen</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (count($events) === 0) : ?>
          <tr><td colspan="4">Noch keine Events angelegt.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <p class="admin-footnote">* Auf der Startseite erscheinen nur Events, die noch nicht vorbei sind (Kalendertag, Zeitzone Europa/Wien).</p>
  </main>
</body>
</html>
