<?php
declare(strict_types=1);

function home(): void
{
    [$featured] = listings_query(['sirala' => ''], 6);
    view('site/home', [
        'featured' => $featured,
        'agents' => db_all('SELECT * FROM agents ORDER BY sort, id LIMIT 4'),
        'posts' => db_all('SELECT * FROM posts WHERE published = 1 ORDER BY published_at DESC LIMIT 3'),
        'districts' => db_all("SELECT district, COUNT(*) c FROM listings WHERE status = 'aktif' GROUP BY district ORDER BY c DESC"),
        'neighborhoods' => array_map('hydrate_listing', db_all(
            "SELECT district, images, '[]' AS features, COUNT(*) c FROM listings WHERE status = 'aktif' AND category IN ('daire','villa','mustakil','rezidans')
             GROUP BY district ORDER BY MAX(price) DESC LIMIT 4"
        )),
        'sold' => array_map('hydrate_listing', db_all("SELECT * FROM listings WHERE status = 'satildi' ORDER BY price DESC LIMIT 3")),
        'stats' => [
            'aktif' => (int) db_value("SELECT COUNT(*) FROM listings WHERE status = 'aktif'"),
            'danisman' => (int) db_value('SELECT COUNT(*) FROM agents'),
        ],
        'meta' => ['title' => setting('site_name') . ' — ' . setting('tagline'), 'raw_title' => true],
    ]);
}

function listings(?string $forcedType = null): void
{
    $f = [
        'tip' => $forcedType ?? input('tip'), 'kategori' => input('kategori'), 'ilce' => input('ilce'), 'oda' => input('oda'),
        'min_fiyat' => input('min_fiyat'), 'max_fiyat' => input('max_fiyat'), 'min_m2' => input('min_m2'), 'max_m2' => input('max_m2'),
        'q' => input('q'), 'sirala' => input('sirala'),
    ];
    $perPage = 9;
    $page = max(1, (int) input('sayfa', 1));
    [$items, $total] = listings_query($f, $perPage, ($page - 1) * $perPage);
    [$mapItems] = listings_query($f, 200);

    $heading = match ($f['tip']) {
        'satilik' => 'Satılık Portföy',
        'kiralik' => 'Kiralık Portföy',
        default => 'Tüm Portföy',
    };
    if ($f['kategori']) {
        $heading = ($f['tip'] ? LISTING_TYPES[$f['tip']] . ' ' : '') . category_label($f['kategori']);
    }
    if ($f['ilce']) {
        $heading = $f['ilce'] . ' — ' . $heading;
    }

    view('site/listings', [
        'items' => $items, 'total' => $total, 'page' => $page, 'pages' => (int) ceil($total / $perPage),
        'f' => $f, 'forcedType' => $forcedType, 'heading' => $heading,
        'mapItems' => array_map(fn($l) => [
            'lat' => $l['lat'], 'lng' => $l['lng'], 'title' => $l['title'], 'url' => url('ilan/' . $l['slug']),
            'price' => price_plain($l['price']) . ($l['type'] === 'kiralik' ? '/ay' : ''), 'img' => img($l['cover'], 400),
        ], array_filter($mapItems, fn($l) => $l['lat'])),
        'districts' => db_all("SELECT DISTINCT district FROM listings WHERE status != 'pasif' ORDER BY district"),
        'meta' => ['title' => $heading, 'description' => "$heading — {$total} ilan. " . setting('site_name') . ' güvencesiyle.'],
    ]);
}

function find_listing(string $slug): array
{
    $l = hydrate_listing(db_one("SELECT * FROM listings WHERE slug = ? AND status != 'pasif'", [$slug]));
    return $l ?: not_found();
}

function listing_show(string $slug, array $extra = []): void
{
    $l = find_listing($slug);
    if (empty($extra)) {
        db_run('UPDATE listings SET views = views + 1 WHERE id = ?', [$l['id']]);
    }
    $agent = $l['agent_id'] ? db_one('SELECT * FROM agents WHERE id = ?', [$l['agent_id']]) : null;
    $similar = array_map('hydrate_listing', db_all(
        "SELECT * FROM listings WHERE id != ? AND status = 'aktif' AND (category = ? OR district = ?) ORDER BY (type = ?) DESC, created_at DESC LIMIT 3",
        [$l['id'], $l['category'], $l['district'], $l['type']]
    ));
    view('site/listing', [
        'l' => $l, 'agent' => $agent, 'similar' => $similar, 'solidHeader' => true, ...$extra,
        'meta' => [
            'title' => $l['title'],
            'description' => excerpt($l['description'], 155),
            'image' => img($l['cover'], 1200),
            'type' => 'product',
        ],
    ]);
}

function listing_inquiry(string $slug): void
{
    csrf_check();
    $l = find_listing($slug);
    [$data, $errors] = validate_message();
    if ($errors) {
        listing_show($slug, ['errors' => $errors, 'old' => $data]);
        return;
    }
    db_insert('messages', $data + ['subject' => 'İlan: ' . $l['title'], 'listing_id' => $l['id']]);
    flash('success', 'Talebiniz alındı. Danışmanımız en kısa sürede sizinle iletişime geçecek.');
    redirect('ilan/' . $slug . '#iletisim-formu');
}

