<?php
$siteName = setting('site_name');
$meta ??= [];
$pageTitle = !empty($meta['raw_title']) ? $meta['title'] : ($meta['title'] ?? $siteName) . ' | ' . $siteName;
$desc = $meta['description'] ?? setting('meta_description');
$scheme = (($_SERVER['HTTPS'] ?? '') === 'on') ? 'https' : 'http';
$canonical = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . url(ltrim(current_path(), '/'));
$wa = setting('whatsapp');
?><!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($desc) ?>">
    <link rel="canonical" href="<?= e($canonical) ?>">
    <meta property="og:site_name" content="<?= e($siteName) ?>">
    <meta property="og:title" content="<?= e($meta['title'] ?? $siteName) ?>">
    <meta property="og:description" content="<?= e($desc) ?>">
    <meta property="og:type" content="<?= e($meta['type'] ?? 'website') ?>">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <?php if (!empty($meta['image'])): ?><meta property="og:image" content="<?= e($meta['image']) ?>"><?php endif; ?>
    <meta name="theme-color" content="#151412">
    <link rel="icon" href="<?= asset('img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Manrope:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('vendor/leaflet/leaflet.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <script>
        // Stok görsel yüklenemezse yerel yer tutucuya düş
        window.addEventListener('error', function (e) {
            var t = e.target;
            if (t && t.tagName === 'IMG' && !t.dataset.fallback && !t.classList.contains('leaflet-tile')) { t.dataset.fallback = 1; t.src = '<?= BASE ?>/assets/img/placeholder.svg'; }
        }, true);
    </script>
    <script type="application/ld+json"><?= json_encode([
        '@context' => 'https://schema.org', '@type' => 'RealEstateAgent', 'name' => $siteName,
        'telephone' => setting('phone'), 'email' => setting('email'), 'address' => setting('address'),
        'geo' => ['@type' => 'GeoCoordinates', 'latitude' => setting('office_lat'), 'longitude' => setting('office_lng')],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
</head>
<body>
<header class="header<?= empty($solidHeader) ? ' header--overlay' : '' ?>" id="header">
    <div class="container header__inner">
        <a class="logo" href="<?= url() ?>" aria-label="<?= e($siteName) ?> ana sayfa">
            <span class="logo__word"><?= e(mb_strtoupper(explode(' ', $siteName)[0])) ?></span>
            <span class="logo__sub"><?= e(implode(' ', array_slice(explode(' ', $siteName), 1))) ?></span>
        </a>
        <nav class="nav" id="nav" aria-label="Ana menü">
            <a class="<?= is_active('/satilik') ?>" href="<?= url('satilik') ?>">Satılık</a>
            <a class="<?= is_active('/kiralik') ?>" href="<?= url('kiralik') ?>">Kiralık</a>
            <a class="<?= is_active('/danisman') ?>" href="<?= url('danismanlar') ?>">Danışmanlar</a>
            <a class="<?= is_active('/hizmetlerimiz') ?>" href="<?= url('hizmetlerimiz') ?>">Hizmetler</a>
            <a class="<?= is_active('/hakkimizda') ?>" href="<?= url('hakkimizda') ?>">Hakkımızda</a>
            <a class="<?= is_active('/blog') ?>" href="<?= url('blog') ?>">Journal</a>
            <a class="<?= is_active('/iletisim') ?>" href="<?= url('iletisim') ?>">İletişim</a>
            <div class="nav__mobile-extra">
                <a href="<?= tel_link(setting('phone')) ?>"><?= icon('phone') ?> <?= e(setting('phone')) ?></a>
                <a href="<?= wa_link($wa) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?> WhatsApp</a>
            </div>
        </nav>
        <div class="header__cta">
            <a class="header__phone" href="<?= tel_link(setting('phone')) ?>"><?= e(setting('phone')) ?></a>
            <a class="header__wa" href="<?= wa_link($wa) ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><?= icon('whatsapp') ?></a>
            <a class="btn btn--outline btn--sm" href="<?= url('iletisim', ['konu' => 'Mülkümü değerlendirin']) ?>">Değerleme</a>
            <button class="burger" id="burger" aria-label="Menüyü aç" aria-expanded="false" aria-controls="nav"><span></span><span></span></button>
        </div>
    </div>
</header>

<main>
    <?= $content ?>
</main>

<footer class="footer">
    <div class="container">
        <div class="footer__top">
            <div>
                <p class="footer__kicker">Bir sonraki adresiniz için</p>
                <h2 class="footer__title">Özel bir görüşme planlayalım.</h2>
            </div>
            <div class="footer__actions">
                <a class="btn btn--light" href="<?= url('iletisim') ?>">Randevu oluştur</a>
                <a class="btn btn--ghost-light" href="<?= wa_link($wa, 'Merhaba, web siteniz üzerinden ulaşıyorum.') ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?> WhatsApp</a>
            </div>
        </div>
        <div class="footer__grid">
            <div>
                <a class="logo logo--light" href="<?= url() ?>">
                    <span class="logo__word"><?= e(mb_strtoupper(explode(' ', $siteName)[0])) ?></span>
                    <span class="logo__sub"><?= e(implode(' ', array_slice(explode(' ', $siteName), 1))) ?></span>
                </a>
                <p class="footer__about"><?= e(setting('tagline')) ?> 2004'ten bu yana İstanbul'un seçkin semtlerinde satış, kiralama ve yatırım danışmanlığı.</p>
            </div>
            <div>
                <h4>Portföy</h4>
                <a href="<?= url('satilik') ?>">Satılık</a>
                <a href="<?= url('kiralik') ?>">Kiralık</a>
                <a href="<?= url('ilanlar', ['kategori' => 'villa']) ?>">Villalar</a>
                <a href="<?= url('ilanlar', ['kategori' => 'ofis']) ?>">Ticari</a>
            </div>
            <div>
                <h4>Kurumsal</h4>
                <a href="<?= url('hakkimizda') ?>">Hakkımızda</a>
                <a href="<?= url('hizmetlerimiz') ?>">Hizmetler</a>
                <a href="<?= url('danismanlar') ?>">Danışmanlar</a>
                <a href="<?= url('blog') ?>">Journal</a>
            </div>
            <div>
                <h4>Ofis</h4>
                <p><?= e(setting('address')) ?></p>
                <p><a href="<?= tel_link(setting('phone')) ?>"><?= e(setting('phone')) ?></a><br><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></p>
                <div class="socials">
                    <a href="<?= e(setting('instagram')) ?>" aria-label="Instagram" target="_blank" rel="noopener"><?= icon('instagram') ?></a>
                    <a href="<?= e(setting('linkedin')) ?>" aria-label="LinkedIn" target="_blank" rel="noopener"><?= icon('linkedin') ?></a>
                </div>
            </div>
        </div>
        <div class="footer__bottom">
            <span>© <?= date('Y') ?> <?= e($siteName) ?></span>
            <span>Taşınmaz Ticareti Yetki Belgesi No: 3400000 · <a href="<?= url('yonetim') ?>">Yönetim</a></span>
        </div>
    </div>
</footer>

<a class="wa-float" href="<?= wa_link($wa, 'Merhaba, web siteniz üzerinden ulaşıyorum.') ?>" target="_blank" rel="noopener" aria-label="WhatsApp ile yazın"><?= icon('whatsapp') ?><span>WhatsApp'tan yazın</span></a>

<script src="<?= asset('vendor/leaflet/leaflet.js') ?>" defer></script>
<script src="<?= asset('js/app.js') ?>" defer></script>
</body>
</html>
