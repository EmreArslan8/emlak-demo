<section class="cta">
    <img class="cta__bg" src="<?= e(img('https://images.unsplash.com/photo-1600210492486-724fe5c67fb0', 2000)) ?>" alt="" loading="lazy">
    <div class="container cta__inner">
        <p class="kicker kicker--light">Ücretsiz değerleme</p>
        <h2 class="display display--sm">Mülkünüzün bugünkü değerini öğrenin.</h2>
        <p>Bölge verilerine dayalı, emsal analizli değerleme raporunuz 48 saat içinde hazır.</p>
        <div class="btn-row btn-row--center">
            <a class="btn btn--light" href="<?= url('iletisim', ['konu' => 'Mülkümü değerlendirin']) ?>">Değerleme talep et</a>
            <a class="btn btn--ghost-light" href="<?= wa_link(setting('whatsapp'), 'Merhaba, mülkümün değerlemesi hakkında bilgi almak istiyorum.') ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?> WhatsApp</a>
        </div>
    </div>
</section>
