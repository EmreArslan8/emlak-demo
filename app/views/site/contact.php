<?php partial('site/_page_hero', ['title' => 'İletişim', 'crumbs' => ['İletişim' => null],
    'lead' => 'Sorularınız, değerleme talepleriniz veya randevu için bize ulaşın — aynı gün dönüş yapıyoruz.']); ?>
<section class="section section--tight">
    <div class="container contact">
        <div class="contact__info">
            <a class="contact-card" href="<?= tel_link(setting('phone')) ?>"><span><?= icon('phone') ?></span><div><small>Telefon</small><strong><?= e(setting('phone')) ?></strong></div></a>
            <a class="contact-card contact-card--wa" href="<?= wa_link(setting('whatsapp'), 'Merhaba, bilgi almak istiyorum.') ?>" target="_blank" rel="noopener"><span><?= icon('whatsapp') ?></span><div><small>WhatsApp hızlı erişim</small><strong><?= e(setting('whatsapp')) ?></strong></div></a>
            <a class="contact-card" href="mailto:<?= e(setting('email')) ?>"><span><?= icon('mail') ?></span><div><small>E-posta</small><strong><?= e(setting('email')) ?></strong></div></a>
            <div class="contact-card"><span><?= icon('pin') ?></span><div><small>Adres</small><strong><?= e(setting('address')) ?></strong></div></div>
            <div class="contact-card"><span><?= icon('clock') ?></span><div><small>Çalışma saatleri</small><strong><?= e(setting('working_hours')) ?></strong></div></div>
        </div>
        <div class="panel">
            <h2>Bize yazın</h2>
            <?php partial('site/_message_form', ['action' => url('iletisim'), 'withSubject' => true, 'errors' => $errors ?? [], 'old' => $old ?? [], 'submitLabel' => 'Mesajı gönder']); ?>
        </div>
    </div>
    <div class="container">
        <div class="map map--lg" data-map data-lat="<?= e(setting('office_lat')) ?>" data-lng="<?= e(setting('office_lng')) ?>" data-zoom="16" data-popup="<?= e(setting('site_name')) ?>"></div>
    </div>
</section>
