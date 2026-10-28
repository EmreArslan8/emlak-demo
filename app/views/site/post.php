<article>
    <header class="post-hero" style="--bg:url('<?= e(img($p['cover'], 1800)) ?>')">
        <div class="container container--narrow">
            <nav class="crumbs" aria-label="Sayfa yolu"><a href="<?= url() ?>">Ana Sayfa</a><span>/</span><a href="<?= url('blog') ?>">Blog</a></nav>
            <?php if ($p['category']): ?><span class="badge badge--light"><?= e($p['category']) ?></span><?php endif; ?>
            <h1><?= e($p['title']) ?></h1>
            <p class="post-hero__meta"><?= e($p['author']) ?> · <?= tr_date($p['published_at']) ?></p>
        </div>
    </header>
    <div class="section section--tight">
        <div class="container container--narrow">
            <p class="lead"><?= e($p['excerpt']) ?></p>
            <div class="prose prose--lg"><?= render_text($p['content'] ?? '') ?></div>
        </div>
    </div>
</article>
<?php if ($others): ?>
    <section class="section section--tint">
        <div class="container">
            <div class="section__head"><div><h2>Diğer yazılar</h2></div></div>
            <div class="grid grid--3"><?php foreach ($others as $o) partial('site/_post_card', ['p' => $o]); ?></div>
        </div>
    </section>
<?php endif; ?>
