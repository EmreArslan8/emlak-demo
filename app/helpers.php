<?php
declare(strict_types=1);

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function url(string $path = '', array $query = []): string
{
    $u = BASE . '/' . ltrim($path, '/');
    $query = array_filter($query, fn($v) => $v !== '' && $v !== null);
    return $query ? $u . '?' . http_build_query($query) : $u;
}

function asset(string $path): string
{
    $file = ROOT . '/public/assets/' . $path;
    $v = is_file($file) ? filemtime($file) : 1;
    return BASE . '/assets/' . $path . '?v=' . $v;
}

/** Unsplash adreslerine boyut parametresi ekler, yerel yüklemeleri olduğu gibi döndürür. */
function img(?string $src, int $w = 1200): string
{
    if (!$src) {
        return BASE . '/assets/img/placeholder.svg';
    }
    if (str_starts_with($src, 'https://images.unsplash.com/') && !str_contains($src, '?')) {
        return $src . '?auto=format&fit=crop&w=' . $w . '&q=75';
    }
    if (str_starts_with($src, '/uploads/')) {
        return BASE . $src;
    }
    return $src;
}

function price(float|int|null $amount, string $type = 'satilik'): string
{
    $s = number_format((float) $amount, 0, ',', '.') . ' ₺';
    return $type === 'kiralik' ? $s . ' <small>/ay</small>' : $s;
}

function price_plain(float|int|null $amount): string
{
    return number_format((float) $amount, 0, ',', '.') . ' ₺';
}

function slugify(string $text): string
{
    $map = ['ç' => 'c', 'Ç' => 'c', 'ğ' => 'g', 'Ğ' => 'g', 'ı' => 'i', 'I' => 'i', 'İ' => 'i',
        'ö' => 'o', 'Ö' => 'o', 'ş' => 's', 'Ş' => 's', 'ü' => 'u', 'Ü' => 'u'];
    $text = str_replace(["'", '’'], '', strtr($text, $map));
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-') ?: 'icerik';
}

function unique_slug(string $table, string $text, ?int $ignoreId = null): string
{
    $base = slugify($text);
    $slug = $base;
    $i = 2;
    while (true) {
        $sql = "SELECT id FROM $table WHERE slug = ?" . ($ignoreId ? ' AND id != ?' : '');
        $params = $ignoreId ? [$slug, $ignoreId] : [$slug];
        if (!db_value($sql, $params)) {
            return $slug;
        }
        $slug = $base . '-' . $i++;
    }
}

function excerpt(string $text, int $len = 140): string
{
    $text = trim(preg_replace('/\s+/', ' ', strip_tags($text)));
    return mb_strlen($text) > $len ? rtrim(mb_substr($text, 0, $len)) . '…' : $text;
}

/** Düz metni paragraf/başlık HTML'ine çevirir ("## " ile başlayan satırlar alt başlık olur). */
function render_text(string $text): string
{
    $out = '';
    foreach (preg_split("/\R{2,}/", trim($text)) as $block) {
        $block = trim($block);
        if ($block === '') {
            continue;
        }
        if (str_starts_with($block, '## ')) {
            $out .= '<h2>' . e(substr($block, 3)) . '</h2>';
        } elseif (preg_match('/^- /', $block)) {
            $items = array_map(fn($l) => '<li>' . e(ltrim($l, '- ')) . '</li>', preg_split('/\R/', $block));
            $out .= '<ul>' . implode('', $items) . '</ul>';
        } else {
            $out .= '<p>' . nl2br(e($block)) . '</p>';
        }
    }
    return $out;
}

function tr_date(?string $date): string
{
    if (!$date) {
        return '';
    }
    $months = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
    $t = strtotime($date);
    return date('j', $t) . ' ' . $months[(int) date('n', $t) - 1] . ' ' . date('Y', $t);
}

function wa_link(string $phone, string $text = ''): string
{
    $digits = preg_replace('/\D/', '', $phone);
    return 'https://wa.me/' . $digits . ($text !== '' ? '?text=' . rawurlencode($text) : '');
}

