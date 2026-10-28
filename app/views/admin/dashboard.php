<div class="stats">
    <div class="stat"><span class="stat__icon"><?= icon('home') ?></span><div><small>Yayındaki ilan</small><strong><?= $counts['aktif'] ?></strong></div></div>
    <div class="stat"><span class="stat__icon stat__icon--blue"><?= icon('key') ?></span><div><small>Satılık / Kiralık</small><strong><?= $counts['satilik'] ?> / <?= $counts['kiralik'] ?></strong></div></div>
    <div class="stat"><span class="stat__icon stat__icon--gold"><?= icon('eye') ?></span><div><small>Toplam görüntülenme</small><strong><?= number_format($counts['views'], 0, ',', '.') ?></strong></div></div>
    <div class="stat"><span class="stat__icon stat__icon--rose"><?= icon('inbox') ?></span><div><small>Talepler (okunmamış)</small><strong><?= $counts['messages'] ?> <em>(<?= $unread ?>)</em></strong></div></div>
</div>

<div class="quick">
    <a class="btn btn--primary" href="<?= url('yonetim/ilanlar/yeni') ?>"><?= icon('plus') ?> Yeni ilan ekle</a>
    <a class="btn btn--ghost" href="<?= url('yonetim/blog/yeni') ?>"><?= icon('edit') ?> Blog yazısı yaz</a>
    <a class="btn btn--ghost" href="<?= url('yonetim/danismanlar/yeni') ?>"><?= icon('users') ?> Danışman ekle</a>
</div>

<div class="cols">
    <section class="box">
        <header class="box__head"><h2>En çok görüntülenen ilanlar</h2><a href="<?= url('yonetim/ilanlar') ?>">Tümü →</a></header>
        <ul class="list">
            <?php foreach ($popular as $l): ?>
                <li>
                    <img src="<?= e(img($l['cover'], 160)) ?>" alt="">
                    <div><a href="<?= url('yonetim/ilanlar/' . $l['id']) ?>"><?= e($l['title']) ?></a><small><?= e($l['district']) ?> · <?= price($l['price'], $l['type']) ?></small></div>
                    <span class="list__meta"><?= icon('eye') ?> <?= (int) $l['views'] ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="box">
        <header class="box__head"><h2>Son talepler</h2><a href="<?= url('yonetim/mesajlar') ?>">Tümü →</a></header>
        <ul class="list">
            <?php foreach ($messages as $m): ?>
                <li>
                    <span class="avatar <?= $m['is_read'] ? '' : 'avatar--new' ?>"><?= e(mb_substr($m['name'], 0, 1)) ?></span>
                    <div><strong><?= e($m['name']) ?></strong><small><?= e($m['listing_title'] ?: $m['subject']) ?></small></div>
                    <span class="list__meta"><?= tr_date($m['created_at']) ?></span>
                </li>
            <?php endforeach; ?>
            <?php if (!$messages): ?><li class="muted">Henüz talep yok.</li><?php endif; ?>
        </ul>
    </section>
</div>
