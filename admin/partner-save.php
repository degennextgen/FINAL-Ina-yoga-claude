<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

admin_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: partners.php');
    exit;
}

if (!admin_verify_csrf($_POST['csrf'] ?? null)) {
    http_response_code(403);
    exit('Ungültige Anfrage.');
}

$id = trim((string) ($_POST['id'] ?? ''));
$file = isset($_FILES['logo']) && is_array($_FILES['logo']) ? $_FILES['logo'] : null;

$existing = null;
$existingLogo = null;
if ($id !== '') {
    foreach (site_load_full()['partners'] as $p) {
        if (is_array($p) && ($p['id'] ?? '') === $id) {
            $existing = $p;
            $existingLogo = isset($p['logo']) ? (string) $p['logo'] : null;
            break;
        }
    }
    if ($existing === null) {
        http_response_code(404);
        exit('Kooperation nicht gefunden.');
    }
}

try {
    $normalized = partners_normalize_input($_POST, $file, $existingLogo, $existing !== null);
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    exit(htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

$sort = isset($_POST['sort']) ? max(0, min(99, (int) $_POST['sort'])) : 0;

$newId = $id !== '' ? $id : partners_generate_id();

$record = array_merge($normalized, [
    'id' => $newId,
    'sort' => $sort,
]);

$full = site_load_full();
$list = $full['partners'];
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

usort($list, static function (array $a, array $b): int {
    $c = ((int) ($a['sort'] ?? 0)) <=> ((int) ($b['sort'] ?? 0));
    if ($c !== 0) {
        return $c;
    }
    return strcmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
});
foreach ($list as $i => &$row) {
    $row['sort'] = $i;
}
unset($row);

$full['partners'] = partners_normalize_list($list);
site_save_full($full);

header('Location: partners.php?saved=1');
exit;
