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
    <meta name="theme-color" content="#0f3d3e">
    <link rel="icon" href="<?= asset('img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
<div class="topbar">
    <div class="container topbar__inner">
        <span><?= icon('clock') ?> <?= e(setting('working_hours')) ?></span>
        <span class="topbar__right">
            <a href="mailto:<?= e(setting('email')) ?>"><?= icon('mail') ?> <?= e(setting('email')) ?></a>
            <a href="<?= tel_link(setting('phone')) ?>"><?= icon('phone') ?> <?= e(setting('phone')) ?></a>
        </span>
    </div>
</div>
<header class="header" id="header">
    <div class="container header__inner">
        <a class="logo" href="<?= url() ?>" aria-label="<?= e($siteName) ?> ana sayfa">
            <span class="logo__mark"><svg viewBox="0 0 32 32" aria-hidden="true"><path d="M4 15 16 5l12 10v12a1 1 0 0 1-1 1h-7v-8h-8v8H5a1 1 0 0 1-1-1z" fill="currentColor"/></svg></span>
            <span class="logo__text"><?= e(explode(' ', $siteName)[0]) ?><small><?= e(implode(' ', array_slice(explode(' ', $siteName), 1))) ?></small></span>
        </a>
        <nav class="nav" id="nav" aria-label="Ana menü">
            <a class="<?= is_active('/') ?>" href="<?= url() ?>">Ana Sayfa</a>
            <a class="<?= is_active('/satilik') ?>" href="<?= url('satilik') ?>">Satılık</a>
            <a class="<?= is_active('/kiralik') ?>" href="<?= url('kiralik') ?>">Kiralık</a>
            <a class="<?= is_active('/danisman') ?>" href="<?= url('danismanlar') ?>">Danışmanlar</a>
            <a class="<?= is_active('/hizmetlerimiz') ?>" href="<?= url('hizmetlerimiz') ?>">Hizmetlerimiz</a>
            <a class="<?= is_active('/hakkimizda') ?>" href="<?= url('hakkimizda') ?>">Hakkımızda</a>
            <a class="<?= is_active('/blog') ?>" href="<?= url('blog') ?>">Blog</a>
            <a class="<?= is_active('/iletisim') ?>" href="<?= url('iletisim') ?>">İletişim</a>
        </nav>
        <div class="header__cta">
            <a class="btn btn--accent btn--sm" href="<?= url('iletisim', ['konu' => 'Mülkümü değerlendirin']) ?>">Mülkünü Değerlendir</a>
            <button class="burger" id="burger" aria-label="Menüyü aç" aria-expanded="false" aria-controls="nav"><span></span><span></span><span></span></button>
        </div>
    </div>
</header>

<main>
    <?= $content ?>
</main>

<footer class="footer">
    <div class="container footer__grid">
        <div>
            <a class="logo logo--light" href="<?= url() ?>">
                <span class="logo__mark"><svg viewBox="0 0 32 32" aria-hidden="true"><path d="M4 15 16 5l12 10v12a1 1 0 0 1-1 1h-7v-8h-8v8H5a1 1 0 0 1-1-1z" fill="currentColor"/></svg></span>
                <span class="logo__text"><?= e(explode(' ', $siteName)[0]) ?><small><?= e(implode(' ', array_slice(explode(' ', $siteName), 1))) ?></small></span>
            </a>
            <p class="footer__about"><?= e(setting('tagline')) ?> Satış, kiralama ve yatırım danışmanlığında güvenilir çözüm ortağınız.</p>
            <div class="socials">
                <a href="<?= e(setting('instagram')) ?>" aria-label="Instagram" target="_blank" rel="noopener"><?= icon('instagram') ?></a>
                <a href="<?= e(setting('linkedin')) ?>" aria-label="LinkedIn" target="_blank" rel="noopener"><?= icon('linkedin') ?></a>
                <a href="<?= wa_link($wa) ?>" aria-label="WhatsApp" target="_blank" rel="noopener"><?= icon('whatsapp') ?></a>
            </div>
        </div>
        <div>
            <h4>Portföy</h4>
            <a href="<?= url('satilik') ?>">Satılık İlanlar</a>
            <a href="<?= url('kiralik') ?>">Kiralık İlanlar</a>
            <a href="<?= url('ilanlar', ['kategori' => 'villa']) ?>">Villalar</a>
            <a href="<?= url('ilanlar', ['kategori' => 'ofis']) ?>">Ticari Gayrimenkul</a>
        </div>
        <div>
            <h4>Kurumsal</h4>
            <a href="<?= url('hakkimizda') ?>">Hakkımızda</a>
            <a href="<?= url('hizmetlerimiz') ?>">Hizmetlerimiz</a>
            <a href="<?= url('danismanlar') ?>">Danışmanlarımız</a>
            <a href="<?= url('blog') ?>">Blog &amp; Haberler</a>
        </div>
        <div>
            <h4>İletişim</h4>
            <p class="footer__contact"><?= icon('pin') ?> <?= e(setting('address')) ?></p>
            <p class="footer__contact"><?= icon('phone') ?> <a href="<?= tel_link(setting('phone')) ?>"><?= e(setting('phone')) ?></a></p>
            <p class="footer__contact"><?= icon('mail') ?> <a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></p>
        </div>
    </div>
    <div class="container footer__bottom">
        <span>© <?= date('Y') ?> <?= e($siteName) ?>. Tüm hakları saklıdır.</span>
        <span>Yetki Belgesi No: 3400000 · <a href="<?= url('yonetim') ?>">Yönetim</a></span>
    </div>
</footer>

<a class="wa-float" href="<?= wa_link($wa, 'Merhaba, web siteniz üzerinden ulaşıyorum.') ?>" target="_blank" rel="noopener" aria-label="WhatsApp ile yazın">
    <?= icon('whatsapp') ?><span>WhatsApp</span>
</a>

<script src="<?= asset('vendor/leaflet/leaflet.js') ?>" defer></script>
<script src="<?= asset('js/app.js') ?>" defer></script>
</body>
</html>
