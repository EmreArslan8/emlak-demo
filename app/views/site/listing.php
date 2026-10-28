<?php
$sold = $l['status'] === 'satildi';
$imgs = $l['images'] ?: [null];
$facts = array_filter([
    ['İlan No', '#' . str_pad((string) $l['id'], 6, '0', STR_PAD_LEFT)],
    ['İlan Tarihi', tr_date($l['created_at'])],
    ['Emlak Tipi', LISTING_TYPES[$l['type']] . ' ' . category_label($l['category'])],
    ['Oda Sayısı', $l['rooms'] !== '-' ? $l['rooms'] : null],
    ['Brüt m²', $l['gross_m2'] ? number_format((int) $l['gross_m2'], 0, ',', '.') . ' m²' : null],
    ['Net m²', $l['net_m2'] ? number_format((int) $l['net_m2'], 0, ',', '.') . ' m²' : null],
    ['Banyo', $l['bathrooms'] ?: null],
    ['Bulunduğu Kat', $l['floor'] !== '-' ? $l['floor'] : null],
    ['Kat Sayısı', $l['total_floors'] ?: null],
    ['Bina Yaşı', $l['category'] !== 'arsa' ? ($l['building_age'] ? $l['building_age'] . ' yıl' : 'Sıfır') : null],
    ['Isıtma', $l['heating'] !== 'Yok' ? $l['heating'] : null],
    ['m² Fiyatı', $l['gross_m2'] ? price_plain($l['price'] / $l['gross_m2']) : null],
], fn($r) => $r[1] !== null && $r[1] !== '');
$waText = "Merhaba, \"{$l['title']}\" (İlan No: #{$l['id']}) hakkında bilgi almak istiyorum.";
$schema = [
    '@context' => 'https://schema.org', '@type' => 'RealEstateListing', 'name' => $l['title'],
    'description' => excerpt($l['description'], 300), 'image' => array_map(fn($i) => img($i, 1200), $l['images']),
    'datePosted' => substr($l['created_at'], 0, 10),
    'offers' => ['@type' => 'Offer', 'price' => $l['price'], 'priceCurrency' => 'TRY'],
    'address' => ['@type' => 'PostalAddress', 'addressLocality' => $l['district'], 'addressRegion' => $l['city'], 'addressCountry' => 'TR'],
];
?>
<script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>

<div class="container detail-top">
    <nav class="crumbs crumbs--dark" aria-label="Sayfa yolu">
        <a href="<?= url() ?>">Ana Sayfa</a><span>/</span>
        <a href="<?= url($l['type']) ?>"><?= LISTING_TYPES[$l['type']] ?></a><span>/</span>
        <a href="<?= url('ilanlar', ['ilce' => $l['district']]) ?>"><?= e($l['district']) ?></a><span>/</span>
        <span aria-current="page"><?= e($l['neighborhood']) ?></span>
    </nav>
    <div class="detail-head">
        <div>
            <div class="detail-head__badges">
                <span class="badge badge--<?= e($l['type']) ?>"><?= LISTING_TYPES[$l['type']] ?></span>
                <span class="badge badge--outline"><?= e(category_label($l['category'])) ?></span>
                <?php if ($sold): ?><span class="badge badge--sold"><?= $l['type'] === 'kiralik' ? 'Kiralandı' : 'Satıldı' ?></span><?php endif; ?>
            </div>
            <h1><?= e($l['title']) ?></h1>
            <p class="detail-head__loc"><?= icon('pin') ?> <?= e($l['neighborhood']) ?> Mah., <?= e($l['district']) ?> / <?= e($l['city']) ?></p>
        </div>
        <div class="detail-head__price">
            <strong><?= price($l['price'], $l['type']) ?></strong>
            <span><?= icon('eye') ?> <?= number_format((int) $l['views'], 0, ',', '.') ?> görüntülenme</span>
        </div>
    </div>
</div>

<div class="container">
    <div class="gallery" data-gallery>
        <?php foreach (array_slice($imgs, 0, 5) as $i => $src): ?>
            <button type="button" class="gallery__item <?= $i === 0 ? 'gallery__item--main' : '' ?>" data-index="<?= $i ?>" aria-label="Fotoğraf <?= $i + 1 ?>">
                <img src="<?= e(img($src, $i === 0 ? 1600 : 700)) ?>" alt="<?= e($l['title']) ?> — fotoğraf <?= $i + 1 ?>" <?= $i ? 'loading="lazy"' : 'fetchpriority="high"' ?>>
                <?php if ($i === min(4, count($imgs) - 1) && count($imgs) > 1): ?>
                    <span class="gallery__more"><?= icon('camera') ?> Tüm fotoğraflar (<?= count($imgs) ?>)</span>
                <?php endif; ?>
            </button>
        <?php endforeach; ?>
        <template data-sources><?= e(json_encode(array_map(fn($s) => img($s, 2000), $imgs), JSON_UNESCAPED_SLASHES)) ?></template>
    </div>
</div>

