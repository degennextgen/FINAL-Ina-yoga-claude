<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60');

require_once dirname(__DIR__) . '/includes/events-store.php';

try {
    $list = events_public_list();
    echo json_encode(['events' => $list], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Serverfehler'], JSON_THROW_ON_ERROR);
}
