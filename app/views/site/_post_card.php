<article class="post-card">
    <a class="post-card__media" href="<?= url('blog/' . $p['slug']) ?>" tabindex="-1">
        <img src="<?= e(img($p['cover'], 800)) ?>" alt="<?= e($p['title']) ?>" loading="lazy">
        <?php if ($p['category']): ?><span class="badge badge--light"><?= e($p['category']) ?></span><?php endif; ?>
    </a>
    <div class="post-card__body">
        <time datetime="<?= e(substr((string) $p['published_at'], 0, 10)) ?>"><?= icon('calendar') ?> <?= tr_date($p['published_at']) ?></time>
        <h3><a href="<?= url('blog/' . $p['slug']) ?>"><?= e($p['title']) ?></a></h3>
        <p><?= e($p['excerpt']) ?></p>
        <a class="link-arrow" href="<?= url('blog/' . $p['slug']) ?>">Devamını oku <?= icon('arrow') ?></a>
    </div>
</article>
