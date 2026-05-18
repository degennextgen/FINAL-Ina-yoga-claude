<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

admin_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: pricing.php');
    exit;
}

if (!admin_verify_csrf($_POST['csrf'] ?? null)) {
    http_response_code(403);
    exit('Ungültige Anfrage.');
}

try {
    $pricing = pricing_parse_from_post($_POST);
} catch (Throwable $e) {
    http_response_code(400);
    exit('Ungültige Eingabe.');
}

$full = site_load_full();
$full['pricing'] = pricing_normalize_block($pricing);
site_save_full($full);

header('Location: pricing.php?saved=1');
exit;
