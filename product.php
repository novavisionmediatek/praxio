<?php
$pageFile = 'product.php';
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/icons.php';
$pageTitle = c('product_seo_title');
$pageDesc = c('product_seo_desc');
require __DIR__ . '/includes/header.php';
$cats     = rows("SELECT * FROM asset_categories WHERE placement='product' AND is_active=1 ORDER BY sort_order, id");
$compare  = rows('SELECT * FROM compare_rows WHERE is_active=1 ORDER BY sort_order, id');
$diligence = rows("SELECT * FROM features WHERE placement='diligence' AND is_active=1 ORDER BY sort_order, id");
$steps    = rows("SELECT * FROM steps WHERE placement='product' AND is_active=1 ORDER BY sort_order, id");
?>

<section class="phero">
  <div class="bgimg imgwrap" data-parallax><img alt="" src="<?= img(c('product_hero_img')) ?>"></div>
  <div class="container"><div class="crumb"><a href="index.php">Home</a> / Product</div>
    <h1 data-split><?= ch('product_hero_title') ?></h1>
    <p class="reveal" style="--d:300ms"><?= e(c('product_hero_sub')) ?></p></div>
</section>

<section>
  <div class="container split">
    <div class="reveal left"><span class="eyebrow"><?= e(c('product_what_eyebrow')) ?></span><h2 data-split><?= ch('product_what_title') ?></h2>
      <p style="margin-top:20px;font-size:1.08rem"><?= e(c('product_what_text')) ?></p>
      <ul class="list"><?php foreach (cl('product_what_list') as $li): ?><li><?= e($li) ?></li><?php endforeach; ?></ul></div>
    <div class="frame reveal right"><div class="imgwrap" data-parallax><img loading="lazy" alt="<?= e(c('product_what_title')) ?>" src="<?= img(c('product_what_img')) ?>"></div>
      <div class="float f1"><div class="dot"><?= e(c('product_what_f_big')) ?></div><div><b><?= e(c('product_what_f_title')) ?></b><small><?= e(c('product_what_f_sub')) ?></small></div></div></div>
  </div>
</section>

<section class="soft">
  <div class="container">
    <div class="head c"><span class="eyebrow"><?= e(c('product_compare_eyebrow')) ?></span><h2 data-split><?= ch('product_compare_title') ?></h2></div>
    <div class="reveal" style="overflow-x:auto"><table class="compare" style="margin-top:0"><thead><tr><th></th><th><?= e(c('product_compare_col1')) ?></th><th><?= e(c('product_compare_col2')) ?></th></tr></thead><tbody>
      <?php foreach ($compare as $r): ?><tr><td><?= e($r['aspect']) ?></td><td><?= e($r['loan_text']) ?></td><td><span class="tick">✓</span> <?= e($r['lease_text']) ?></td></tr><?php endforeach; ?>
    </tbody></table></div>
  </div>
</section>

<section>
  <div class="container">
    <div class="head c"><span class="eyebrow"><?= e(c('product_assets_eyebrow')) ?></span><h2 data-split><?= ch('product_assets_title') ?></h2></div>
    <div class="grid g4">
      <?php foreach ($cats as $i => $c): ?>
      <div class="prod reveal"<?= $i ? ' style="--d:' . ($i * 100) . 'ms"' : '' ?>><div class="imgwrap"><img loading="lazy" alt="<?= e($c['title']) ?>" src="<?= img($c['image_url']) ?>"></div><div class="prod-b"><h3><?= e($c['title']) ?></h3><p><?= e($c['description']) ?></p></div></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="dark">
  <div class="container">
    <div class="head"><span class="eyebrow"><?= e(c('product_diligence_eyebrow')) ?></span><h2 data-split><?= ch('product_diligence_title') ?></h2></div>
    <div class="grid g3"><?php feature_cards($diligence); ?></div>
  </div>
</section>

<section>
  <div class="container">
    <div class="head c"><span class="eyebrow"><?= e(c('product_process_eyebrow')) ?></span><h2 data-split><?= ch('product_process_title') ?></h2></div>
    <div class="steps">
      <?php foreach ($steps as $i => $s): ?><div class="step reveal"<?= $i ? ' style="--d:' . ($i * 120) . 'ms"' : '' ?>><div class="n"><?= $i + 1 ?></div><h3><?= e($s['title']) ?></h3><p><?= e($s['description']) ?></p></div><?php endforeach; ?>
    </div>
  </div>
</section>

<section class="soft" style="text-align:center"><div class="container"><h2 data-split><?= ch('product_cta_title') ?></h2><p style="margin:18px 0 30px"><?= e(c('product_cta_sub')) ?></p><a href="contact.php" class="btn btn-gold"><?= e(c('product_cta_btn')) ?> <span class="arr">→</span></a></div></section>

<?php require __DIR__ . '/includes/footer.php'; ?>
