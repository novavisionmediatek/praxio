<?php
$pageFile = 'faq.php';
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = c('faq_seo_title');
$pageDesc = c('faq_seo_desc');
require __DIR__ . '/includes/header.php';
$faqs = rows('SELECT * FROM faqs WHERE is_active=1 ORDER BY sort_order, id');
?>

<section class="phero">
  <div class="bgimg imgwrap" data-parallax><img alt="" src="<?= img(c('faq_hero_img')) ?>"></div>
  <div class="container"><div class="crumb"><a href="index.php">Home</a> / FAQ</div>
    <h1 data-split><?= ch('faq_hero_title') ?></h1>
    <p class="reveal" style="--d:300ms"><?= e(c('faq_hero_sub')) ?></p></div>
</section>

<section class="soft">
  <div class="container">
    <div class="chips"><button class="chip active" data-cat="all">All</button><button class="chip" data-cat="lease">Leasing</button><button class="chip" data-cat="fin">Finance &amp; tax</button><button class="chip" data-cat="process">Process</button><button class="chip" data-cat="lender">Lenders</button></div>
    <div class="faq">
      <?php foreach ($faqs as $f): ?>
      <div class="faq-item" data-cat="<?= e($f['category']) ?>"><button class="faq-q"><?= e($f['question']) ?><span class="plus"></span></button><div class="faq-a"><div><p><?= $f['answer'] ?></p></div></div></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="dark" style="text-align:center"><div class="container"><h2 data-split><?= ch('faq_cta_title') ?></h2><p style="margin:18px 0 30px"><?= e(c('faq_cta_sub')) ?></p><a href="contact.php" class="btn btn-gold"><?= e(c('faq_cta_btn')) ?> <span class="arr">→</span></a></div></section>

<?php require __DIR__ . '/includes/footer.php'; ?>
