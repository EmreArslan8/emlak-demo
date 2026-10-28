<section class="page-hero">
    <img class="page-hero__bg" src="<?= e(img($image ?? 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c', 2000)) ?>" alt="">
    <div class="container page-hero__inner">
        <nav class="crumbs" aria-label="Sayfa yolu">
            <a href="<?= url() ?>">Ana Sayfa</a>
            <?php foreach ($crumbs ?? [] as $label => $href): ?>
                <span>/</span><?php if ($href): ?><a href="<?= e($href) ?>"><?= e($label) ?></a><?php else: ?><span aria-current="page"><?= e($label) ?></span><?php endif; ?>
            <?php endforeach; ?>
        </nav>
        <h1 class="display display--md"><?= e($title) ?></h1>
        <?php if (!empty($lead)): ?><p class="page-hero__lead"><?= e($lead) ?></p><?php endif; ?>
    </div>
</section>