function validate_message(): array
{
    $data = [
        'name' => mb_substr((string) input('name'), 0, 120),
        'email' => mb_substr((string) input('email'), 0, 160),
        'phone' => mb_substr((string) input('phone'), 0, 40),
        'message' => mb_substr((string) input('message'), 0, 3000),
    ];
    $errors = [];
    if ($data['name'] === '') {
        $errors['name'] = 'Lütfen adınızı yazın.';
    }
    if ($data['email'] === '' && $data['phone'] === '') {
        $errors['email'] = 'E-posta veya telefon bilgilerinden en az birini girin.';
    } elseif ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Geçerli bir e-posta adresi girin.';
    }
    if ($data['message'] === '') {
        $errors['message'] = 'Mesajınızı yazın.';
    }
    if (input('website') !== '') { // bal küpü (spam botları)
        $errors['name'] = 'Gönderim reddedildi.';
    }
    return [$data, $errors];
}

function agents(): void
{
    view('site/agents', [
        'agents' => db_all("SELECT a.*, (SELECT COUNT(*) FROM listings l WHERE l.agent_id = a.id AND l.status = 'aktif') AS listing_count
            FROM agents a ORDER BY sort, id"),
        'meta' => ['title' => 'Danışmanlarımız', 'description' => 'Uzman gayrimenkul danışmanı kadromuzla tanışın.'],
    ]);
}

function agent_show(string $slug): void
{
    $a = db_one('SELECT * FROM agents WHERE slug = ?', [$slug]) ?: not_found();
    [$items, $total] = listings_query(['agent_id' => $a['id']], 50);
    view('site/agent', [
        'a' => $a, 'items' => $items, 'total' => $total,
        'meta' => ['title' => $a['name'] . ' — ' . $a['title'], 'description' => excerpt($a['bio'] ?? '', 155), 'image' => img($a['photo'], 800)],
    ]);
}

function about(): void
{
    view('site/about', [
        'agents' => db_all('SELECT * FROM agents ORDER BY sort, id'),
        'meta' => ['title' => 'Hakkımızda', 'description' => setting('site_name') . ' hikâyesi, değerlerimiz ve ekibimiz.'],
    ]);
}

function services(): void
{
    view('site/services', ['meta' => ['title' => 'Hizmetlerimiz', 'description' => 'Satış, kiralama, değerleme, yatırım danışmanlığı ve tapu süreç yönetimi.']]);
}

function blog(): void
{
    $cat = input('kategori');
    $posts = $cat
        ? db_all('SELECT * FROM posts WHERE published = 1 AND category = ? ORDER BY published_at DESC', [$cat])
        : db_all('SELECT * FROM posts WHERE published = 1 ORDER BY published_at DESC');
    view('site/blog', [
        'posts' => $posts, 'cat' => $cat,
        'cats' => db_all('SELECT DISTINCT category FROM posts WHERE published = 1 ORDER BY category'),
        'meta' => ['title' => 'Blog & Haberler', 'description' => 'Gayrimenkul piyasası, yatırım ve mevzuat üzerine güncel yazılar.'],
    ]);
}

function post_show(string $slug): void
{
    $p = db_one('SELECT * FROM posts WHERE slug = ? AND published = 1', [$slug]) ?: not_found();
    view('site/post', [
        'p' => $p,
        'others' => db_all('SELECT * FROM posts WHERE id != ? AND published = 1 ORDER BY published_at DESC LIMIT 3', [$p['id']]),
        'meta' => ['title' => $p['title'], 'description' => $p['excerpt'], 'image' => img($p['cover'], 1200), 'type' => 'article'],
    ]);
}

function contact(array $extra = []): void
{
    view('site/contact', $extra + ['meta' => ['title' => 'İletişim', 'description' => 'Bize ulaşın: ' . setting('phone') . ' · ' . setting('address')]]);
}

function contact_submit(): void
{
    csrf_check();
    [$data, $errors] = validate_message();
    $data['subject'] = mb_substr((string) input('subject', 'Genel'), 0, 120) ?: 'Genel';
    if ($errors) {
        contact(['errors' => $errors, 'old' => $data, 'meta' => ['title' => 'İletişim']]);
        return;
    }
    db_insert('messages', $data);
    flash('success', 'Mesajınız bize ulaştı. Teşekkür ederiz!');
    redirect('iletisim');
}

function sitemap(): void
{
    header('Content-Type: application/xml; charset=utf-8');
    $host = site_host();
    $urls = ['', 'ilanlar', 'satilik', 'kiralik', 'danismanlar', 'hakkimizda', 'hizmetlerimiz', 'blog', 'iletisim'];
    foreach (db_all("SELECT slug FROM listings WHERE status = 'aktif'") as $r) {
        $urls[] = 'ilan/' . $r['slug'];
    }
    foreach (db_all('SELECT slug FROM agents') as $r) {
        $urls[] = 'danisman/' . $r['slug'];
    }
    foreach (db_all('SELECT slug FROM posts WHERE published = 1') as $r) {
        $urls[] = 'blog/' . $r['slug'];
    }
    echo '<?xml version="1.0" encoding="UTF-8"?>', "\n", '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach ($urls as $u) {
        echo '<url><loc>', e($host . url($u)), '</loc></url>';
    }
    echo '</urlset>';
}

function robots(): void
{
    header('Content-Type: text/plain; charset=utf-8');
    echo "User-agent: *\nDisallow: " . url('yonetim') . "\nSitemap: " . site_host() . url('sitemap.xml') . "\n";
}

function site_host(): string
{
    return (($_SERVER['HTTPS'] ?? '') === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
}
