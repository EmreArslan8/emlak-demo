<section class="hero">
    <div class="hero__bg">
        <img src="<?= e(img('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9', 2000)) ?>" alt="" fetchpriority="high">
    </div>
    <div class="container hero__inner">
        <span class="eyebrow eyebrow--light"><?= icon('shield') ?> Yetkili &amp; Lisanslı Emlak Ofisi</span>
        <h1><?= e(setting('hero_title')) ?></h1>
        <p class="hero__lead"><?= e(setting('hero_subtitle')) ?></p>

        <form class="search" action="<?= url('ilanlar') ?>" method="get" data-search>
            <div class="search__tabs" role="tablist">
                <label><input type="radio" name="tip" value="satilik" checked><span>Satılık</span></label>
                <label><input type="radio" name="tip" value="kiralik"><span>Kiralık</span></label>
            </div>
            <div class="search__fields">
                <label class="search__field">
                    <span>Konum</span>
                    <select name="ilce">
                        <option value="">Tüm İlçeler</option>
                        <?php foreach ($districts as $d): ?><option><?= e($d['district']) ?></option><?php endforeach; ?>
                    </select>
                </label>
                <label class="search__field">
                    <span>Emlak Tipi</span>
                    <select name="kategori">
                        <option value="">Tümü</option>
                        <?php foreach (CATEGORIES as $k => $v): ?><option value="<?= $k ?>"><?= e($v) ?></option><?php endforeach; ?>
                    </select>
                </label>
                <label class="search__field">
                    <span>Oda Sayısı</span>
                    <select name="oda">
                        <option value="">Farketmez</option>
                        <?php foreach (['1+1', '2+1', '3+1', '4+'] as $r): ?><option><?= $r ?></option><?php endforeach; ?>
                    </select>
                </label>
                <label class="search__field">
                    <span>Maks. Fiyat</span>
                    <input type="text" name="max_fiyat" inputmode="numeric" placeholder="Örn. 15.000.000" data-money>
                </label>
                <button class="btn btn--accent search__btn" type="submit"><?= icon('search') ?> <span>İlan Ara</span></button>
            </div>
        </form>

        <ul class="hero__stats">
            <li><strong>20+</strong><span>Yıllık tecrübe</span></li>
            <li><strong>1.200+</strong><span>Tamamlanan işlem</span></li>
            <li><strong><?= $stats['aktif'] ?></strong><span>Aktif portföy</span></li>
            <li><strong>4.9<small>/5</small></strong><span>Müşteri memnuniyeti</span></li>
        </ul>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section__head">
            <div>
                <span class="eyebrow">Portföyümüz</span>
                <h2>Öne çıkan ilanlar</h2>
                <p>Uzman ekibimizin titizlikle seçtiği, yerinde incelenmiş güncel mülkler.</p>
            </div>
            <a class="btn btn--ghost" href="<?= url('ilanlar') ?>">Tüm ilanlar <?= icon('arrow') ?></a>
        </div>
        <div class="grid grid--3">
            <?php foreach ($featured as $l) partial('site/_card', ['l' => $l]); ?>
        </div>
    </div>
</section>

<section class="section section--tint">
    <div class="container">
        <div class="section__head section__head--center">
            <div>
                <span class="eyebrow">Ne arıyorsunuz?</span>
                <h2>Kategorilere göre keşfedin</h2>
            </div>
        </div>
        <div class="cats">
            <?php
            $catCards = [
                ['daire', 'Daire', 'photo-1545324418-cc1a3fa10c00'],
                ['villa', 'Villa', 'photo-1613490493576-7fde63acd811'],
                ['rezidans', 'Rezidans', 'photo-1486406146926-c627a92ad1ab'],
                ['ofis', 'Ofis & Ticari', 'photo-1497366216548-37526070297c'],
                ['arsa', 'Arsa', 'photo-1500382017468-9049fed747ef'],
            ];
            foreach ($catCards as [$k, $label, $photo]):
                $c = (int) db_value("SELECT COUNT(*) FROM listings WHERE status='aktif' AND category = ?", [$k]); ?>
                <a class="cat" href="<?= url('ilanlar', ['kategori' => $k]) ?>">
                    <img src="<?= e(img('https://images.unsplash.com/' . $photo, 600)) ?>" alt="<?= e($label) ?>" loading="lazy">
                    <span class="cat__label"><strong><?= e($label) ?></strong><small><?= $c ?> ilan</small></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container split">
        <div class="split__media">
            <img src="<?= e(img('https://images.unsplash.com/photo-1600607687939-ce8a6c25118c', 1200)) ?>" alt="Modern salon" loading="lazy">
            <div class="floating-card">
                <span class="floating-card__icon"><?= icon('key') ?></span>
                <div><strong>Ortalama 38 gün</strong><small>içinde satış / kiralama</small></div>
            </div>
        </div>
        <div class="split__body">
            <span class="eyebrow">Neden <?= e(explode(' ', setting('site_name'))[0]) ?>?</span>
            <h2>Gayrimenkulde şeffaf, hızlı ve güvenli süreç</h2>
            <p>Mülkünüzü doğru fiyatla piyasaya sunmaktan tapu devrine kadar her adımda yanınızdayız. Bölge verilerine dayalı değerleme, profesyonel fotoğraf ve geniş alıcı ağımızla zamandan kazanırsınız.</p>
            <ul class="checklist">
                <li><?= icon('check') ?> Ücretsiz ekspertiz ve piyasa analizi</li>
                <li><?= icon('check') ?> Profesyonel fotoğraf, video ve sanal tur</li>
                <li><?= icon('check') ?> Tapu, kredi ve hukuki süreç takibi</li>
                <li><?= icon('check') ?> Yabancı yatırımcılar için çok dilli danışmanlık</li>
            </ul>
            <div class="btn-row">
                <a class="btn btn--primary" href="<?= url('hizmetlerimiz') ?>">Hizmetlerimiz</a>
                <a class="btn btn--ghost" href="<?= url('hakkimizda') ?>">Bizi tanıyın</a>
            </div>
        </div>
    </div>
</section>

<section class="section section--dark">
    <div class="container">
        <div class="section__head">
            <div>
                <span class="eyebrow eyebrow--light">Ekibimiz</span>
                <h2>Uzman danışmanlarımız</h2>
                <p>Her biri kendi bölgesinde uzmanlaşmış, lisanslı gayrimenkul danışmanları.</p>
            </div>
            <a class="btn btn--light" href="<?= url('danismanlar') ?>">Tüm ekip <?= icon('arrow') ?></a>
        </div>
        <div class="grid grid--4">
            <?php foreach ($agents as $a) partial('site/_agent_card', ['a' => $a]); ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section__head">
            <div>
                <span class="eyebrow">Blog</span>
                <h2>Piyasadan güncel yazılar</h2>
            </div>
            <a class="btn btn--ghost" href="<?= url('blog') ?>">Tüm yazılar <?= icon('arrow') ?></a>
        </div>
        <div class="grid grid--3">
            <?php foreach ($posts as $p) partial('site/_post_card', ['p' => $p]); ?>
        </div>
    </div>
</section>

<?php partial('site/_cta'); ?>
