<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

admin_require_login();

$full = site_load_full();
$p = pricing_normalize_block($full['pricing']);
$yoga = $p['yoga_courses'];
$st = $p['soft_touch'];
$nu = $p['nuad_thai_slider'];

$csrf = admin_csrf_token();
$saved = isset($_GET['saved']);

/** @param list<array{label: string, value: string}> $rows */
$padRows = static function (array $rows, int $slots = 8): array {
    $out = $rows;
    while (count($out) < $slots) {
        $out[] = ['label' => '', 'value' => ''];
    }
    return $out;
};

$yogaRows = $padRows($yoga['rows']);
$stRows = $padRows($st['rows']);
$nuRows = $padRows($nu['rows']);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Preise · Admin · ina.YOGA</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
  <header class="admin-header">
    <h1>Preise (Startseite)</h1>
    <nav class="admin-nav">
      <a href="index.php">Events</a>
      <a href="partners.php">Kooperationen</a>
      <a href="../index.html" target="_blank">Website</a>
      <a href="logout.php">Abmelden</a>
    </nav>
  </header>

  <main class="admin-main admin-main--form">
    <?php if ($saved) : ?>
      <p class="admin-flash admin-flash--ok">Preise gespeichert.</p>
    <?php endif; ?>

    <form method="post" action="pricing-save.php" class="admin-form admin-form--wide">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

      <h2 class="admin-section-title">Yoga Kurse</h2>
      <p class="admin-hint">Bezeichnung und Preis pro Zeile. Leere Zeilen werden ignoriert (max. 12 Einträge).</p>
      <label>
        Fußnote unter der Tabelle
        <input type="text" name="yoga_footnote" value="<?= htmlspecialchars((string) $yoga['footnote'], ENT_QUOTES, 'UTF-8') ?>">
      </label>
      <?php foreach ($yogaRows as $i => $row) : ?>
        <div class="admin-row-pair">
          <label>Bezeichnung <?= $i + 1 ?>
            <input type="text" name="yoga_label[]" value="<?= htmlspecialchars((string) $row['label'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
          <label>Preis <?= $i + 1 ?>
            <input type="text" name="yoga_value[]" value="<?= htmlspecialchars((string) $row['value'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
        </div>
      <?php endforeach; ?>

      <h2 class="admin-section-title">Bodywork: Soft Touch (Slider)</h2>
      <label>
        Einleitung vor der Liste
        <input type="text" name="soft_touch_intro" value="<?= htmlspecialchars((string) $st['intro'], ENT_QUOTES, 'UTF-8') ?>">
      </label>
      <?php foreach ($stRows as $i => $row) : ?>
        <div class="admin-row-pair">
          <label>Bezeichnung <?= $i + 1 ?>
            <input type="text" name="soft_touch_label[]" value="<?= htmlspecialchars((string) $row['label'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
          <label>Preis <?= $i + 1 ?>
            <input type="text" name="soft_touch_value[]" value="<?= htmlspecialchars((string) $row['value'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
        </div>
      <?php endforeach; ?>

      <h2 class="admin-section-title">Bodywork: Nuad Thai (nur Slider)</h2>
      <p class="admin-hint">Die Preistabelle im Nuad-Thai-Modal bleibt fest im HTML und wird hier nicht geändert.</p>
      <label>
        Einleitung vor der Liste
        <input type="text" name="nuad_slider_intro" value="<?= htmlspecialchars((string) $nu['intro'], ENT_QUOTES, 'UTF-8') ?>">
      </label>
      <?php foreach ($nuRows as $i => $row) : ?>
        <div class="admin-row-pair">
          <label>Bezeichnung <?= $i + 1 ?>
            <input type="text" name="nuad_slider_label[]" value="<?= htmlspecialchars((string) $row['label'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
          <label>Preis <?= $i + 1 ?>
            <input type="text" name="nuad_slider_value[]" value="<?= htmlspecialchars((string) $row['value'], ENT_QUOTES, 'UTF-8') ?>">
          </label>
        </div>
      <?php endforeach; ?>

      <div class="admin-form-actions">
        <button type="submit">Preise speichern</button>
        <a href="index.php" class="admin-link-cancel">Zurück zu Events</a>
      </div>
    </form>
  </main>
</body>
</html>
