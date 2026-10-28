<section class="cta">
    <div class="container cta__inner">
        <div>
            <h2>Mülkünüzün değerini merak ediyor musunuz?</h2>
            <p>48 saat içinde ücretsiz, bölge verilerine dayalı değerleme raporunuzu hazırlayalım.</p>
        </div>
        <div class="btn-row">
            <a class="btn btn--accent" href="<?= url('iletisim', ['konu' => 'Mülkümü değerlendirin']) ?>">Ücretsiz Değerleme</a>
            <a class="btn btn--wa" href="<?= wa_link(setting('whatsapp'), 'Merhaba, mülkümün değerlemesi hakkında bilgi almak istiyorum.') ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?> WhatsApp</a>
        </div>
    </div>
</section>
