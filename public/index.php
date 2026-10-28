<?php
declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';
require APP . '/controllers/site.php';
require APP . '/controllers/admin.php';

$routes = [
    // Ziyaretçi sayfaları (SEO uyumlu URL'ler)
    ['GET', '/', 'home'],
    ['GET', '/ilanlar', 'listings'],
    ['GET', '/satilik', fn() => listings('satilik')],
    ['GET', '/kiralik', fn() => listings('kiralik')],
    ['GET', '/ilan/([a-z0-9-]+)', 'listing_show'],
    ['POST', '/ilan/([a-z0-9-]+)', 'listing_inquiry'],
    ['GET', '/danismanlar', 'agents'],
    ['GET', '/danisman/([a-z0-9-]+)', 'agent_show'],
    ['GET', '/hakkimizda', 'about'],
    ['GET', '/hizmetlerimiz', 'services'],
    ['GET', '/blog', 'blog'],
    ['GET', '/blog/([a-z0-9-]+)', 'post_show'],
    ['GET', '/iletisim', 'contact'],
    ['POST', '/iletisim', 'contact_submit'],
    ['GET', '/sitemap.xml', 'sitemap'],
    ['GET', '/robots.txt', 'robots'],

    // Yönetim paneli
    ['GET', '/yonetim/giris', 'admin_login'],
    ['POST', '/yonetim/giris', 'admin_login_submit'],
    ['POST', '/yonetim/cikis', 'admin_logout'],
    ['GET', '/yonetim', 'admin_dashboard'],
    ['GET', '/yonetim/ilanlar', 'admin_listings'],
    ['GET', '/yonetim/ilanlar/yeni', 'admin_listing_form'],
    ['POST', '/yonetim/ilanlar/yeni', 'admin_listing_save'],
    ['GET', '/yonetim/ilanlar/(\d+)', 'admin_listing_form'],
    ['POST', '/yonetim/ilanlar/(\d+)', 'admin_listing_save'],
    ['POST', '/yonetim/ilanlar/(\d+)/sil', 'admin_listing_delete'],
    ['GET', '/yonetim/danismanlar', 'admin_agents'],
    ['GET', '/yonetim/danismanlar/yeni', 'admin_agent_form'],
    ['POST', '/yonetim/danismanlar/yeni', 'admin_agent_save'],
    ['GET', '/yonetim/danismanlar/(\d+)', 'admin_agent_form'],
    ['POST', '/yonetim/danismanlar/(\d+)', 'admin_agent_save'],
    ['POST', '/yonetim/danismanlar/(\d+)/sil', 'admin_agent_delete'],
    ['GET', '/yonetim/blog', 'admin_posts'],
    ['GET', '/yonetim/blog/yeni', 'admin_post_form'],
    ['POST', '/yonetim/blog/yeni', 'admin_post_save'],
    ['GET', '/yonetim/blog/(\d+)', 'admin_post_form'],
    ['POST', '/yonetim/blog/(\d+)', 'admin_post_save'],
    ['POST', '/yonetim/blog/(\d+)/sil', 'admin_post_delete'],
    ['GET', '/yonetim/mesajlar', 'admin_messages'],
    ['POST', '/yonetim/mesajlar/(\d+)/okundu', 'admin_message_read'],
    ['POST', '/yonetim/mesajlar/(\d+)/sil', 'admin_message_delete'],
    ['GET', '/yonetim/ayarlar', 'admin_settings'],
    ['POST', '/yonetim/ayarlar', 'admin_settings_save'],
];

$path = current_path();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

foreach ($routes as [$m, $pattern, $handler]) {
    if ($m === $method && preg_match('#^' . $pattern . '$#', $path, $match)) {
        array_shift($match);
        $handler(...$match);
        exit;
    }
}

not_found();
