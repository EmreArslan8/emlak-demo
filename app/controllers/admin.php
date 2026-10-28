<?php
declare(strict_types=1);

function admin_view(string $name, array $data = []): void
{
    $data['unread'] = (int) db_value('SELECT COUNT(*) FROM messages WHERE is_read = 0');
    view('admin/' . $name, $data, 'admin/layout');
}

// ---- Oturum --------------------------------------------------------------

function admin_login(array $extra = []): void
{
    if (auth_user()) {
        redirect('yonetim');
    }
    view('admin/login', $extra, null);
}

function admin_login_submit(): void
{
    csrf_check();
    $u = db_one('SELECT * FROM users WHERE username = ?', [input('username')]);
    if (!$u || !password_verify((string) input('password'), $u['password_hash'])) {
        usleep(300000);
        admin_login(['error' => 'Kullanıcı adı veya şifre hatalı.']);
        return;
    }
    session_regenerate_id(true);
    $_SESSION['admin'] = ['id' => $u['id'], 'name' => $u['name'], 'username' => $u['username']];
    redirect('yonetim');
}

function admin_logout(): void
{
    csrf_check();
    unset($_SESSION['admin']);
    session_regenerate_id(true);
    redirect('yonetim/giris');
}

function admin_dashboard(): void
{
    require_admin();
    admin_view('dashboard', [
        'title' => 'Genel Bakış',
        'counts' => [
            'aktif' => (int) db_value("SELECT COUNT(*) FROM listings WHERE status = 'aktif'"),
            'satilik' => (int) db_value("SELECT COUNT(*) FROM listings WHERE status = 'aktif' AND type = 'satilik'"),
            'kiralik' => (int) db_value("SELECT COUNT(*) FROM listings WHERE status = 'aktif' AND type = 'kiralik'"),
            'views' => (int) db_value('SELECT COALESCE(SUM(views), 0) FROM listings'),
            'messages' => (int) db_value('SELECT COUNT(*) FROM messages'),
        ],
        'popular' => array_map('hydrate_listing', db_all('SELECT * FROM listings ORDER BY views DESC LIMIT 5')),
        'messages' => db_all('SELECT m.*, l.title AS listing_title FROM messages m LEFT JOIN listings l ON l.id = m.listing_id ORDER BY m.created_at DESC LIMIT 5'),
    ]);
}

// ---- İlanlar -------------------------------------------------------------

function admin_listings(): void
{
    require_admin();
    $q = input('q');
    $status = input('durum');
    $where = ['1=1'];
    $p = [];
    if ($q !== '') {
        $where[] = '(l.title LIKE ? OR l.district LIKE ?)';
        array_push($p, "%$q%", "%$q%");
    }
    if ($status !== '' && isset(STATUSES[$status])) {
        $where[] = 'l.status = ?';
        $p[] = $status;
    }
    $rows = db_all('SELECT l.*, a.name AS agent_name FROM listings l LEFT JOIN agents a ON a.id = l.agent_id WHERE '
        . implode(' AND ', $where) . ' ORDER BY l.created_at DESC', $p);
    admin_view('listings', ['title' => 'İlanlar', 'rows' => array_map('hydrate_listing', $rows), 'q' => $q, 'status' => $status]);
}

function admin_listing_form(?string $id = null, array $extra = []): void
{
    require_admin();
    $l = $id ? hydrate_listing(db_one('SELECT * FROM listings WHERE id = ?', [(int) $id])) : null;
    if ($id && !$l) {
        not_found();
    }
    $l ??= ['type' => 'satilik', 'category' => 'daire', 'city' => 'İstanbul', 'status' => 'aktif', 'images' => [], 'features' => [], 'featured' => 0];
    admin_view('listing_form', $extra + [
        'title' => $id ? 'İlanı Düzenle' : 'Yeni İlan',
        'l' => $l,
        'agents' => db_all('SELECT id, name FROM agents ORDER BY sort, id'),
    ]);
}

