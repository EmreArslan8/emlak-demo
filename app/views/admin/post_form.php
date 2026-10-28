<?php
$errors ??= [];
$v = fn($k) => e((string) ($p[$k] ?? ''));
$id = $p['id'] ?? null;
?>
<form class="form-grid" method="post" action="<?= $id ? url('yonetim/blog/' . $id) : url('yonetim/blog/yeni') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-grid__main">
        <section class="box">
            <label class="field <?= isset($errors['title']) ? 'has-error' : '' ?>"><span>Başlık *</span><input name="title" value="<?= $v('title') ?>" required><?php if (isset($errors['title'])): ?><em class="err"><?= e($errors['title']) ?></em><?php endif; ?></label>
            <label class="field"><span>Özet (boş bırakılırsa otomatik oluşturulur)</span><textarea name="excerpt" rows="2"><?= $v('excerpt') ?></textarea></label>
            <label class="field"><span>İçerik</span><textarea name="content" rows="18" class="mono"><?= $v('content') ?></textarea>
                <small class="muted">İpucu: Paragraflar arasında boş satır bırakın. <code>## </code> ile başlayan satır alt başlık, <code>- </code> ile başlayan satırlar madde listesi olur.</small></label>
        </section>
    </div>
    <aside class="form-grid__side">
        <section class="box sticky">
            <h2>Yayın</h2>
            <label class="switch"><input type="checkbox" name="published" value="1" <?= !empty($p['published']) ? 'checked' : '' ?>><span></span> Yayında</label>
            <label class="field"><span>Yayın tarihi</span><input type="date" name="published_at" value="<?= e(substr((string) ($p['published_at'] ?? date('Y-m-d')), 0, 10)) ?>"></label>
            <label class="field"><span>Kategori</span><input name="category" value="<?= $v('category') ?>" list="cats"><datalist id="cats"><option value="Rehber"><option value="Yatırım"><option value="İpuçları"><option value="Mevzuat"><option value="Haber"></datalist></label>
            <label class="field"><span>Yazar</span><input name="author" value="<?= $v('author') ?>"></label>
            <img class="preview" src="<?= e(img($p['cover'] ?? null, 600)) ?>" alt="">
            <label class="field"><span>Kapak görseli bağlantısı</span><input name="cover" value="<?= $v('cover') ?>"></label>
            <label class="upload upload--sm"><input type="file" name="cover_file" accept="image/jpeg,image/png,image/webp"><?= icon('camera') ?> <span>Dosya yükle</span></label>
            <button class="btn btn--primary btn--block" type="submit">Kaydet</button>
            <a class="back" href="<?= url('yonetim/blog') ?>">← Yazı listesi</a>
        </section>
    </aside>
</form>
