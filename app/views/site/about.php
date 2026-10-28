<?php partial('site/_page_hero', ['title' => 'Hakkımızda', 'crumbs' => ['Hakkımızda' => null],
    'lead' => '2004\'ten bu yana İstanbul\'da binlerce aileyi doğru evle buluşturuyoruz.',
    'image' => 'https://images.unsplash.com/photo-1524231757912-21f4fe3a7200']); ?>

<section class="section">
    <div class="container split">
        <div class="split__body">
            <span class="eyebrow">Hikâyemiz</span>
            <h2>Küçük bir semt ofisinden İstanbul'un güvenilir markasına</h2>
            <p><?= e(setting('site_name')) ?>, 2004 yılında Caddebostan'da iki kişilik bir ekip olarak kuruldu. Bugün 4 ilçede uzmanlaşmış danışman kadrosu, kurumsal yatırımcı ağı ve yüzlerce aktif portföyüyle İstanbul'un önde gelen butik gayrimenkul ofislerinden biri.</p>
            <p>Değişmeyen tek şey yaklaşımımız: her müşteriye kendi evini arıyormuş gibi davranmak. Şeffaf fiyatlama, eksiksiz evrak kontrolü ve satış sonrası destek bu yaklaşımın parçası.</p>
            <ul class="mini-stats mini-stats--big">
                <li><strong>2004</strong> Kuruluş</li>
                <li><strong>1.200+</strong> İşlem</li>
                <li><strong>%94</strong> Tavsiye oranı</li>
            </ul>
        </div>
        <div class="split__media">
            <img src="<?= e(img('https://images.unsplash.com/photo-1497366811353-6870744d04b2', 1200)) ?>" alt="Ofisimiz" loading="lazy">
        </div>
    </div>
</section>

<section class="section section--tint">
    <div class="container">
        <div class="section__head section__head--center"><div><span class="eyebrow">Değerlerimiz</span><h2>Bizi farklı kılan ilkeler</h2></div></div>
        <div class="grid grid--3">
            <?php foreach ([
                ['shield', 'Güven', 'Her ilanı yerinde inceler, tapu ve imar durumunu yayına almadan önce doğrularız.'],
                ['chart', 'Veriye dayalı', 'Fiyatlamayı tahminle değil; bölge satış verileri ve güncel emsal analiziyle yaparız.'],
                ['users', 'İnsan odaklı', 'Satış sonrası da yanınızdayız: taşınma, tadilat ve kiracı yönetimi için çözüm ortaklarımız hazır.'],
            ] as [$ic, $t, $d]): ?>
                <div class="feature-box"><span class="feature-box__icon"><?= icon($ic) ?></span><h3><?= $t ?></h3><p><?= $d ?></p></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section__head"><div><span class="eyebrow">Ekip</span><h2>Danışman kadromuz</h2></div><a class="btn btn--ghost" href="<?= url('danismanlar') ?>">Tümünü gör <?= icon('arrow') ?></a></div>
        <div class="grid grid--4"><?php foreach ($agents as $a) partial('site/_agent_card', ['a' => $a]); ?></div>
    </div>
</section>
<?php partial('site/_cta'); ?>
