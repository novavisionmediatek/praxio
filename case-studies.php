<?php
$pageFile = 'case-studies.php';
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = c('cases_seo_title');
$pageDesc = c('cases_seo_desc');
require __DIR__ . '/includes/header.php';
$cases = rows('SELECT * FROM case_studies WHERE is_active=1 ORDER BY sort_order, id');
?>

<section class="phero">
  <div class="bgimg imgwrap" data-parallax><img alt="" src="<?= img(c('cases_hero_img')) ?>"></div>
  <div class="container"><div class="crumb"><a href="index.php">Home</a> / Case studies</div>
    <h1 data-split><?= ch('cases_hero_title') ?></h1>
    <p class="reveal" style="--d:300ms"><?= e(c('cases_hero_sub')) ?></p></div>
</section>

<section>
  <div class="container">
    <?php foreach ($cases as $i => $c): $even = $i % 2 === 0; $last = $i === count($cases) - 1; ?>
    <div class="crow" id="case-<?= (int)$c['id'] ?>"<?= $last ? ' style="margin-bottom:0"' : '' ?>>
      <div class="imgwrap reveal <?= $even ? 'left' : 'right' ?>" data-parallax><img loading="lazy" alt="<?= e($c['image_alt']) ?>" src="<?= img($c['image_url']) ?>"></div>
      <div class="reveal <?= $even ? 'right' : 'left' ?>"><span class="tag"><?= e($c['tag']) ?></span><h2 style="font-size:2.2rem"><?= e($c['title']) ?></h2>
        <div class="metrics"><?php foreach (pairs($c['metrics']) as [$v, $l]): ?><div><b><?= e($v) ?></b><small><?= e($l) ?></small></div><?php endforeach; ?></div>
        <h4>Challenge</h4><p><?= e($c['challenge']) ?></p>
        <h4>Solution</h4><p><?= e($c['solution']) ?></p>
        <h4>Outcome</h4><p><?= e($c['outcome']) ?></p>
        <p style="margin-top:22px"><a class="btn btn-navy" href="case-study.php?slug=<?= e($c['slug']) ?>">Read the full case study <span class="arr">→</span></a></p></div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="dark" style="text-align:center"><div class="container"><h2 data-split><?= ch('cases_cta_title') ?></h2><p style="margin:18px 0 30px"><?= e(c('cases_cta_sub')) ?></p><a href="contact.php" class="btn btn-gold"><?= e(c('cases_cta_btn')) ?> <span class="arr">→</span></a></div></section>

<?php require __DIR__ . '/includes/footer.php'; ?>
