<?php
$errors ??= [];
$v = fn($k, $d = '') => e((string) ($l[$k] ?? $d));
$id = $l['id'] ?? null;
$action = $id ? url('yonetim/ilanlar/' . $id) : url('yonetim/ilanlar/yeni');
$err = fn($k) => isset($errors[$k]) ? '<em class="err">' . e($errors[$k]) . '</em>' : '';
?>
<?php if ($errors): ?><div class="alert alert--error">Lütfen işaretli alanları kontrol edin.</div><?php endif; ?>
<form class="form-grid" method="post" action="<?= $action ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-grid__main">
        <section class="box">
            <h2>Temel bilgiler</h2>
            <label class="field <?= isset($errors['title']) ? 'has-error' : '' ?>"><span>İlan başlığı *</span><input name="title" value="<?= $v('title') ?>" required maxlength="120" placeholder="Örn. Moda'da deniz manzaralı 3+1 daire"><?= $err('title') ?></label>
            <div class="row3">
                <label class="field"><span>İlan tipi</span><select name="type"><?php foreach (LISTING_TYPES as $k => $t): ?><option value="<?= $k ?>" <?= ($l['type'] ?? '') === $k ? 'selected' : '' ?>><?= $t ?></option><?php endforeach; ?></select></label>
                <label class="field"><span>Emlak tipi</span><select name="category"><?php foreach (CATEGORIES as $k => $t): ?><option value="<?= $k ?>" <?= ($l['category'] ?? '') === $k ? 'selected' : '' ?>><?= $t ?></option><?php endforeach; ?></select></label>
                <label class="field <?= isset($errors['price']) ? 'has-error' : '' ?>"><span>Fiyat (₺) *</span><input name="price" value="<?= isset($l['price']) ? number_format((float) $l['price'], 0, ',', '.') : '' ?>" inputmode="numeric" data-money required><?= $err('price') ?></label>
            </div>
            <label class="field"><span>Açıklama</span><textarea name="description" rows="9" placeholder="Paragraflar arasında boş satır bırakın."><?= $v('description') ?></textarea></label>
        </section>

        <section class="box">
            <h2>Detaylar</h2>
            <div class="row4">
                <label class="field"><span>Oda sayısı</span><input name="rooms" value="<?= $v('rooms') ?>" list="rooms"><datalist id="rooms"><?php foreach (ROOM_OPTIONS as $r): ?><option value="<?= $r ?>"><?php endforeach; ?><option value="-"></datalist></label>
                <label class="field"><span>Brüt m²</span><input type="number" name="gross_m2" value="<?= $v('gross_m2') ?>" min="0"></label>
                <label class="field"><span>Net m²</span><input type="number" name="net_m2" value="<?= $v('net_m2') ?>" min="0"></label>
                <label class="field"><span>Banyo</span><input type="number" name="bathrooms" value="<?= $v('bathrooms') ?>" min="0"></label>
                <label class="field"><span>Bulunduğu kat</span><input name="floor" value="<?= $v('floor') ?>" placeholder="Örn. 3, Bahçe, Çatı"></label>
                <label class="field"><span>Kat sayısı</span><input type="number" name="total_floors" value="<?= $v('total_floors') ?>" min="0"></label>
                <label class="field"><span>Bina yaşı</span><input type="number" name="building_age" value="<?= $v('building_age') ?>" min="0"></label>
                <label class="field"><span>Isıtma</span><select name="heating"><?php foreach (HEATING as $h): ?><option <?= ($l['heating'] ?? '') === $h ? 'selected' : '' ?>><?= $h ?></option><?php endforeach; ?></select></label>
            </div>
            <div class="field"><span>Özellikler</span>
                <div class="checks">
                    <?php foreach (FEATURES as $feat): ?>
                        <label><input type="checkbox" name="features[]" value="<?= e($feat) ?>" <?= in_array($feat, $l['features'] ?? [], true) ? 'checked' : '' ?>> <?= e($feat) ?></label>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="box">
            <h2>Konum</h2>
            <div class="row3">
                <label class="field"><span>İl</span><input name="city" value="<?= $v('city', 'İstanbul') ?>"></label>
                <label class="field <?= isset($errors['district']) ? 'has-error' : '' ?>"><span>İlçe *</span><input name="district" value="<?= $v('district') ?>" required><?= $err('district') ?></label>
                <label class="field"><span>Mahalle</span><input name="neighborhood" value="<?= $v('neighborhood') ?>"></label>
            </div>
            <p class="muted">Haritaya tıklayarak veya işaretçiyi sürükleyerek konumu belirleyin.</p>
            <div class="map-picker" data-map-picker data-lat="<?= $v('lat', '41.015') ?>" data-lng="<?= $v('lng', '28.98') ?>"></div>
            <div class="row2">
                <label class="field"><span>Enlem</span><input name="lat" value="<?= $v('lat') ?>" data-lat></label>
                <label class="field"><span>Boylam</span><input name="lng" value="<?= $v('lng') ?>" data-lng></label>
            </div>
        </section>

        <section class="box">
            <h2>Fotoğraflar</h2>
            <div class="thumbs" data-thumbs></div>
            <label class="field"><span>Görsel bağlantıları (her satıra bir adres — ilk görsel kapak olur)</span>
                <textarea name="images" rows="4" data-images><?= e(implode("\n", $l['images'] ?? [])) ?></textarea></label>
            <label class="upload"><input type="file" name="uploads[]" accept="image/jpeg,image/png,image/webp" multiple data-upload><?= icon('camera') ?> <span>Bilgisayardan fotoğraf yükle (JPG, PNG, WebP · en fazla 8 MB)</span></label>
        </section>
    </div>

    <aside class="form-grid__side">
        <section class="box sticky">
            <h2>Yayın</h2>
            <label class="field"><span>Durum</span><select name="status"><?php foreach (STATUSES as $k => $t): ?><option value="<?= $k ?>" <?= ($l['status'] ?? '') === $k ? 'selected' : '' ?>><?= $t ?></option><?php endforeach; ?></select></label>
            <label class="field"><span>Danışman</span><select name="agent_id"><option value="">— Seçiniz —</option><?php foreach ($agents as $a): ?><option value="<?= $a['id'] ?>" <?= (int) ($l['agent_id'] ?? 0) === (int) $a['id'] ? 'selected' : '' ?>><?= e($a['name']) ?></option><?php endforeach; ?></select></label>
            <label class="switch"><input type="checkbox" name="featured" value="1" <?= !empty($l['featured']) ? 'checked' : '' ?>><span></span> Öne çıkan ilan</label>
            <?php if ($id): ?>
                <label class="field"><span>SEO adresi (slug)</span><input name="slug" value="<?= $v('slug') ?>"><small class="muted"><?= e(url('ilan/')) ?><?= $v('slug') ?></small></label>
            <?php endif; ?>
            <button class="btn btn--primary btn--block" type="submit"><?= $id ? 'Değişiklikleri kaydet' : 'İlanı yayınla' ?></button>
            <?php if ($id && !empty($l['slug'])): ?><a class="btn btn--ghost btn--block" href="<?= url('ilan/' . $l['slug']) ?>" target="_blank"><?= icon('external') ?> Sitede görüntüle</a><?php endif; ?>
            <a class="back" href="<?= url('yonetim/ilanlar') ?>">← İlan listesine dön</a>
        </section>
    </aside>
</form>
