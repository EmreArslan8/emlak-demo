<form class="form-grid" method="post" action="<?= url('yonetim/ayarlar') ?>">
    <?= csrf_field() ?>
    <div class="form-grid__main">
        <?php foreach ($groups as $group => $fields): ?>
            <section class="box">
                <h2><?= e($group) ?></h2>
                <div class="row2">
                    <?php foreach ($fields as $k => $label): $long = in_array($k, ['meta_description', 'hero_subtitle', 'address'], true); ?>
                        <label class="field <?= $long ? 'span2' : '' ?>"><span><?= e($label) ?></span>
                            <?php if ($long): ?><textarea name="<?= $k ?>" rows="2"><?= e(setting($k)) ?></textarea>
                            <?php else: ?><input name="<?= $k ?>" value="<?= e(setting($k)) ?>"><?php endif; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
    <aside class="form-grid__side">
        <section class="box sticky">
            <h2>Güvenlik</h2>
            <label class="field"><span>Yeni şifre (değiştirmek için)</span><input type="password" name="new_password" autocomplete="new-password" minlength="8"></label>
            <button class="btn btn--primary btn--block" type="submit">Ayarları kaydet</button>
        </section>
    </aside>
</form>
