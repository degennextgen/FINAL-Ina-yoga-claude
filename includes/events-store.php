<?php
declare(strict_types=1);

/**
 * Site-Daten (Events + Preise) in data/site.json.
 * Legacy: data/events.json wird nur gelesen, wenn site.json fehlt (kein Auto-Write beim Lesen).
 */

function site_str_clip(string $s, int $max): string
{
    if (function_exists('mb_substr')) {
        return mb_substr($s, 0, $max, 'UTF-8');
    }
    return strlen($s) <= $max ? $s : substr($s, 0, $max);
}

function site_data_path(): string
{
    return dirname(__DIR__) . '/data/site.json';
}

function events_legacy_path(): string
{
    return dirname(__DIR__) . '/data/events.json';
}

/**
 * @return array{
 *   yoga_courses: array{rows: list<array{label: string, value: string}>, footnote: string},
 *   soft_touch: array{intro: string, rows: list<array{label: string, value: string}>},
 *   nuad_thai_slider: array{intro: string, rows: list<array{label: string, value: string}>}
 * }
 */
function pricing_default_full(): array
{
    return [
        'yoga_courses' => [
            'rows' => [
                ['label' => 'Drop-in', 'value' => '€ 19,-'],
                ['label' => '5er Block', 'value' => '€ 90,-'],
                ['label' => '10er Block', 'value' => '€ 165,-'],
            ],
            'footnote' => '(Einheiten á 75 Minuten)',
        ],
        'soft_touch' => [
            'intro' => 'Preistabelle:',
            'rows' => [
                ['label' => '50 min', 'value' => '€ 55'],
                ['label' => '70 min', 'value' => '€ 75'],
                ['label' => '90 min', 'value' => '€ 95'],
            ],
        ],
        'nuad_thai_slider' => [
            'intro' => 'Preistabelle (je Einheit):',
            'rows' => [
                ['label' => '50 min', 'value' => '€ 55'],
                ['label' => '70 min', 'value' => '€ 75'],
                ['label' => '90 min', 'value' => '€ 95'],
            ],
        ],
    ];
}

/**
 * @param array<string, mixed> $p
 * @return array<string, mixed>
 */
function pricing_normalize_block(array $p): array
{
    $defaults = pricing_default_full();
    $out = [];
    foreach ($defaults as $key => $template) {
        $block = isset($p[$key]) && is_array($p[$key]) ? $p[$key] : [];
        $rows = [];
        if (isset($block['rows']) && is_array($block['rows'])) {
            foreach ($block['rows'] as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $label = trim((string) ($row['label'] ?? ''));
                $value = trim((string) ($row['value'] ?? ''));
                if ($label === '' && $value === '') {
                    continue;
                }
                $rows[] = [
                    'label' => site_str_clip($label, 120),
                    'value' => site_str_clip($value, 120),
                ];
                if (count($rows) >= 12) {
                    break;
                }
            }
        }
        if ($key === 'yoga_courses') {
            $fn = isset($block['footnote']) ? trim((string) $block['footnote']) : '';
            $out[$key] = [
                'rows' => $rows !== [] ? $rows : $template['rows'],
                'footnote' => $fn !== '' ? site_str_clip($fn, 200) : (string) $template['footnote'],
            ];
        } else {
            $intro = isset($block['intro']) ? trim((string) $block['intro']) : '';
            $out[$key] = [
                'intro' => $intro !== '' ? site_str_clip($intro, 200) : (string) $template['intro'],
                'rows' => $rows !== [] ? $rows : $template['rows'],
            ];
        }
    }
    return $out;
}

/**
 * @return array{events: list<array<string, mixed>>, pricing: array<string, mixed>}
 */
function site_load_full(): array
{
    $sitePath = site_data_path();
    $defaults = pricing_default_full();

    if (is_readable($sitePath)) {
        $json = file_get_contents($sitePath);
        if ($json === false) {
            return ['events' => [], 'pricing' => $defaults];
        }
        $data = json_decode($json, true);
        if (!is_array($data)) {
            return ['events' => [], 'pricing' => $defaults];
        }
        $events = isset($data['events']) && is_array($data['events']) ? array_values($data['events']) : [];
        $pricingIn = isset($data['pricing']) && is_array($data['pricing']) ? $data['pricing'] : [];
        $pricing = pricing_normalize_block($pricingIn);

        return ['events' => $events, 'pricing' => $pricing];
    }

    $events = [];
    $legacy = events_legacy_path();
    if (is_readable($legacy)) {
        $json = file_get_contents($legacy);
        if ($json !== false) {
            $old = json_decode($json, true);
            if (is_array($old) && isset($old['events']) && is_array($old['events'])) {
                $events = array_values($old['events']);
            }
        }
    }

    return ['events' => $events, 'pricing' => $defaults];
}

/**
 * @param array{events: list<array<string, mixed>>, pricing: array<string, mixed>} $data
 */
