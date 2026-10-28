<?php /** @var array $l */ $sold = $l['status'] === 'satildi'; ?>
<article class="card <?= $sold ? 'card--sold' : '' ?>">
    <a class="card__media" href="<?= url('ilan/' . $l['slug']) ?>" tabindex="-1">
        <img src="<?= e(img($l['cover'], 900)) ?>" alt="<?= e($l['title']) ?>" loading="lazy" width="900" height="675">
        <span class="card__tag"><?= $sold ? ($l['type'] === 'kiralik' ? 'Kiralandı' : 'Satıldı') : e(LISTING_TYPES[$l['type']] ?? '') ?></span>
        <?php if ($l['featured'] && !$sold): ?><span class="card__tag card__tag--right">Özel portföy</span><?php endif; ?>
    </a>
    <div class="card__body">
        <p class="card__loc"><?= e($l['neighborhood']) ?>, <?= e($l['district']) ?></p>
        <h3 class="card__title"><a href="<?= url('ilan/' . $l['slug']) ?>"><?= e($l['title']) ?></a></h3>
        <ul class="card__facts">
            <?php if ($l['rooms'] && $l['rooms'] !== '-'): ?><li><?= icon('bed') ?> <?= e($l['rooms']) ?></li><?php endif; ?>
            <?php if ($l['bathrooms']): ?><li><?= icon('bath') ?> <?= (int) $l['bathrooms'] ?></li><?php endif; ?>
            <li><?= icon('area') ?> <?= number_format((int) $l['gross_m2'], 0, ',', '.') ?> m²</li>
            <li class="card__cat"><?= e(category_label($l['category'])) ?></li>
        </ul>
        <div class="card__foot">
            <p class="card__price"><?= price($l['price'], $l['type']) ?></p>
            <?php if (!$sold): ?>
                <a class="card__wa" href="<?= wa_link(setting('whatsapp'), 'Merhaba, "' . $l['title'] . '" (İlan No: #' . $l['id'] . ') hakkında bilgi almak istiyorum.') ?>" target="_blank" rel="noopener" aria-label="Bu ilan için WhatsApp'tan yazın"><?= icon('whatsapp') ?></a>
            <?php endif; ?>
        </div>
    </div>
</article>
