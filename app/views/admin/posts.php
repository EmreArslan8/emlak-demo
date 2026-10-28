<div class="toolbar"><span></span><a class="btn btn--primary" href="<?= url('yonetim/blog/yeni') ?>"><?= icon('plus') ?> Yeni yazı</a></div>
<div class="box box--flush">
    <table class="table">
        <thead><tr><th>Yazı</th><th>Kategori</th><th>Tarih</th><th>Durum</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($rows as $p): ?>
            <tr>
                <td class="table__main"><img src="<?= e(img($p['cover'], 160)) ?>" alt=""><div><a href="<?= url('yonetim/blog/' . $p['id']) ?>"><?= e($p['title']) ?></a><small><?= e(excerpt($p['excerpt'] ?? '', 80)) ?></small></div></td>
                <td><?= e($p['category']) ?></td>
                <td class="nowrap"><?= tr_date($p['published_at']) ?></td>
                <td><span class="status status--<?= $p['published'] ? 'aktif' : 'pasif' ?>"><?= $p['published'] ? 'Yayında' : 'Taslak' ?></span></td>
                <td class="table__actions">
                    <a href="<?= url('blog/' . $p['slug']) ?>" target="_blank" title="Sitede gör"><?= icon('external') ?></a>
                    <a href="<?= url('yonetim/blog/' . $p['id']) ?>" title="Düzenle"><?= icon('edit') ?></a>
                    <form method="post" action="<?= url('yonetim/blog/' . $p['id'] . '/sil') ?>" onsubmit="return confirm('Yazı silinsin mi?')"><?= csrf_field() ?><button title="Sil"><?= icon('trash') ?></button></form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
