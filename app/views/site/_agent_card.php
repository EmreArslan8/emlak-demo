<article class="agent">
    <a class="agent__photo" href="<?= url('danisman/' . $a['slug']) ?>">
        <img src="<?= e(img($a['photo'], 600)) ?>" alt="<?= e($a['name']) ?>" loading="lazy">
    </a>
    <div class="agent__body">
        <h3><a href="<?= url('danisman/' . $a['slug']) ?>"><?= e($a['name']) ?></a></h3>
        <p><?= e($a['title']) ?></p>
        <div class="agent__actions">
            <a href="<?= tel_link($a['phone']) ?>" aria-label="<?= e($a['name']) ?> ara"><?= icon('phone') ?></a>
            <a href="<?= wa_link($a['phone']) ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><?= icon('whatsapp') ?></a>
            <a href="mailto:<?= e($a['email']) ?>" aria-label="E-posta"><?= icon('mail') ?></a>
        </div>
    </div>
</article>
