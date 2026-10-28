<?php $hero = $featured[0] ?? null; ?>
<section class="hero">
    <div class="hero__bg">
        <img src="<?= e(img('https://images.unsplash.com/photo-1613490493576-7fde63acd811', 2400)) ?>" alt="" fetchpriority="high">
    </div>
    <div class="container hero__inner">
        <p class="kicker kicker--light">İstanbul · Since 2004</p>
        <h1 class="display"><?= e(setting('hero_title')) ?></h1>
        <p class="hero__lead"><?= e(setting('hero_subtitle')) ?></p>

        <form class="search" action="<?= url('ilanlar') ?>" method="get">
            <div class="search__tabs">
                <label><input type="radio" name="tip" value="satilik" checked><span>Satılık</span></label>
                <label><input type="radio" name="tip" value="kiralik"><span>Kiralık</span></label>
            </div>
            <div class="search__fields">
                <label class="search__field">
                    <span>Konum</span>
                    <select name="ilce">
                        <option value="">Tüm İstanbul</option>
                        <?php foreach ($districts as $d): ?><option><?= e($d['district']) ?></option><?php endforeach; ?>
                    </select>
                </label>
                <label class="search__field">
                    <span>Mülk tipi</span>
                    <select name="kategori">
                        <option value="">Tümü</option>
                        <?php foreach (CATEGORIES as $k => $v): ?><option value="<?= $k ?>"><?= e($v) ?></option><?php endforeach; ?>
                    </select>
                </label>
                <label class="search__field">
                    <span>Oda</span>
                    <select name="oda">
                        <option value="">Farketmez</option>
                        <?php foreach (['1+1', '2+1', '3+1', '4+'] as $r): ?><option><?= $r ?></option><?php endforeach; ?>
                    </select>
                </label>
                <label class="search__field">
                    <span>Bütçe (maks.)</span>
                    <input type="text" name="max_fiyat" inputmode="numeric" placeholder="₺" data-money>
                </label>
                <button class="search__btn" type="submit" aria-label="Ara"><?= icon('search') ?><span>Portföyü keşfet</span></button>
            </div>
        </form>
        <nav class="quick-links" aria-label="Hızlı arama">
            <?php foreach ($catCounts as $k => $c): ?>
                <a href="<?= url('ilanlar', ['kategori' => $k]) ?>"><?= e(category_label($k)) ?> <small><?= $c ?></small></a>
            <?php endforeach; ?>
        </nav>
    </div>
</section>



<section class="section" id="portfoy">
    <div class="container">
        <div class="section__head">
            <div>
                <p class="kicker">Seçkin portföy</p>
                <h2 class="title">Öne çıkan ilanlar</h2>
            </div>
            <nav class="tabs" aria-label="İlan tipi">
                <a href="<?= url('ilanlar') ?>">Tümü <small><?= $stats['aktif'] ?></small></a>
                <a href="<?= url('satilik') ?>">Satılık <small><?= $stats['satilik'] ?></small></a>
                <a href="<?= url('kiralik') ?>">Kiralık <small><?= $stats['kiralik'] ?></small></a>
            </nav>
        </div>
        <?php if ($hero): ?>
            <a class="showcase" href="<?= url('ilan/' . $hero['slug']) ?>">
                <div class="showcase__media"><img src="<?= e(img($hero['cover'], 1800)) ?>" alt="<?= e($hero['title']) ?>" loading="lazy"></div>
                <div class="showcase__body">
                    <p class="kicker"><?= e(LISTING_TYPES[$hero['type']]) ?> · <?= e($hero['neighborhood']) ?>, <?= e($hero['district']) ?></p>
                    <h3 class="showcase__title"><?= e($hero['title']) ?></h3>
                    <p class="showcase__text"><?= e(excerpt($hero['description'], 180)) ?></p>
                    <ul class="specs">
                        <?php if ($hero['rooms'] !== '-'): ?><li><strong><?= e($hero['rooms']) ?></strong>Oda</li><?php endif; ?>
                        <li><strong><?= number_format((int) $hero['gross_m2'], 0, ',', '.') ?></strong>m²</li>
                        <?php if ($hero['bathrooms']): ?><li><strong><?= (int) $hero['bathrooms'] ?></strong>Banyo</li><?php endif; ?>
                    </ul>
                    <p class="showcase__price"><?= price($hero['price'], $hero['type']) ?></p>
                    <span class="link-arrow">Mülkü inceleyin <?= icon('arrow') ?></span>
                </div>
            </a>
        <?php endif; ?>
        <div class="grid grid--3">
            <?php foreach (array_slice($featured, 1) as $l) partial('site/_card', ['l' => $l]); ?>
        </div>
        <div class="center-cta"><a class="btn btn--dark" href="<?= url('ilanlar') ?>">Tüm ilanları görüntüle (<?= $stats['aktif'] ?>) <?= icon('arrow') ?></a></div>
    </div>
</section>

<section class="intro">
    <div class="container intro__inner">
        <p class="kicker">Vista Gayrimenkul</p>
        <p class="intro__text">Boğaz'dan Bağdat Caddesi'ne, İstanbul'un en seçkin adreslerinde <em>yerinde incelenmiş</em> bir portföy ve her adımda yanınızda olan uzman bir ekip.</p>
        <ul class="numbers">
            <li><strong>20+</strong><span>Yıllık deneyim</span></li>
            <li><strong>1.200+</strong><span>Tamamlanan işlem</span></li>
            <li><strong>₺4,8 mr</strong><span>Toplam işlem hacmi</span></li>
            <li><strong><?= $stats['aktif'] ?></strong><span>Aktif portföy</span></li>
        </ul>
    </div>
