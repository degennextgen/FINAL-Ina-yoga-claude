<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

admin_require_login();

$id = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
$event = null;
if ($id !== '') {
    foreach (events_load_raw()['events'] as $e) {
        if (is_array($e) && ($e['id'] ?? '') === $id) {
            $event = $e;
            break;
        }
    }
    if ($event === null) {
        http_response_code(404);
        exit('Event nicht gefunden.');
    }
}

$csrf = admin_csrf_token();
$isEdit = $event !== null;

$title = $isEdit ? (string) ($event['title'] ?? '') : '';
$dateStart = $isEdit ? (string) ($event['date_start'] ?? '') : '';
$dateEnd = $isEdit ? (string) ($event['date_end'] ?? '') : '';
$time = $isEdit ? (string) ($event['time'] ?? '') : '';
$location = $isEdit ? (string) ($event['location'] ?? '') : '';
$excerpt = $isEdit ? (string) ($event['excerpt'] ?? '') : '';
$body = $isEdit ? (string) ($event['body'] ?? '') : '';
$image = $isEdit ? (string) ($event['image'] ?? '') : '';
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $isEdit ? 'Event bearbeiten' : 'Neues Event' ?> · Admin</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
  <header class="admin-header">
    <h1><?= $isEdit ? 'Event bearbeiten' : 'Neues Event' ?></h1>
    <nav class="admin-nav">
      <a href="index.php">← Übersicht</a>
    </nav>
  </header>

  <main class="admin-main admin-main--form">
    <form method="post" action="event-save.php" enctype="multipart/form-data" class="admin-form admin-form--wide">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
      <?php if ($isEdit) : ?>
        <input type="hidden" name="id" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">
      <?php endif; ?>

      <label>
        Eventname <span class="req">*</span>
        <input type="text" name="title" required value="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
      </label>

      <div class="admin-row">
        <label>
          Datum Start (optional)
          <input type="date" name="date_start" value="<?= htmlspecialchars($dateStart, ENT_QUOTES, 'UTF-8') ?>">
        </label>
        <label>
          Datum Ende (optional, bei Mehrtages-Events)
          <input type="date" name="date_end" value="<?= htmlspecialchars($dateEnd, ENT_QUOTES, 'UTF-8') ?>">
        </label>
      </div>
      <p class="admin-hint">Leer lassen → auf der Website wird „Termin auf Anfrage“ angezeigt; das Event bleibt sichtbar, bis Sie es löschen.</p>

      <label>
        Uhrzeit (optional)
        <input type="text" name="time" placeholder="z. B. 08.00 – 10.00" value="<?= htmlspecialchars($time, ENT_QUOTES, 'UTF-8') ?>">
      </label>

      <label>
        Location (optional, Zeilenumbruch für zweite Zeile)
        <textarea name="location" rows="3"><?= htmlspecialchars($location, ENT_QUOTES, 'UTF-8') ?></textarea>
      </label>

      <label>
        Kurzbeschreibung (Karte)
        <textarea name="excerpt" rows="2"><?= htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8') ?></textarea>
      </label>

      <label>
        Beschreibung (Modal) <span class="req">*</span>
        <textarea name="body" rows="6" required><?= htmlspecialchars($body, ENT_QUOTES, 'UTF-8') ?></textarea>
      </label>

      <label>
        Bild <?= $isEdit ? '(optional ersetzen)' : '(erforderlich)' ?>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" <?= $isEdit ? '' : 'required' ?>>
      </label>
      <?php if ($isEdit && $image !== '') : ?>
        <p class="admin-preview">Aktuell: <code><?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?></code><br>
          <img src="../<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-thumb"></p>
      <?php endif; ?>

      <div class="admin-form-actions">
        <button type="submit"><?= $isEdit ? 'Speichern' : 'Anlegen' ?></button>
        <a href="index.php" class="admin-link-cancel">Abbrechen</a>
      </div>
    </form>
  </main>
</body>
</html>
