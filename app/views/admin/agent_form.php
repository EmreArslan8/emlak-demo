<?php
$errors ??= [];
$v = fn($k) => e((string) ($a[$k] ?? ''));
$id = $a['id'] ?? null;
?>
<form class="form-grid" method="post" action="<?= $id ? url('yonetim/danismanlar/' . $id) : url('yonetim/danismanlar/yeni') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-grid__main">
        <section class="box">
            <h2>Profil</h2>
            <div class="row2">
                <label class="field <?= isset($errors['name']) ? 'has-error' : '' ?>"><span>Ad soyad *</span><input name="name" value="<?= $v('name') ?>" required><?php if (isset($errors['name'])): ?><em class="err"><?= e($errors['name']) ?></em><?php endif; ?></label>
                <label class="field"><span>Unvan</span><input name="title" value="<?= $v('title') ?>" placeholder="Örn. Kıdemli Danışman"></label>
                <label class="field"><span>Telefon / WhatsApp</span><input name="phone" value="<?= $v('phone') ?>" placeholder="+90 5xx xxx xx xx"></label>
                <label class="field"><span>E-posta</span><input type="email" name="email" value="<?= $v('email') ?>"></label>
                <label class="field"><span>Tecrübe (yıl)</span><input type="number" name="experience_years" value="<?= $v('experience_years') ?>" min="0"></label>
                <label class="field"><span>Diller</span><input name="languages" value="<?= $v('languages') ?>" placeholder="Türkçe, İngilizce"></label>
            </div>
            <label class="field"><span>Uzmanlık alanları</span><input name="specialties" value="<?= $v('specialties') ?>"></label>
            <label class="field"><span>Biyografi</span><textarea name="bio" rows="6"><?= $v('bio') ?></textarea></label>
        </section>
    </div>
    <aside class="form-grid__side">
        <section class="box sticky">
            <h2>Fotoğraf</h2>
            <img class="preview round" src="<?= e(img($a['photo'] ?? null, 300)) ?>" alt="">
            <label class="field"><span>Görsel bağlantısı</span><input name="photo" value="<?= $v('photo') ?>"></label>
            <label class="upload upload--sm"><input type="file" name="photo_file" accept="image/jpeg,image/png,image/webp"><?= icon('camera') ?> <span>Dosya yükle</span></label>
            <label class="field"><span>Sıralama</span><input type="number" name="sort" value="<?= $v('sort') ?>"></label>
            <button class="btn btn--primary btn--block" type="submit">Kaydet</button>
            <a class="back" href="<?= url('yonetim/danismanlar') ?>">← Danışman listesi</a>
        </section>
    </aside>
</form>