function admin_listing_save(?string $id = null): void
{
    require_admin();
    csrf_check();
    $num = fn($k) => input($k) === '' ? null : (int) str_replace('.', '', (string) input($k));
    $images = array_values(array_filter(array_map('trim', preg_split('/\R/', (string) input('images')))));
    $images = array_merge($images, handle_uploads('uploads'));

    $data = [
        'title' => (string) input('title'),
        'type' => isset(LISTING_TYPES[input('type')]) ? input('type') : 'satilik',
        'category' => isset(CATEGORIES[input('category')]) ? input('category') : 'daire',
        'price' => (float) str_replace(['.', ' '], '', (string) input('price')),
        'city' => (string) input('city', 'İstanbul'),
        'district' => (string) input('district'),
        'neighborhood' => (string) input('neighborhood'),
        'lat' => input('lat') === '' ? null : (float) input('lat'),
        'lng' => input('lng') === '' ? null : (float) input('lng'),
        'rooms' => (string) input('rooms'),
        'gross_m2' => $num('gross_m2'),
        'net_m2' => $num('net_m2'),
        'bathrooms' => $num('bathrooms'),
        'floor' => (string) input('floor'),
        'total_floors' => $num('total_floors'),
        'building_age' => $num('building_age'),
        'heating' => (string) input('heating'),
        'description' => (string) input('description'),
        'features' => json_encode(array_values(array_intersect((array) ($_POST['features'] ?? []), FEATURES)), JSON_UNESCAPED_UNICODE),
        'images' => json_encode($images, JSON_UNESCAPED_SLASHES),
        'agent_id' => input('agent_id') === '' ? null : (int) input('agent_id'),
        'featured' => input('featured') ? 1 : 0,
        'status' => isset(STATUSES[input('status')]) ? input('status') : 'aktif',
        'updated_at' => date('Y-m-d H:i:s'),
    ];

    $errors = [];
    if (mb_strlen($data['title']) < 5) {
        $errors['title'] = 'Başlık en az 5 karakter olmalı.';
    }
    if ($data['price'] <= 0) {
        $errors['price'] = 'Geçerli bir fiyat girin.';
    }
    if ($data['district'] === '') {
        $errors['district'] = 'İlçe zorunludur.';
    }
    if ($errors) {
        $data['images'] = $images;
        $data['features'] = json_decode($data['features'], true);
        $data['id'] = $id;
        admin_listing_form($id, ['errors' => $errors, 'l' => $data]);
        return;
    }

    if ($id) {
        $data['slug'] = input('slug') !== '' ? unique_slug('listings', (string) input('slug'), (int) $id) : unique_slug('listings', $data['title'], (int) $id);
        db_update('listings', (int) $id, $data);
        flash('success', 'İlan güncellendi.');
    } else {
        $data['slug'] = unique_slug('listings', $data['title']);
        $id = (string) db_insert('listings', $data);
        flash('success', 'İlan oluşturuldu.');
    }
    redirect('yonetim/ilanlar/' . $id);
}

function admin_listing_delete(string $id): void
{
    require_admin();
    csrf_check();
    db_run('DELETE FROM listings WHERE id = ?', [(int) $id]);
    flash('success', 'İlan silindi.');
    redirect('yonetim/ilanlar');
}