/** Google Maps gömülü harita (API anahtarı gerektirmez). */
function gmap_iframe(string $query, int $zoom = 15, string $title = 'Harita'): string
{
    $src = 'https://maps.google.com/maps?q=' . rawurlencode($query) . '&z=' . $zoom . '&hl=tr&output=embed';
    return '<iframe src="' . e($src) . '" title="' . e($title) . '" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>';
}

function tel_link(string $phone): string
{
    return 'tel:+' . preg_replace('/\D/', '', $phone);
}

function setting(string $key, string $default = ''): string
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (db_all('SELECT key, value FROM settings') as $row) {
            $cache[$row['key']] = $row['value'];
        }
    }
    return $cache[$key] ?? $default;
}

// ---- İstek / yanıt -------------------------------------------------------

function redirect(string $path): never
{
    header('Location: ' . (str_starts_with($path, 'http') ? $path : url($path)));
    exit;
}

function flash(string $type, ?string $msg = null): ?string
{
    if ($msg !== null) {
        $_SESSION['flash'][$type] = $msg;
        return null;
    }
    $m = $_SESSION['flash'][$type] ?? null;
    unset($_SESSION['flash'][$type]);
    return $m;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
}

function csrf_check(): void
{
    if (!hash_equals(csrf_token(), (string) ($_POST['_token'] ?? ''))) {
        http_response_code(419);
        exit('Oturum süresi doldu. Lütfen sayfayı yenileyip tekrar deneyin.');
    }
}

function input(string $key, mixed $default = ''): mixed
{
    $v = $_POST[$key] ?? $_GET[$key] ?? $default;
    return is_string($v) ? trim($v) : $v;
}

function view(string $name, array $data = [], ?string $layout = 'site/layout'): void
{
    extract($data, EXTR_SKIP);
    ob_start();
    require APP . '/views/' . $name . '.php';
    $content = ob_get_clean();
    if ($layout === null) {
        echo $content;
        return;
    }
    require APP . '/views/' . $layout . '.php';
}

function partial(string $name, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require APP . '/views/' . $name . '.php';
}

function not_found(): never
{
    http_response_code(404);
    view('site/404', ['meta' => ['title' => 'Sayfa bulunamadı'], 'solidHeader' => true]);
    exit;
}

function current_path(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    if (BASE !== '' && str_starts_with($path, BASE)) {
        $path = substr($path, strlen(BASE));
    }
    return '/' . trim($path, '/');
}

function is_active(string $prefix): string
{
    $p = current_path();
    $on = $prefix === '/' ? $p === '/' : str_starts_with($p, $prefix);
    return $on ? 'is-active' : '';
}

// ---- Alan sabitleri ------------------------------------------------------

const LISTING_TYPES = ['satilik' => 'Satılık', 'kiralik' => 'Kiralık'];
const CATEGORIES = ['daire' => 'Daire', 'villa' => 'Villa', 'rezidans' => 'Rezidans', 'mustakil' => 'Müstakil Ev',
    'ofis' => 'Ofis', 'dukkan' => 'Dükkan', 'arsa' => 'Arsa'];
const ROOM_OPTIONS = ['1+0', '1+1', '2+1', '3+1', '4+1', '5+1', '6+2'];
const STATUSES = ['aktif' => 'Yayında', 'pasif' => 'Taslak', 'satildi' => 'Satıldı / Kiralandı'];
const HEATING = ['Kombi (Doğalgaz)', 'Merkezi', 'Yerden Isıtma', 'Klima', 'Isı Pompası', 'Yok'];
const FEATURES = ['Asansör', 'Otopark', 'Kapalı Otopark', 'Güvenlik', 'Site İçerisinde', 'Havuz', 'Spor Salonu',
    'Balkon', 'Teras', 'Bahçe', 'Deniz Manzarası', 'Boğaz Manzarası', 'Ankastre Mutfak', 'Akıllı Ev',
    'Ebeveyn Banyosu', 'Giyinme Odası', 'Şömine', 'Jeneratör', 'Metroya Yakın', 'Krediye Uygun', 'Eşyalı'];

function category_label(string $k): string
{
    return CATEGORIES[$k] ?? ucfirst($k);
}

function auth_user(): ?array
{
    return $_SESSION['admin'] ?? null;
}