function site_save_full(array $data): void
{
    $path = site_data_path();
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $payload = [
        'events' => array_values($data['events']),
        'pricing' => pricing_normalize_block($data['pricing']),
    ];
    $json = json_encode(
        $payload,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
    );
    if (file_put_contents($path, $json) === false) {
        throw new RuntimeException('Could not write site file.');
    }
}

/**
 * @return array{events: list<array<string, mixed>>}
 */
function events_load_raw(): array
{
    $full = site_load_full();
    return ['events' => $full['events']];
}

/**
 * @param array{events: list<array<string, mixed>>} $data
 */
function events_save_raw(array $data): void
{
    $full = site_load_full();
    $full['events'] = $data['events'];
    site_save_full($full);
}

/**
 * @return array<string, mixed>
 */
function pricing_public_payload(array $pricing): array
{
    $p = pricing_normalize_block($pricing);
    $out = [];
    foreach ($p as $key => $block) {
        if ($key === 'yoga_courses') {
            $out[$key] = [
                'rows' => $block['rows'],
                'footnote' => (string) ($block['footnote'] ?? ''),
            ];
        } else {
            $out[$key] = [
                'intro' => (string) ($block['intro'] ?? ''),
                'rows' => $block['rows'],
            ];
        }
    }
    return $out;
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

/**
 * @param array<string, mixed> $post
 * @return array<string, mixed>
 */
function pricing_parse_from_post(array $post): array
{
    $defaults = pricing_default_full();

    $yogaFoot = trim((string) ($post['yoga_footnote'] ?? ''));
    if ($yogaFoot === '') {
        $yogaFoot = (string) $defaults['yoga_courses']['footnote'];
    } else {
        $yogaFoot = site_str_clip($yogaFoot, 200);
    }

    $yogaLabels = isset($post['yoga_label']) && is_array($post['yoga_label']) ? $post['yoga_label'] : [];
    $yogaValues = isset($post['yoga_value']) && is_array($post['yoga_value']) ? $post['yoga_value'] : [];
    $yogaRows = [];
    $n = min(count($yogaLabels), count($yogaValues), 12);
    for ($i = 0; $i < $n; $i++) {
        $label = trim((string) $yogaLabels[$i]);
        $value = trim((string) $yogaValues[$i]);
        if ($label === '' && $value === '') {
            continue;
        }
        $yogaRows[] = ['label' => site_str_clip($label, 120), 'value' => site_str_clip($value, 120)];
    }

    $stIntro = trim((string) ($post['soft_touch_intro'] ?? ''));
    if ($stIntro === '') {
        $stIntro = (string) $defaults['soft_touch']['intro'];
    } else {
        $stIntro = site_str_clip($stIntro, 200);
    }
    $stL = isset($post['soft_touch_label']) && is_array($post['soft_touch_label']) ? $post['soft_touch_label'] : [];
    $stV = isset($post['soft_touch_value']) && is_array($post['soft_touch_value']) ? $post['soft_touch_value'] : [];
    $stRows = [];
    $n2 = min(count($stL), count($stV), 12);
    for ($i = 0; $i < $n2; $i++) {
        $label = trim((string) $stL[$i]);
        $value = trim((string) $stV[$i]);
        if ($label === '' && $value === '') {
            continue;
        }
        $stRows[] = ['label' => site_str_clip($label, 120), 'value' => site_str_clip($value, 120)];
    }

    $nuIntro = trim((string) ($post['nuad_slider_intro'] ?? ''));
    if ($nuIntro === '') {
        $nuIntro = (string) $defaults['nuad_thai_slider']['intro'];
    } else {
        $nuIntro = site_str_clip($nuIntro, 200);
    }
    $nuL = isset($post['nuad_slider_label']) && is_array($post['nuad_slider_label']) ? $post['nuad_slider_label'] : [];
    $nuV = isset($post['nuad_slider_value']) && is_array($post['nuad_slider_value']) ? $post['nuad_slider_value'] : [];
    $nuRows = [];
    $n3 = min(count($nuL), count($nuV), 12);
    for ($i = 0; $i < $n3; $i++) {
        $label = trim((string) $nuL[$i]);
        $value = trim((string) $nuV[$i]);
        if ($label === '' && $value === '') {
            continue;
        }
        $nuRows[] = ['label' => site_str_clip($label, 120), 'value' => site_str_clip($value, 120)];
    }

    return [
        'yoga_courses' => [
            'rows' => $yogaRows !== [] ? $yogaRows : $defaults['yoga_courses']['rows'],
            'footnote' => $yogaFoot,
        ],
        'soft_touch' => [
            'intro' => $stIntro,
            'rows' => $stRows !== [] ? $stRows : $defaults['soft_touch']['rows'],
        ],
        'nuad_thai_slider' => [
            'intro' => $nuIntro,
            'rows' => $nuRows !== [] ? $nuRows : $defaults['nuad_thai_slider']['rows'],
        ],
    ];
}