/** Çoklu görsel yükleme; yalnızca gerçek resim dosyalarını kabul eder. */
function handle_uploads(string $field): array
{
    $saved = [];
    if (empty($_FILES[$field]['name'])) {
        return $saved;
    }
    $files = $_FILES[$field];
    $names = (array) $files['name'];
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0775, true);
    }
    $allowed = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    foreach ($names as $i => $name) {
        $tmp = ((array) $files['tmp_name'])[$i] ?? '';
        $err = ((array) $files['error'])[$i] ?? UPLOAD_ERR_NO_FILE;
        $size = ((array) $files['size'])[$i] ?? 0;
        if ($err !== UPLOAD_ERR_OK || $size > 8 * 1024 * 1024 || !is_uploaded_file($tmp)) {
            continue;
        }
        $type = @exif_imagetype($tmp) ?: (getimagesize($tmp)[2] ?? null);
        if (!isset($allowed[$type])) {
            continue;
        }
        $file = date('Ymd') . '-' . bin2hex(random_bytes(6)) . '.' . $allowed[$type];
        if (move_uploaded_file($tmp, UPLOAD_DIR . '/' . $file)) {
            $saved[] = '/uploads/' . $file;
        }
    }
    return $saved;
}

// ---- Danışmanlar ---------------------------------------------------------

function admin_agents(): void
{
    require_admin();
    admin_view('agents', ['title' => 'Danışmanlar', 'rows' => db_all(
        'SELECT a.*, (SELECT COUNT(*) FROM listings l WHERE l.agent_id = a.id) AS listing_count FROM agents a ORDER BY sort, id'
    )]);
}

function admin_agent_form(?string $id = null, array $extra = []): void
{
    require_admin();
    $a = $id ? db_one('SELECT * FROM agents WHERE id = ?', [(int) $id]) : [];
    if ($id && !$a) {
        not_found();
    }
    admin_view('agent_form', $extra + ['title' => $id ? 'Danışmanı Düzenle' : 'Yeni Danışman', 'a' => $a]);
}

function admin_agent_save(?string $id = null): void
{
    require_admin();
    csrf_check();
    $data = [];
    foreach (['name', 'title', 'phone', 'email', 'bio', 'languages', 'specialties', 'photo'] as $k) {
        $data[$k] = (string) input($k);
    }
    $data['experience_years'] = (int) input('experience_years');
    $data['sort'] = (int) input('sort');
    if ($up = handle_uploads('photo_file')) {
        $data['photo'] = $up[0];
    }
    if (mb_strlen($data['name']) < 3) {
        admin_agent_form($id, ['errors' => ['name' => 'Ad soyad zorunludur.'], 'a' => $data + ['id' => $id]]);
        return;
    }
    $data['slug'] = unique_slug('agents', $data['name'], $id ? (int) $id : null);
    if ($id) {
        db_update('agents', (int) $id, $data);
    } else {
        $id = (string) db_insert('agents', $data);
    }
    flash('success', 'Danışman kaydedildi.');
    redirect('yonetim/danismanlar/' . $id);
}

function admin_agent_delete(string $id): void
{
    require_admin();
    csrf_check();
    db_run('DELETE FROM agents WHERE id = ?', [(int) $id]);
    flash('success', 'Danışman silindi. İlanları danışmansız olarak kaldı.');
    redirect('yonetim/danismanlar');
}

// ---- Blog ----------------------------------------------------------------

function admin_posts(): void
{
    require_admin();
    admin_view('posts', ['title' => 'Blog Yazıları', 'rows' => db_all('SELECT * FROM posts ORDER BY published_at DESC')]);
}

function admin_post_form(?string $id = null, array $extra = []): void
{
    require_admin();
    $p = $id ? db_one('SELECT * FROM posts WHERE id = ?', [(int) $id]) : ['published' => 1, 'author' => setting('site_name')];
    if ($id && !$p) {
        not_found();
    }
    admin_view('post_form', $extra + ['title' => $id ? 'Yazıyı Düzenle' : 'Yeni Yazı', 'p' => $p]);
}

