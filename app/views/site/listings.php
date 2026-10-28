<?php
$crumbs = $forcedType ? [LISTING_TYPES[$forcedType] => null] : ['İlanlar' => null];
partial('site/_page_hero', ['title' => $heading, 'crumbs' => $crumbs, 'lead' => "$total ilan listeleniyor"]);
$action = $forcedType ? url($forcedType) : url('ilanlar');
$qs = array_filter($f, fn($v) => $v !== '' && $v !== null);
if ($forcedType) {
    unset($qs['tip']);
}
$activeCount = count(array_diff_key($qs, ['sirala' => 1, 'tip' => 1]));
?>
<section class="section section--tight">
    <div class="container listings">
        <aside class="filters" id="filters">
            <form method="get" action="<?= $action ?>" class="filters__form">
                <div class="filters__head">
                    <h2><?= icon('filter') ?> Filtrele</h2>
                    <?php if ($activeCount): ?><a href="<?= $action ?>" class="filters__reset">Temizle</a><?php endif; ?>
                    <button type="button" class="filters__close" data-close-filters aria-label="Kapat">×</button>
                </div>

                <label class="field">
                    <span>Anahtar kelime</span>
                    <input type="search" name="q" value="<?= e($f['q']) ?>" placeholder="Semt, başlık…">
                </label>

                <?php if (!$forcedType): ?>
                    <div class="field">
                        <span>İlan tipi</span>
                        <div class="seg">
                            <label><input type="radio" name="tip" value="" <?= !$f['tip'] ? 'checked' : '' ?>><span>Tümü</span></label>
                            <?php foreach (LISTING_TYPES as $k => $v): ?>
                                <label><input type="radio" name="tip" value="<?= $k ?>" <?= $f['tip'] === $k ? 'checked' : '' ?>><span><?= $v ?></span></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <label class="field">
                    <span>Emlak tipi</span>
                    <select name="kategori">
                        <option value="">Tümü</option>
                        <?php foreach (CATEGORIES as $k => $v): ?><option value="<?= $k ?>" <?= $f['kategori'] === $k ? 'selected' : '' ?>><?= e($v) ?></option><?php endforeach; ?>
                    </select>
                </label>

                <label class="field">
                    <span>İlçe</span>
                    <select name="ilce">
                        <option value="">Tüm ilçeler</option>
                        <?php foreach ($districts as $d): ?><option <?= $f['ilce'] === $d['district'] ? 'selected' : '' ?>><?= e($d['district']) ?></option><?php endforeach; ?>
                    </select>
                </label>

                <div class="field">
                    <span>Oda sayısı</span>
                    <div class="chips">
                        <?php foreach (['' => 'Tümü', '1+0' => '1+0', '1+1' => '1+1', '2+1' => '2+1', '3+1' => '3+1', '4+' => '4+'] as $k => $v): ?>
                            <label class="chip"><input type="radio" name="oda" value="<?= e($k) ?>" <?= $f['oda'] === $k ? 'checked' : '' ?>><span><?= e($v) ?></span></label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="field">
                    <span>Fiyat (₺)</span>
                    <div class="field-row field-row--tight">
                        <input type="text" name="min_fiyat" value="<?= e($f['min_fiyat']) ?>" placeholder="En az" inputmode="numeric" data-money>
                        <input type="text" name="max_fiyat" value="<?= e($f['max_fiyat']) ?>" placeholder="En çok" inputmode="numeric" data-money>
                    </div>
                </div>

                <div class="field">
                    <span>Brüt m²</span>
                    <div class="field-row field-row--tight">
                        <input type="number" name="min_m2" value="<?= e($f['min_m2']) ?>" placeholder="En az" min="0">
                        <input type="number" name="max_m2" value="<?= e($f['max_m2']) ?>" placeholder="En çok" min="0">
                    </div>
                </div>

                <input type="hidden" name="sirala" value="<?= e($f['sirala']) ?>">
                <button class="btn btn--primary btn--block" type="submit"><?= icon('search') ?> Sonuçları göster</button>
            </form>
        </aside>

        <div class="results">
            <div class="toolbar">
                <button class="btn btn--ghost btn--sm toolbar__filter" type="button" data-open-filters>
                    <?= icon('filter') ?> Filtreler<?= $activeCount ? " ($activeCount)" : '' ?>
                </button>
                <p class="toolbar__count"><strong><?= $total ?></strong> ilan bulundu</p>
                <div class="toolbar__right">
                    <form method="get" action="<?= $action ?>" class="sort">
                        <?php foreach ($qs as $k => $v): if ($k === 'sirala') continue; ?>
                            <input type="hidden" name="<?= e($k) ?>" value="<?= e($v) ?>">
                        <?php endforeach; ?>
                        <select name="sirala" onchange="this.form.submit()" aria-label="Sırala">
                            <?php foreach (['' => 'Önerilen', 'fiyat-artan' => 'Fiyat (artan)', 'fiyat-azalan' => 'Fiyat (azalan)', 'm2' => 'm² (büyükten)'] as $k => $v): ?>
                                <option value="<?= $k ?>" <?= $f['sirala'] === $k ? 'selected' : '' ?>><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <div class="view-toggle" role="group" aria-label="Görünüm">
                        <button type="button" class="is-active" data-view="grid" aria-label="Liste"><?= icon('grid') ?></button>
                        <button type="button" data-view="map" aria-label="Harita"><?= icon('map') ?></button>
                    </div>
                </div>
            </div>

            <div class="results__map" id="results-map" hidden data-markers='<?= e(json_encode(array_values($mapItems), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?>'></div>

            <?php if ($items): ?>
                <div class="grid grid--3 results__grid">
                    <?php foreach ($items as $l) partial('site/_card', ['l' => $l]); ?>
                </div>
                <?php if ($pages > 1): ?>
                    <nav class="pagination" aria-label="Sayfalar">
                        <?php for ($i = 1; $i <= $pages; $i++): ?>
                            <a class="<?= $i === $page ? 'is-active' : '' ?>" href="<?= e(url(ltrim(current_path(), '/'), array_merge($qs, ['sayfa' => $i > 1 ? $i : null]))) ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </nav>
                <?php endif; ?>
            <?php else: ?>
                <div class="empty">
                    <?= icon('search') ?>
                    <h3>Kriterlerinize uygun ilan bulunamadı</h3>
                    <p>Filtreleri genişletmeyi deneyin ya da aradığınız mülkü bize iletin; portföyümüze girdiğinde ilk siz haberdar olun.</p>
                    <div class="btn-row btn-row--center">
                        <a class="btn btn--ghost" href="<?= $action ?>">Filtreleri temizle</a>
                        <a class="btn btn--primary" href="<?= url('iletisim', ['konu' => $f['tip'] === 'kiralik' ? 'Kiralık ilan arıyorum' : 'Satılık ilan arıyorum']) ?>">Talep bırak</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
