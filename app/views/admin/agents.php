<div class="toolbar"><span></span><a class="btn btn--primary" href="<?= url('yonetim/danismanlar/yeni') ?>"><?= icon('plus') ?> Yeni danışman</a></div>
<div class="box box--flush">
    <table class="table">
        <thead><tr><th>Danışman</th><th>Telefon</th><th>E-posta</th><th>İlan</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($rows as $a): ?>
            <tr>
                <td class="table__main"><img class="round" src="<?= e(img($a['photo'], 120)) ?>" alt=""><div><a href="<?= url('yonetim/danismanlar/' . $a['id']) ?>"><?= e($a['name']) ?></a><small><?= e($a['title']) ?></small></div></td>
                <td class="nowrap"><?= e($a['phone']) ?></td>
                <td><?= e($a['email']) ?></td>
                <td><?= (int) $a['listing_count'] ?></td>
                <td class="table__actions">
                    <a href="<?= url('danisman/' . $a['slug']) ?>" target="_blank" title="Sitede gör"><?= icon('external') ?></a>
                    <a href="<?= url('yonetim/danismanlar/' . $a['id']) ?>" title="Düzenle"><?= icon('edit') ?></a>
                    <form method="post" action="<?= url('yonetim/danismanlar/' . $a['id'] . '/sil') ?>" onsubmit="return confirm('Danışman silinsin mi? İlanları silinmez.')"><?= csrf_field() ?><button title="Sil"><?= icon('trash') ?></button></form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
