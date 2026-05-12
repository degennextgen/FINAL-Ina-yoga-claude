<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

admin_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (!admin_verify_csrf($_POST['csrf'] ?? null)) {
    http_response_code(403);
    exit('Ungültige Anfrage.');
}

$id = trim((string) ($_POST['id'] ?? ''));
$file = isset($_FILES['image']) && is_array($_FILES['image']) ? $_FILES['image'] : null;

$existing = null;
$existingImage = null;
if ($id !== '') {
    foreach (events_load_raw()['events'] as $e) {
        if (is_array($e) && ($e['id'] ?? '') === $id) {
            $existing = $e;
            $existingImage = isset($e['image']) ? (string) $e['image'] : null;
            break;
        }
    }
    if ($existing === null) {
        http_response_code(404);
        exit('Event nicht gefunden.');
    }
}

try {
    $normalized = events_normalize_input($_POST, $file, $existingImage);
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    exit(htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

$now = (new DateTimeImmutable('now', new DateTimeZone('Europe/Vienna')))->format('c');

$newId = $id !== '' ? $id : events_generate_id();

$record = array_merge($normalized, [
    'id' => $newId,
    'updated_at' => $now,
]);

$raw = events_load_raw();
$list = $raw['events'];
$found = false;
foreach ($list as $i => $row) {
    if (is_array($row) && ($row['id'] ?? '') === $newId) {
        $list[$i] = $record;
        $found = true;
        break;
    }
}
if (!$found) {
    $list[] = $record;
}

events_save_raw(['events' => array_values($list)]);

header('Location: index.php?saved=1');
exit;
