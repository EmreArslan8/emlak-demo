<?php partial('site/_page_hero', ['title' => 'Blog & Haberler', 'crumbs' => ['Blog' => null],
    'lead' => 'Gayrimenkul piyasası, yatırım ve mevzuat üzerine uzmanlarımızdan güncel yazılar.']); ?>
<section class="section section--tight">
    <div class="container">
        <div class="chips chips--nav">
            <a class="chip <?= !$cat ? 'is-active' : '' ?>" href="<?= url('blog') ?>"><span>Tümü</span></a>
            <?php foreach ($cats as $c): ?>
                <a class="chip <?= $cat === $c['category'] ? 'is-active' : '' ?>" href="<?= url('blog', ['kategori' => $c['category']]) ?>"><span><?= e($c['category']) ?></span></a>
            <?php endforeach; ?>
        </div>
        <div class="grid grid--3"><?php foreach ($posts as $p) partial('site/_post_card', ['p' => $p]); ?></div>
        <?php if (!$posts): ?><p class="muted">Bu kategoride henüz yazı yok.</p><?php endif; ?>
    </div>
</section>
