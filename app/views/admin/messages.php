<div class="messages">
    <?php foreach ($rows as $m): ?>
        <article class="box message <?= $m['is_read'] ? '' : 'message--new' ?>">
            <header>
                <span class="avatar <?= $m['is_read'] ? '' : 'avatar--new' ?>"><?= e(mb_substr($m['name'], 0, 1)) ?></span>
                <div>
                    <strong><?= e($m['name']) ?></strong>
                    <small><?= e($m['subject']) ?> · <?= tr_date($m['created_at']) ?> <?= e(substr($m['created_at'], 11, 5)) ?></small>
                </div>
                <div class="message__actions">
                    <form method="post" action="<?= url('yonetim/mesajlar/' . $m['id'] . '/okundu') ?>"><?= csrf_field() ?><button class="btn btn--ghost btn--sm"><?= $m['is_read'] ? 'Okunmadı yap' : 'Okundu' ?></button></form>
                    <form method="post" action="<?= url('yonetim/mesajlar/' . $m['id'] . '/sil') ?>" onsubmit="return confirm('Mesaj silinsin mi?')"><?= csrf_field() ?><button class="btn btn--ghost btn--sm" title="Sil"><?= icon('trash') ?></button></form>
                </div>
            </header>
            <p><?= nl2br(e($m['message'])) ?></p>
            <footer>
                <?php if ($m['phone']): ?><a href="<?= tel_link($m['phone']) ?>"><?= icon('phone') ?> <?= e($m['phone']) ?></a>
                    <a href="<?= wa_link(preg_replace('/^0/', '90', preg_replace('/\D/', '', $m['phone']))) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?> WhatsApp</a><?php endif; ?>
                <?php if ($m['email']): ?><a href="mailto:<?= e($m['email']) ?>"><?= icon('mail') ?> <?= e($m['email']) ?></a><?php endif; ?>
                <?php if ($m['listing_title']): ?><a href="<?= url('ilan/' . $m['listing_slug']) ?>" target="_blank"><?= icon('home') ?> <?= e($m['listing_title']) ?></a><?php endif; ?>
            </footer>
        </article>
    <?php endforeach; ?>
    <?php if (!$rows): ?><div class="box muted center">Henüz gelen talep yok.</div><?php endif; ?>
</div>
