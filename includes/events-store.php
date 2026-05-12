<?php
declare(strict_types=1);

/**
 * Events persistence + filtering (Europe/Vienna, calendar-day rules).
 */

function events_data_path(): string
{
    return dirname(__DIR__) . '/data/events.json';
}

/**
 * @return array{events: list<array<string, mixed>>}
 */
function events_load_raw(): array
{
    $path = events_data_path();
    if (!is_readable($path)) {
        return ['events' => []];
    }
    $json = file_get_contents($path);
    if ($json === false) {
        return ['events' => []];
    }
    $data = json_decode($json, true);
    if (!is_array($data) || !isset($data['events']) || !is_array($data['events'])) {
        return ['events' => []];
    }
    return ['events' => array_values($data['events'])];
}

/**
 * @param array{events: list<array<string, mixed>>} $data
 */
function events_save_raw(array $data): void
{
    $path = events_data_path();
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $payload = json_encode(
        $data,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
    );
    if (file_put_contents($path, $payload) === false) {
        throw new RuntimeException('Could not write events file.');
    }
}

function events_generate_id(): string
{
    return 'evt_' . bin2hex(random_bytes(8));
}

function events_today_vienna(): DateTimeImmutable
{
    return new DateTimeImmutable('today', new DateTimeZone('Europe/Vienna'));
}

/**
 * Visible if no start date (always “Termin auf Anfrage”) or last event day >= today.
 */
function events_is_upcoming_or_today(array $event, DateTimeImmutable $today): bool
{
    $ds = isset($event['date_start']) ? trim((string) $event['date_start']) : '';
    if ($ds === '') {
        return true;
    }
    $de = isset($event['date_end']) ? trim((string) $event['date_end']) : '';
    $end = $de !== '' ? $de : $ds;
    return strcmp($end, $today->format('Y-m-d')) >= 0;
}

/** Für Admin-Ansicht: Event liegt komplett in der Vergangenheit (nur bei gesetztem Datum). */
function events_is_past_event(array $event, ?DateTimeImmutable $today = null): bool
{
    $today = $today ?? events_today_vienna();
    $ds = isset($event['date_start']) ? trim((string) $event['date_start']) : '';
    if ($ds === '') {
        return false;
    }
    $de = isset($event['date_end']) ? trim((string) $event['date_end']) : '';
    $end = $de !== '' ? $de : $ds;
    return strcmp($end, $today->format('Y-m-d')) < 0;
}

/**
 * @param array<string, mixed> $e
 * @return array<string, mixed>
 */
function events_public_payload(array $e): array
{
    return [
        'id' => (string) ($e['id'] ?? ''),
        'title' => (string) ($e['title'] ?? ''),
        'date_start' => isset($e['date_start']) ? (string) $e['date_start'] : '',
        'date_end' => isset($e['date_end']) ? (string) $e['date_end'] : '',
        'time' => isset($e['time']) ? (string) $e['time'] : '',
        'location' => isset($e['location']) ? (string) $e['location'] : '',
        'image' => isset($e['image']) ? (string) $e['image'] : '',
        'excerpt' => isset($e['excerpt']) ? (string) $e['excerpt'] : '',
        'body' => isset($e['body']) ? (string) $e['body'] : '',
    ];
}

/**
 * @return list<array<string, mixed>>
 */
function events_public_list(): array
{
    $raw = events_load_raw();
    $today = events_today_vienna();
    $out = [];
    foreach ($raw['events'] as $e) {
        if (!is_array($e)) {
            continue;
        }
        if (!events_is_upcoming_or_today($e, $today)) {
            continue;
        }
        $out[] = events_public_payload($e);
    }
    usort($out, static function (array $a, array $b): int {
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
        $c = strcmp($as, $bs);
        if ($c !== 0) {
            return $c;
        }
        return strcmp((string) ($a['title'] ?? ''), (string) ($b['title'] ?? ''));
    });
    return $out;
}

/**
 * @param array<string, mixed>|null $file $_FILES['image']
 * @param array<string, mixed> $post
 * @return array<string, mixed>
 */
function events_normalize_input(array $post, ?array $file, ?string $existingImage): array
{
    $title = trim((string) ($post['title'] ?? ''));
    $dateStart = trim((string) ($post['date_start'] ?? ''));
    $dateEnd = trim((string) ($post['date_end'] ?? ''));
    $time = trim((string) ($post['time'] ?? ''));
    $location = trim((string) ($post['location'] ?? ''));
    $excerpt = trim((string) ($post['excerpt'] ?? ''));
    $body = trim((string) ($post['body'] ?? ''));

    if ($title === '') {
        throw new InvalidArgumentException('Eventname ist erforderlich.');
    }

    if ($dateStart !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStart)) {
        throw new InvalidArgumentException('Ungültiges Startdatum.');
    }
    if ($dateEnd !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateEnd)) {
        throw new InvalidArgumentException('Ungültiges Enddatum.');
    }
    if ($dateStart !== '' && $dateEnd !== '' && strcmp($dateEnd, $dateStart) < 0) {
        throw new InvalidArgumentException('Enddatum darf nicht vor dem Startdatum liegen.');
    }

    $imagePath = $existingImage ?? '';
    if ($file !== null && isset($file['error'])) {
        $err = (int) $file['error'];
        if ($err === UPLOAD_ERR_OK) {
            $tmp = (string) ($file['tmp_name'] ?? '');
            if ($tmp === '' || !is_uploaded_file($tmp)) {
                throw new InvalidArgumentException('Ungültiger Upload.');
            }
            $fi = new finfo(FILEINFO_MIME_TYPE);
            $mime = $fi->file($tmp);
            $allowed = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'image/gif' => 'gif',
            ];
            if (!isset($allowed[$mime])) {
                throw new InvalidArgumentException('Nur JPG, PNG, WebP oder GIF erlaubt.');
            }
            $ext = $allowed[$mime];
            $uploadDir = dirname(__DIR__) . '/images/uploads/events';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $basename = 'evt_' . bin2hex(random_bytes(6)) . '.' . $ext;
            $dest = $uploadDir . '/' . $basename;
            if (!move_uploaded_file($tmp, $dest)) {
                throw new InvalidArgumentException('Bild konnte nicht gespeichert werden.');
            }
            $imagePath = 'images/uploads/events/' . $basename;
        } elseif ($err !== UPLOAD_ERR_NO_FILE) {
            throw new InvalidArgumentException('Bild-Upload fehlgeschlagen.');
        }
    }

    if ($imagePath === '') {
        throw new InvalidArgumentException('Bitte ein Bild auswählen oder einen bestehenden Eintrag bearbeiten.');
    }

    return [
        'title' => $title,
        'date_start' => $dateStart,
        'date_end' => $dateEnd,
        'time' => $time,
        'location' => $location,
        'excerpt' => $excerpt,
        'body' => $body,
        'image' => $imagePath,
    ];
}
