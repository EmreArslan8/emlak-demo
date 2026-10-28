<article class="post-card">
    <a class="post-card__media" href="<?= url('blog/' . $p['slug']) ?>" tabindex="-1">
        <img src="<?= e(img($p['cover'], 900)) ?>" alt="<?= e($p['title']) ?>" loading="lazy">
    </a>
    <p class="post-card__meta"><?= e($p['category']) ?> · <time datetime="<?= e(substr((string) $p['published_at'], 0, 10)) ?>"><?= tr_date($p['published_at']) ?></time></p>
    <h3 class="post-card__title"><a href="<?= url('blog/' . $p['slug']) ?>"><?= e($p['title']) ?></a></h3>
    <p class="post-card__text"><?= e($p['excerpt']) ?></p>
</article>