</section>

<section class="section section--stone">
    <div class="container">
        <div class="section__head">
            <div>
                <p class="kicker">Semtler</p>
                <h2 class="title">İstanbul'u semt semt keşfedin</h2>
            </div>
            <p class="section__aside">Her semtin karakterini, fiyat dinamiklerini ve gelecek potansiyelini bölge uzmanlarımızdan dinleyin.</p>
        </div>
        <div class="districts">
            <?php foreach ($neighborhoods as $n): ?>
                <a class="district" href="<?= url('ilanlar', ['ilce' => $n['district']]) ?>">
                    <img src="<?= e(img($n['cover'], 900)) ?>" alt="<?= e($n['district']) ?>" loading="lazy">
                    <span class="district__label"><strong><?= e($n['district']) ?></strong><small><?= (int) $n['c'] ?> mülk <?= icon('arrow') ?></small></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container split">
        <div class="split__media">
            <img src="<?= e(img('https://images.unsplash.com/photo-1600607687939-ce8a6c25118c', 1400)) ?>" alt="Modern salon" loading="lazy">
        </div>
        <div class="split__body">
            <p class="kicker">Yaklaşımımız</p>
            <h2 class="title">Az ama özenle seçilmiş. Her mülk, her müşteri.</h2>
            <p>Mülkünüzü doğru fiyatla piyasaya sunmaktan tapu devrine kadar her adımı tek bir danışman yönetir. Bölge verilerine dayalı değerleme, profesyonel prodüksiyon ve seçkin alıcı ağımızla süreci hızlı ve gizli yürütürüz.</p>
            <ol class="pillars">
                <li><span>01</span><div><strong>Veriye dayalı değerleme</strong>48 saat içinde emsal analizli rapor.</div></li>
                <li><span>02</span><div><strong>Prodüksiyon & pazarlama</strong>Profesyonel fotoğraf, film ve hedefli kampanyalar.</div></li>
                <li><span>03</span><div><strong>Uçtan uca süreç</strong>Kredi, tapu ve hukuki takip tek elden.</div></li>
            </ol>
            <a class="btn btn--dark" href="<?= url('hizmetlerimiz') ?>">Hizmetlerimiz</a>
        </div>
    </div>
</section>

<?php if ($sold): ?>
<section class="section section--dark">
    <div class="container">
        <div class="section__head">
            <div>
                <p class="kicker kicker--light">Son işlemler</p>
                <h2 class="title">Satılanlar</h2>
            </div>
            <p class="section__aside">Gizlilik esasıyla yürüttüğümüz işlemlerden bazıları.</p>
        </div>
        <div class="grid grid--3">
            <?php foreach ($sold as $l): ?>
                <a class="sold" href="<?= url('ilan/' . $l['slug']) ?>">
                    <div class="sold__media"><img src="<?= e(img($l['cover'], 900)) ?>" alt="<?= e($l['title']) ?>" loading="lazy"><span class="sold__tag">Satıldı</span></div>
                    <p class="sold__loc"><?= e($l['neighborhood']) ?>, <?= e($l['district']) ?></p>
                    <h3><?= e($l['title']) ?></h3>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section">
    <div class="container">
        <div class="section__head section__head--center">
            <div>
                <p class="kicker">Müşterilerimiz</p>
                <h2 class="title">Güvenle anlatılan hikâyeler</h2>
            </div>
        </div>
        <div class="quotes">
            <?php foreach ([
                ['Yurt dışındayken evimizin satış sürecini baştan sona Elif Hanım yönetti. Tapu gününe kadar her şey şeffaf ve planlandığı gibiydi.', 'Selin & Kerem T.', 'Bebek · Satış'],
                ['Üç hafta içinde doğru kiracıyı buldular; sözleşmeden teslim tutanağına kadar hiçbir detayla uğraşmadım.', 'Ahmet Y.', 'Fenerbahçe · Kiralama'],
                ['Şirketimizin yeni ofisi için Maslak\'ta beş alternatifi karşılaştırmalı raporla sundular. Kararı vermek çok kolaylaştı.', 'Deniz K., Operasyon Direktörü', 'Maslak · Ticari'],
            ] as [$q, $who, $what]): ?>
                <figure class="quote">
                    <blockquote>“<?= e($q) ?>”</blockquote>
                    <figcaption><strong><?= e($who) ?></strong><span><?= e($what) ?></span></figcaption>
                </figure>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--stone">
    <div class="container">
        <div class="section__head">
            <div>
                <p class="kicker">Ekibimiz</p>
                <h2 class="title">Danışmanlarımız</h2>
            </div>
            <a class="link-arrow" href="<?= url('danismanlar') ?>">Tüm ekip <?= icon('arrow') ?></a>
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
                <p class="kicker">Journal</p>
                <h2 class="title">Piyasadan notlar</h2>
            </div>
            <a class="link-arrow" href="<?= url('blog') ?>">Tüm yazılar <?= icon('arrow') ?></a>
        </div>
        <div class="grid grid--3">
            <?php foreach ($posts as $p) partial('site/_post_card', ['p' => $p]); ?>
        </div>
    </div>
</section>

<?php partial('site/_cta'); ?>
