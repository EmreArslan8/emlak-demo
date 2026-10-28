<?php
$errors ??= [];
$old ??= [];
$ok = flash('success');
?>
<?php if ($ok): ?><div class="alert alert--success"><?= icon('check') ?> <?= e($ok) ?></div><?php endif; ?>
<form class="form" method="post" action="<?= e($action) ?>" novalidate>
    <?= csrf_field() ?>
    <input class="hp" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
    <?php if (!empty($withSubject)): ?>
        <label class="field">
            <span>Konu</span>
            <select name="subject">
                <?php foreach (['Genel bilgi', 'Satılık ilan arıyorum', 'Kiralık ilan arıyorum', 'Mülkümü değerlendirin', 'Yatırım danışmanlığı'] as $s): ?>
                    <option <?= ($old['subject'] ?? input('konu')) === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    <?php endif; ?>
    <label class="field <?= isset($errors['name']) ? 'has-error' : '' ?>">
        <span>Ad Soyad *</span>
        <input type="text" name="name" value="<?= e($old['name'] ?? '') ?>" autocomplete="name" required>
        <?php if (isset($errors['name'])): ?><em><?= e($errors['name']) ?></em><?php endif; ?>
    </label>
    <div class="field-row">
        <label class="field <?= isset($errors['email']) ? 'has-error' : '' ?>">
            <span>E-posta</span>
            <input type="email" name="email" value="<?= e($old['email'] ?? '') ?>" autocomplete="email">
            <?php if (isset($errors['email'])): ?><em><?= e($errors['email']) ?></em><?php endif; ?>
        </label>
        <label class="field">
            <span>Telefon</span>
            <input type="tel" name="phone" value="<?= e($old['phone'] ?? '') ?>" autocomplete="tel" placeholder="05xx xxx xx xx">
        </label>
    </div>
    <label class="field <?= isset($errors['message']) ? 'has-error' : '' ?>">
        <span>Mesajınız *</span>
        <textarea name="message" rows="4" required><?= e($old['message'] ?? ($defaultMessage ?? '')) ?></textarea>
        <?php if (isset($errors['message'])): ?><em><?= e($errors['message']) ?></em><?php endif; ?>
    </label>
    <label class="check">
        <input type="checkbox" required checked> <span>KVKK aydınlatma metnini okudum, iletişime geçilmesini onaylıyorum.</span>
    </label>
    <button class="btn btn--primary btn--block" type="submit"><?= e($submitLabel ?? 'Gönder') ?></button>
</form>
