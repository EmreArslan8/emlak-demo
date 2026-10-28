<section class="page-hero" <?php if (!empty($image)): ?>style="--bg:url('<?= e(img($image, 1800)) ?>')"<?php endif; ?>>
    <div class="container">
        <nav class="crumbs" aria-label="Sayfa yolu">
            <a href="<?= url() ?>">Ana Sayfa</a>
            <?php foreach ($crumbs ?? [] as $label => $href): ?>
                <span>/</span><?php if ($href): ?><a href="<?= e($href) ?>"><?= e($label) ?></a><?php else: ?><span aria-current="page"><?= e($label) ?></span><?php endif; ?>
            <?php endforeach; ?>
        </nav>
        <h1><?= e($title) ?></h1>
        <?php if (!empty($lead)): ?><p class="page-hero__lead"><?= e($lead) ?></p><?php endif; ?>
    </div>
</section>