function require_admin(): void
{
    if (!auth_user()) {
        redirect('yonetim/giris');
    }
}

/** Satır içi SVG ikon seti (harici bağımlılık yok). */
function icon(string $name, string $class = 'ico'): string
{
    static $paths = [
        'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/>',
        'mail' => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/>',
        'pin' => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>',
        'clock' => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
        'bed' => '<path d="M2 20v-8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v8M2 16h20M6 10V6a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v4"/>',
        'bath' => '<path d="M4 12h16a1 1 0 0 1 1 1v2a5 5 0 0 1-5 5H8a5 5 0 0 1-5-5v-2a1 1 0 0 1 1-1zM6 12V5a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2M7 20l-1 2M17 20l1 2"/>',
        'area' => '<path d="M3 3h18v18H3zM3 9h4M3 15h4M9 3v4M15 3v4"/>',
        'building' => '<rect x="4" y="2" width="16" height="20" rx="1"/><path d="M9 22v-4h6v4M8 6h.01M16 6h.01M12 6h.01M12 10h.01M12 14h.01M16 10h.01M16 14h.01M8 10h.01M8 14h.01"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
        'flame' => '<path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.4-.5-2-1-3-1.1-2.1-.2-4 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.2.4-2.3 1-3.2.2 1.5 1.4 2.7 2.5 2.7z"/>',
        'search' => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
        'check' => '<path d="M20 6 9 17l-5-5"/>',
        'arrow' => '<path d="M5 12h14M12 5l7 7-7 7"/>',
        'eye' => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
        'map' => '<path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3z"/><path d="M9 3v15M15 6v15"/>',
        'grid' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>',
        'filter' => '<path d="M22 3H2l8 9.5V19l4 2v-8.5z"/>',
        'share' => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/>',
        'key' => '<circle cx="7.5" cy="15.5" r="5.5"/><path d="m21 2-9.6 9.6M15.5 7.5l3 3L22 7l-3-3"/>',
        'chart' => '<path d="M3 3v18h18"/><path d="m7 15 4-4 3 3 6-6"/>',
        'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        'home' => '<path d="m3 10 9-7 9 7v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/>',
        'file' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/>',
        'camera' => '<path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>',
        'users' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
        'star' => '<path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z"/>',
        'instagram' => '<rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/>',
        'linkedin' => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'edit' => '<path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>',
        'trash' => '<path d="M3 6h18M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6M10 11v6M14 11v6M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>',
        'inbox' => '<path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.5 5.1 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.5-6.9A2 2 0 0 0 16.8 4H7.2a2 2 0 0 0-1.7 1.1z"/>',
        'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>',
        'external' => '<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14 21 3"/>',
    ];
    if ($name === 'whatsapp') {
        return '<svg class="' . $class . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.5 14.4c-.3-.1-1.8-.9-2-1-.3-.1-.5-.1-.7.1-.2.3-.8 1-.9 1.2-.2.2-.3.2-.6.1-.3-.1-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6l.4-.5c.2-.2.2-.3.3-.5.1-.2 0-.4 0-.5l-.9-2.2c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1.1 2.9 1.2 3.1c.1.2 2.1 3.2 5.1 4.5.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.8-.7 2-1.4.2-.7.2-1.3.2-1.4-.1-.1-.3-.2-.6-.4zM12 21.8c-1.8 0-3.5-.5-5-1.4l-.4-.2-3.7 1 1-3.6-.2-.4a9.8 9.8 0 0 1-1.5-5.2C2.2 6.6 6.6 2.2 12 2.2c2.6 0 5.1 1 6.9 2.9a9.7 9.7 0 0 1 2.9 6.9c0 5.4-4.4 9.8-9.8 9.8zm8.4-18.2A11.8 11.8 0 0 0 12 0C5.5 0 .2 5.3.2 11.8c0 2.1.5 4.1 1.6 5.9L0 24l6.4-1.7a11.8 11.8 0 0 0 5.6 1.4c6.5 0 11.8-5.3 11.8-11.8 0-3.2-1.2-6.1-3.4-8.3z"/></svg>';
    }
    return '<svg class="' . $class . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($paths[$name] ?? '') . '</svg>';
}
