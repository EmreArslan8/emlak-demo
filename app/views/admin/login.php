<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Giriş · <?= e(setting('site_name')) ?> Yönetim</title>
    <link rel="icon" href="<?= asset('img/favicon.svg') ?>" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="login" style="--bg:url('<?= e(img('https://images.unsplash.com/photo-1600585154340-be6161a56a0c', 1800)) ?>')">
<form class="login__card" method="post" action="<?= url('yonetim/giris') ?>">
    <?= csrf_field() ?>
    <span class="sidebar__logo sidebar__logo--lg"><svg viewBox="0 0 32 32"><path d="M4 15 16 5l12 10v12a1 1 0 0 1-1 1h-7v-8h-8v8H5a1 1 0 0 1-1-1z" fill="currentColor"/></svg></span>
    <h1>Yönetim Paneli</h1>
    <p class="muted"><?= e(setting('site_name')) ?> içerik yönetimine hoş geldiniz.</p>
    <?php if (!empty($error)): ?><div class="alert alert--error"><?= e($error) ?></div><?php endif; ?>
    <label class="field"><span>Kullanıcı adı</span><input name="username" value="<?= e((string) input('username')) ?>" autocomplete="username" required autofocus></label>
    <label class="field"><span>Şifre</span><input type="password" name="password" autocomplete="current-password" required></label>
    <button class="btn btn--primary btn--block" type="submit">Giriş yap</button>
    <p class="hint">Demo: <code>admin</code> / <code>admin123</code></p>
    <a class="back" href="<?= url() ?>">← Siteye dön</a>
</form>
</body>
</html>