function admin_post_save(?string $id = null): void
{
    require_admin();
    csrf_check();
    $data = [];
    foreach (['title', 'category', 'content', 'cover', 'author'] as $k) {
        $data[$k] = (string) input($k);
    }
    $data['excerpt'] = (string) input('excerpt') ?: excerpt(preg_replace('/^## .*$/m', '', $data['content']), 160);
    $data['published'] = input('published') ? 1 : 0;
    $data['published_at'] = input('published_at') ? date('Y-m-d H:i:s', strtotime((string) input('published_at'))) : date('Y-m-d H:i:s');
    if ($up = handle_uploads('cover_file')) {
        $data['cover'] = $up[0];
    }
    if (mb_strlen($data['title']) < 5) {
        admin_post_form($id, ['errors' => ['title' => 'Başlık en az 5 karakter olmalı.'], 'p' => $data + ['id' => $id]]);
        return;
    }
    $data['slug'] = unique_slug('posts', $data['title'], $id ? (int) $id : null);
    if ($id) {
        db_update('posts', (int) $id, $data);
    } else {
        $id = (string) db_insert('posts', $data);
    }
    flash('success', 'Yazı kaydedildi.');
    redirect('yonetim/blog/' . $id);
}

function admin_post_delete(string $id): void
{
    require_admin();
    csrf_check();
    db_run('DELETE FROM posts WHERE id = ?', [(int) $id]);
    flash('success', 'Yazı silindi.');
    redirect('yonetim/blog');
}

// ---- Mesajlar ------------------------------------------------------------

function admin_messages(): void
{
    require_admin();
    admin_view('messages', ['title' => 'Gelen Talepler', 'rows' => db_all(
        'SELECT m.*, l.title AS listing_title, l.slug AS listing_slug FROM messages m LEFT JOIN listings l ON l.id = m.listing_id ORDER BY m.created_at DESC'
    )]);
}

function admin_message_read(string $id): void
{
    require_admin();
    csrf_check();
    db_run('UPDATE messages SET is_read = 1 - is_read WHERE id = ?', [(int) $id]);
    redirect('yonetim/mesajlar');
}

function admin_message_delete(string $id): void
{
    require_admin();
    csrf_check();
    db_run('DELETE FROM messages WHERE id = ?', [(int) $id]);
    flash('success', 'Mesaj silindi.');
    redirect('yonetim/mesajlar');
}

// ---- Ayarlar -------------------------------------------------------------

const SETTING_FIELDS = [
    'Genel' => ['site_name' => 'Site adı', 'tagline' => 'Slogan', 'meta_description' => 'Varsayılan meta açıklaması (SEO)'],
    'Ana sayfa' => ['hero_title' => 'Karşılama başlığı', 'hero_subtitle' => 'Karşılama alt metni'],
    'İletişim' => ['phone' => 'Telefon', 'whatsapp' => 'WhatsApp numarası', 'email' => 'E-posta', 'address' => 'Adres',
        'working_hours' => 'Çalışma saatleri', 'office_lat' => 'Ofis enlem', 'office_lng' => 'Ofis boylam'],
    'Sosyal medya' => ['instagram' => 'Instagram', 'linkedin' => 'LinkedIn'],
];

function admin_settings(): void
{
    require_admin();
    admin_view('settings', ['title' => 'Site Ayarları', 'groups' => SETTING_FIELDS]);
}

function admin_settings_save(): void
{
    require_admin();
    csrf_check();
    foreach (SETTING_FIELDS as $fields) {
        foreach ($fields as $k => $_) {
            db_run('INSERT INTO settings (key, value) VALUES (?, ?) ON CONFLICT(key) DO UPDATE SET value = excluded.value', [$k, (string) input($k)]);
        }
    }
    if (input('new_password') !== '') {
        if (mb_strlen((string) input('new_password')) < 8) {
            flash('error', 'Yeni şifre en az 8 karakter olmalı. Diğer ayarlar kaydedildi.');
            redirect('yonetim/ayarlar');
        }
        db_run('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash((string) input('new_password'), PASSWORD_DEFAULT), auth_user()['id']]);
    }
    flash('success', 'Ayarlar kaydedildi.');
    redirect('yonetim/ayarlar');
}
