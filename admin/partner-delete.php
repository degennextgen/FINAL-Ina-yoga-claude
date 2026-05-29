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
if ($id === '') {
    header('Location: partners.php');
    exit;
}

$full = site_load_full();
$list = [];
foreach ($full['partners'] as $p) {
    if (!is_array($p) || ($p['id'] ?? '') === $id) {
        continue;
    }
    $list[] = $p;
}

$full['partners'] = partners_normalize_list($list);
site_save_full($full);

header('Location: partners.php?deleted=1');
exit;