<section class="section section--tight">
    <div class="container detail">
        <div class="detail__main">
            <ul class="key-facts">
                <?php if ($l['rooms'] !== '-'): ?><li><?= icon('bed') ?><strong><?= e($l['rooms']) ?></strong><span>Oda</span></li><?php endif; ?>
                <?php if ($l['bathrooms']): ?><li><?= icon('bath') ?><strong><?= (int) $l['bathrooms'] ?></strong><span>Banyo</span></li><?php endif; ?>
                <li><?= icon('area') ?><strong><?= number_format((int) $l['gross_m2'], 0, ',', '.') ?> m²</strong><span>Brüt alan</span></li>
                <?php if ($l['category'] !== 'arsa'): ?><li><?= icon('building') ?><strong><?= e($l['floor']) ?></strong><span>Kat</span></li><?php endif; ?>
                <?php if ($l['category'] !== 'arsa'): ?><li><?= icon('calendar') ?><strong><?= $l['building_age'] ? (int) $l['building_age'] : 'Sıfır' ?></strong><span>Bina yaşı</span></li><?php endif; ?>
            </ul>

            <div class="panel">
                <h2>Açıklama</h2>
                <div class="prose"><?= render_text($l['description'] ?? '') ?></div>
            </div>

            <div class="panel">
                <h2>İlan Bilgileri</h2>
                <dl class="facts">
                    <?php foreach ($facts as [$k, $v]): ?><div><dt><?= e($k) ?></dt><dd><?= e((string) $v) ?></dd></div><?php endforeach; ?>
                </dl>
            </div>

            <?php if ($l['features']): ?>
                <div class="panel">
                    <h2>Özellikler</h2>
                    <ul class="features">
                        <?php foreach (FEATURES as $feat): $has = in_array($feat, $l['features'], true); if (!$has) continue; ?>
                            <li><?= icon('check') ?> <?= e($feat) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($l['lat']): ?>
                <div class="panel">
                    <h2>Konum</h2>
                    <p class="muted">Gizlilik nedeniyle konum yaklaşık olarak gösterilmektedir.</p>
                    <div class="map" data-map data-lat="<?= e((string) $l['lat']) ?>" data-lng="<?= e((string) $l['lng']) ?>" data-zoom="15" data-circle="1"></div>
                    <a class="link-arrow" href="https://www.google.com/maps/search/?api=1&query=<?= e($l['lat'] . ',' . $l['lng']) ?>" target="_blank" rel="noopener">Google Haritalar'da aç <?= icon('external') ?></a>
                </div>
            <?php endif; ?>
        </div>

        <aside class="detail__side">
            <div class="sticky">
                <?php if ($agent): ?>
                    <div class="panel agent-box">
                        <a class="agent-box__head" href="<?= url('danisman/' . $agent['slug']) ?>">
                            <img src="<?= e(img($agent['photo'], 200)) ?>" alt="<?= e($agent['name']) ?>">
                            <div><strong><?= e($agent['name']) ?></strong><small><?= e($agent['title']) ?></small></div>
                        </a>
                        <div class="agent-box__actions">
                            <a class="btn btn--primary btn--block" href="<?= tel_link($agent['phone']) ?>"><?= icon('phone') ?> <?= e($agent['phone']) ?></a>
                            <a class="btn btn--wa btn--block" href="<?= wa_link($agent['phone'], $waText) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?> WhatsApp ile yaz</a>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="panel" id="iletisim-formu">
                    <h3>Bilgi &amp; randevu talebi</h3>
                    <?php partial('site/_message_form', [
                        'action' => url('ilan/' . $l['slug']) . '#iletisim-formu',
                        'errors' => $errors ?? [], 'old' => $old ?? [],
                        'defaultMessage' => "Merhaba, #{$l['id']} numaralı ilan ile ilgileniyorum. Görüntüleme randevusu almak istiyorum.",
                        'submitLabel' => 'Talep gönder',
                    ]); ?>
                </div>
                <button class="btn btn--ghost btn--block" type="button" data-share="<?= e($l['title']) ?>"><?= icon('share') ?> İlanı paylaş</button>
            </div>
        </aside>
    </div>
</section>

<?php if ($similar): ?>
    <section class="section section--tint">
        <div class="container">
            <div class="section__head"><div><span class="eyebrow">Bunlar da ilginizi çekebilir</span><h2>Benzer ilanlar</h2></div></div>
            <div class="grid grid--3"><?php foreach ($similar as $s) partial('site/_card', ['l' => $s]); ?></div>
        </div>
    </section>
<?php endif; ?>

<div class="lightbox" id="lightbox" hidden>
    <button type="button" class="lightbox__close" data-lb-close aria-label="Kapat">×</button>
    <button type="button" class="lightbox__nav lightbox__nav--prev" data-lb-prev aria-label="Önceki">‹</button>
    <img alt="">
    <button type="button" class="lightbox__nav lightbox__nav--next" data-lb-next aria-label="Sonraki">›</button>
    <span class="lightbox__count"></span>
</div>

<div class="mobile-bar">
    <?php $ph = $agent['phone'] ?? setting('phone'); ?>
    <a class="btn btn--primary" href="<?= tel_link($ph) ?>"><?= icon('phone') ?> Ara</a>
    <a class="btn btn--wa" href="<?= wa_link($ph, $waText) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?> WhatsApp</a>
</div>
