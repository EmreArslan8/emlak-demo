<?php $user = auth_user(); $ok = flash('success'); $err = flash('error'); ?><!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title><?= e($title ?? 'Yönetim') ?> · <?= e(setting('site_name')) ?> Yönetim</title>
    <link rel="icon" href="<?= asset('img/favicon.svg') ?>" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('vendor/leaflet/leaflet.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <script>window.addEventListener('error', function (e) { var t = e.target; if (t && t.tagName === 'IMG' && !t.dataset.fallback && !t.classList.contains('leaflet-tile')) { t.dataset.fallback = 1; t.src = '<?= BASE ?>/assets/img/placeholder.svg'; } }, true);</script>
</head>
<body class="admin">
<aside class="sidebar" id="sidebar">
    <a class="sidebar__brand" href="<?= url('yonetim') ?>">
        <span class="sidebar__logo"><svg viewBox="0 0 32 32"><path d="M4 15 16 5l12 10v12a1 1 0 0 1-1 1h-7v-8h-8v8H5a1 1 0 0 1-1-1z" fill="currentColor"/></svg></span>
        <span><?= e(setting('site_name')) ?><small>Yönetim Paneli</small></span>
    </a>
    <nav class="sidebar__nav">
        <?php
        $cp = current_path();
        $links = [
            ['yonetim', 'Genel Bakış', 'chart', $cp === '/yonetim'],
            ['yonetim/ilanlar', 'İlanlar', 'home', str_starts_with($cp, '/yonetim/ilanlar')],
            ['yonetim/danismanlar', 'Danışmanlar', 'users', str_starts_with($cp, '/yonetim/danismanlar')],
            ['yonetim/blog', 'Blog / Haberler', 'file', str_starts_with($cp, '/yonetim/blog')],
            ['yonetim/mesajlar', 'Gelen Talepler', 'inbox', str_starts_with($cp, '/yonetim/mesajlar')],
            ['yonetim/ayarlar', 'Site Ayarları', 'settings', str_starts_with($cp, '/yonetim/ayarlar')],
        ];
        foreach ($links as [$href, $label, $ic, $on]): ?>
            <a class="<?= $on ? 'is-active' : '' ?>" href="<?= url($href) ?>"><?= icon($ic) ?> <?= $label ?>
                <?php if ($href === 'yonetim/mesajlar' && $unread): ?><span class="pill"><?= $unread ?></span><?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar__foot">
        <a href="<?= url() ?>" target="_blank"><?= icon('external') ?> Siteyi görüntüle</a>
        <form method="post" action="<?= url('yonetim/cikis') ?>"><?= csrf_field() ?><button type="submit"><?= icon('logout') ?> Çıkış yap</button></form>
    </div>
</aside>
<div class="main">
    <header class="topbar">
        <button class="burger" type="button" onclick="document.body.classList.toggle('nav-open')" aria-label="Menü"><span></span><span></span><span></span></button>
        <h1><?= e($title ?? '') ?></h1>
        <div class="topbar__user"><span class="avatar"><?= e(mb_substr($user['name'] ?? 'A', 0, 1)) ?></span><span><?= e($user['name'] ?? '') ?></span></div>
    </header>
    <div class="content">
        <?php if ($ok): ?><div class="alert alert--success"><?= icon('check') ?> <?= e($ok) ?></div><?php endif; ?>
        <?php if ($err): ?><div class="alert alert--error"><?= e($err) ?></div><?php endif; ?>
        <?= $content ?>
    </div>
</div>
<div class="scrim" onclick="document.body.classList.remove('nav-open')"></div>
<script src="<?= asset('vendor/leaflet/leaflet.js') ?>" defer></script>
<script src="<?= asset('js/admin.js') ?>" defer></script>
</body>
</html>
