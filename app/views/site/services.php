<?php partial('site/_page_hero', ['title' => 'Hizmetlerimiz', 'crumbs' => ['Hizmetlerimiz' => null],
    'lead' => 'Alım, satım, kiralama ve yatırım süreçlerinizin her adımında uçtan uca profesyonel destek.',
    'image' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa']); ?>

<section class="section">
    <div class="container grid grid--3">
        <?php foreach ([
            ['home', 'Satış Danışmanlığı', 'Doğru fiyatlama, profesyonel pazarlama ve geniş alıcı ağıyla mülkünüzü en kısa sürede, en iyi değerle satıyoruz.', ['Ücretsiz değerleme raporu', 'Profesyonel fotoğraf & video', 'Tüm portallarda öne çıkarma']],
            ['key', 'Kiralama & Kiracı Bulma', 'Kiracı adaylarını finansal ve referans kontrolünden geçirir, kira sözleşmesini sizin adınıza hazırlarız.', ['Kiracı ön eleme', 'Sözleşme & depozito yönetimi', 'Teslim tutanağı']],
            ['chart', 'Yatırım Danışmanlığı', 'Bütçenize ve hedefinize göre kira getirisi ve değer artışı potansiyeli yüksek portföy oluşturuyoruz.', ['Getiri & amortisman analizi', 'Proje karşılaştırması', 'Yabancı yatırımcı desteği']],
            ['file', 'Tapu & Hukuki Süreç', 'Tapu randevusundan harç hesaplamasına, iskân ve imar kontrolüne kadar tüm süreci yönetiyoruz.', ['Evrak ön kontrolü', 'Harç & masraf hesaplama', 'Avukat yönlendirmesi']],
            ['building', 'Kurumsal & Ticari', 'Şirketiniz için ofis, mağaza veya depo arayışında ihtiyaç analizinden sözleşmeye kadar destek.', ['Lokasyon analizi', 'Kira müzakeresi', 'Fit-out koordinasyonu']],
            ['shield', 'Kentsel Dönüşüm', 'Riskli yapı tespitinden müteahhit seçimine kadar hak sahiplerinin haklarını koruyoruz.', ['Müteahhit karşılaştırması', 'Sözleşme danışmanlığı', 'Kira yardımı başvurusu']],
        ] as [$ic, $t, $d, $items]): ?>
            <div class="feature-box feature-box--card">
                <span class="feature-box__icon"><?= icon($ic) ?></span>
                <h3><?= e($t) ?></h3>
                <p><?= e($d) ?></p>
                <ul class="checklist checklist--sm"><?php foreach ($items as $i): ?><li><?= icon('check') ?> <?= e($i) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="section section--tint">
    <div class="container">
        <div class="section__head section__head--center"><div><span class="eyebrow">Süreç</span><h2>4 adımda satış</h2></div></div>
        <ol class="steps">
            <li><span>01</span><h3>Tanışma & değerleme</h3><p>Mülkünüzü yerinde inceler, 48 saat içinde değerleme raporu sunarız.</p></li>
            <li><span>02</span><h3>Pazarlama</h3><p>Profesyonel çekim, ilan metni ve hedefli dijital kampanyalar hazırlarız.</p></li>
            <li><span>03</span><h3>Görüşme & pazarlık</h3><p>Nitelikli alıcılarla görüşmeleri organize eder, en iyi teklifi müzakere ederiz.</p></li>
            <li><span>04</span><h3>Tapu & teslim</h3><p>Tüm evrak ve tapu sürecini yönetir, anahtar teslimine kadar yanınızda oluruz.</p></li>
        </ol>
    </div>
</section>
<?php partial('site/_cta'); ?>
