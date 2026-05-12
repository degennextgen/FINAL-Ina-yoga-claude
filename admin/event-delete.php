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
if ($id === '') {
    header('Location: index.php');
    exit;
}

$raw = events_load_raw();
$list = [];
foreach ($raw['events'] as $e) {
    if (!is_array($e)) {
        continue;
    }
    if (($e['id'] ?? '') === $id) {
        continue;
    }
    $list[] = $e;
}

events_save_raw(['events' => $list]);

header('Location: index.php?deleted=1');
exit;
