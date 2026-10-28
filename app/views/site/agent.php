<section class="profile">
    <div class="container">
        <nav class="crumbs" aria-label="Sayfa yolu">
            <a href="<?= url() ?>">Ana Sayfa</a><span>/</span><a href="<?= url('danismanlar') ?>">Danışmanlar</a><span>/</span><span aria-current="page"><?= e($a['name']) ?></span>
        </nav>
        <div class="profile__inner">
            <img class="profile__photo" src="<?= e(img($a['photo'], 600)) ?>" alt="<?= e($a['name']) ?>">
            <div class="profile__body">
                <h1><?= e($a['name']) ?></h1>
                <p class="profile__title"><?= e($a['title']) ?></p>
                <ul class="profile__meta">
                    <li><?= icon('star') ?> <?= (int) $a['experience_years'] ?> yıl tecrübe</li>
                    <li><?= icon('home') ?> <?= $total ?> ilan</li>
                    <li><?= icon('users') ?> <?= e($a['languages']) ?></li>
                </ul>
                <div class="btn-row">
                    <a class="btn btn--accent" href="<?= tel_link($a['phone']) ?>"><?= icon('phone') ?> <?= e($a['phone']) ?></a>
                    <a class="btn btn--wa" href="<?= wa_link($a['phone']) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?> WhatsApp</a>
                    <a class="btn btn--light" href="mailto:<?= e($a['email']) ?>"><?= icon('mail') ?> E-posta</a>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="section section--tight">
    <div class="container">
        <div class="panel panel--wide">
            <h2>Hakkında</h2>
            <div class="prose"><?= render_text($a['bio'] ?? '') ?></div>
            <p><strong>Uzmanlık alanları:</strong> <?= e($a['specialties']) ?></p>
        </div>
    </div>
    <div class="container">
        <div class="section__head"><div><span class="eyebrow">Portföy</span><h2>Danışmanın ilanları</h2></div></div>
        <?php if ($items): ?>
            <div class="grid grid--3"><?php foreach ($items as $l) partial('site/_card', ['l' => $l]); ?></div>
        <?php else: ?>
            <p class="muted">Bu danışmanın şu anda yayında ilanı bulunmuyor.</p>
        <?php endif; ?>
    </div>
</section>
