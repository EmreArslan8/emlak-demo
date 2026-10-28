<?php partial('site/_page_hero', ['title' => 'Danışmanlarımız', 'crumbs' => ['Danışmanlar' => null],
    'lead' => 'Bölgesini avucunun içi gibi bilen, lisanslı ve deneyimli gayrimenkul danışmanlarımızla tanışın.']); ?>
<section class="section">
    <div class="container grid grid--2">
        <?php foreach ($agents as $a): ?>
            <article class="agent-row">
                <a href="<?= url('danisman/' . $a['slug']) ?>" class="agent-row__photo"><img src="<?= e(img($a['photo'], 500)) ?>" alt="<?= e($a['name']) ?>" loading="lazy"></a>
                <div class="agent-row__body">
                    <h2><a href="<?= url('danisman/' . $a['slug']) ?>"><?= e($a['name']) ?></a></h2>
                    <p class="agent-row__title"><?= e($a['title']) ?></p>
                    <p class="muted"><?= e($a['specialties']) ?></p>
                    <ul class="mini-stats">
                        <li><strong><?= (int) $a['experience_years'] ?></strong> yıl tecrübe</li>
                        <li><strong><?= (int) $a['listing_count'] ?></strong> aktif ilan</li>
                    </ul>
                    <div class="btn-row">
                        <a class="btn btn--primary btn--sm" href="<?= url('danisman/' . $a['slug']) ?>">Profili ve ilanları</a>
                        <a class="btn btn--wa btn--sm" href="<?= wa_link($a['phone']) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?></a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php partial('site/_cta'); ?>
