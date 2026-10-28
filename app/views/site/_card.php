<?php /** @var array $l */ $sold = $l['status'] === 'satildi'; ?>
<article class="card <?= $sold ? 'card--sold' : '' ?>">
    <a class="card__media" href="<?= url('ilan/' . $l['slug']) ?>" tabindex="-1">
        <img src="<?= e(img($l['cover'], 800)) ?>" alt="<?= e($l['title']) ?>" loading="lazy" width="800" height="560">
        <span class="card__badges">
            <span class="badge badge--<?= e($l['type']) ?>"><?= e(LISTING_TYPES[$l['type']] ?? '') ?></span>
            <?php if ($l['featured'] && !$sold): ?><span class="badge badge--featured"><?= icon('star') ?> Öne Çıkan</span><?php endif; ?>
            <?php if ($sold): ?><span class="badge badge--sold"><?= $l['type'] === 'kiralik' ? 'Kiralandı' : 'Satıldı' ?></span><?php endif; ?>
        </span>
        <?php if (count($l['images']) > 1): ?><span class="card__count"><?= icon('camera') ?> <?= count($l['images']) ?></span><?php endif; ?>
    </a>
    <div class="card__body">
        <div class="card__price"><?= price($l['price'], $l['type']) ?></div>
        <h3 class="card__title"><a href="<?= url('ilan/' . $l['slug']) ?>"><?= e($l['title']) ?></a></h3>
        <p class="card__loc"><?= icon('pin') ?> <?= e($l['neighborhood']) ?>, <?= e($l['district']) ?></p>
        <ul class="card__facts">
            <?php if ($l['rooms'] && $l['rooms'] !== '-'): ?><li><?= icon('bed') ?> <?= e($l['rooms']) ?></li><?php endif; ?>
            <?php if ($l['bathrooms']): ?><li><?= icon('bath') ?> <?= (int) $l['bathrooms'] ?> Banyo</li><?php endif; ?>
            <li><?= icon('area') ?> <?= number_format((int) $l['gross_m2'], 0, ',', '.') ?> m²</li>
            <li class="card__cat"><?= e(category_label($l['category'])) ?></li>
        </ul>
    </div>
</article>
