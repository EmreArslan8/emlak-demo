<div class="toolbar">
    <form class="toolbar__search" method="get">
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Başlık veya ilçe ara…">
        <select name="durum" onchange="this.form.submit()">
            <option value="">Tüm durumlar</option>
            <?php foreach (STATUSES as $k => $v): ?><option value="<?= $k ?>" <?= $status === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?>
        </select>
    </form>
    <a class="btn btn--primary" href="<?= url('yonetim/ilanlar/yeni') ?>"><?= icon('plus') ?> Yeni ilan</a>
</div>
<div class="box box--flush">
    <table class="table">
        <thead><tr><th>İlan</th><th>Tip</th><th>Fiyat</th><th>Danışman</th><th>Durum</th><th>Görüntülenme</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($rows as $l): ?>
            <tr>
                <td class="table__main">
                    <img src="<?= e(img($l['cover'], 160)) ?>" alt="">
                    <div><a href="<?= url('yonetim/ilanlar/' . $l['id']) ?>"><?= e($l['title']) ?></a><small>#<?= $l['id'] ?> · <?= e($l['neighborhood']) ?>, <?= e($l['district']) ?><?= $l['featured'] ? ' · ★ Öne çıkan' : '' ?></small></div>
                </td>
                <td><span class="tag tag--<?= e($l['type']) ?>"><?= LISTING_TYPES[$l['type']] ?></span><small class="block"><?= e(category_label($l['category'])) ?></small></td>
                <td class="nowrap"><?= price($l['price'], $l['type']) ?></td>
                <td><?= e($l['agent_name'] ?? '—') ?></td>
                <td><span class="status status--<?= e($l['status']) ?>"><?= STATUSES[$l['status']] ?? $l['status'] ?></span></td>
                <td><?= (int) $l['views'] ?></td>
                <td class="table__actions">
                    <a href="<?= url('ilan/' . $l['slug']) ?>" target="_blank" title="Sitede gör"><?= icon('external') ?></a>
                    <a href="<?= url('yonetim/ilanlar/' . $l['id']) ?>" title="Düzenle"><?= icon('edit') ?></a>
                    <form method="post" action="<?= url('yonetim/ilanlar/' . $l['id'] . '/sil') ?>" onsubmit="return confirm('Bu ilan kalıcı olarak silinsin mi?')"><?= csrf_field() ?><button title="Sil"><?= icon('trash') ?></button></form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="7" class="muted center">Kayıt bulunamadı.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
