<?php
$pageFile = 'about.php';
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = c('about_seo_title');
$pageDesc = c('about_seo_desc');
require __DIR__ . '/includes/header.php';
$founders  = rows("SELECT * FROM team_members WHERE tier='founder' AND is_active=1 ORDER BY sort_order, id");
$leaders   = rows("SELECT * FROM team_members WHERE tier='leader' AND is_active=1 ORDER BY sort_order, id");
$investors = rows('SELECT * FROM investors WHERE is_active=1 ORDER BY sort_order, id');
$teamSize  = (int)setting('team_size', '17');

function person_card(array $p, int $i, bool $small): void { ?>
      <div class="person reveal"<?= $i ? ' style="--d:' . ($i * 100) . 'ms"' : '' ?>><div class="pic"<?= $small ? ' style="aspect-ratio:2/1"' : '' ?>><?php if (!empty($p['photo_url'])): ?><img src="<?= img($p['photo_url']) ?>" alt="<?= e($p['name']) ?>" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:1"><?php else: ?><?= e($p['initials']) ?><?php endif; ?></div><div class="person-b"><h3><?= e($p['name']) ?></h3><div class="role"><?= e($p['role']) ?></div><ul><?php foreach (lines($p['bio']) as $b): ?><li><?= e($b) ?></li><?php endforeach; ?></ul></div></div>
<?php }
?>

<section class="phero">
  <div class="bgimg imgwrap" data-parallax><img alt="" src="<?= img(c('about_hero_img')) ?>"></div>
  <div class="container"><div class="crumb"><a href="index.php">Home</a> / About us</div>
    <h1 data-split><?= ch('about_hero_title') ?></h1>
    <p class="reveal" style="--d:300ms"><?= e(c('about_hero_sub')) ?></p></div>
</section>

<section>
  <div class="container split">
    <div class="frame reveal left"><div class="imgwrap" data-parallax><img loading="lazy" alt="<?= e(c('about_story_title')) ?>" src="<?= img(c('about_story_img')) ?>"></div>
      <div class="float f1"><div class="dot"><?= e(c('about_story_f_big')) ?></div><div><b><?= e(c('about_story_f_title')) ?></b><small><?= e(c('about_story_f_sub')) ?></small></div></div></div>
    <div class="reveal right"><span class="eyebrow"><?= e(c('about_story_eyebrow')) ?></span><h2 data-split><?= ch('about_story_title') ?></h2>
      <p style="margin-top:20px;font-size:1.08rem"><?= e(c('about_story_p1')) ?></p>
      <p style="margin-top:14px"><?= e(c('about_story_p2')) ?></p>
      <div class="metrics"><div><b data-count="<?= $teamSize ?>">0</b><small>team members</small></div><div><b data-count="<?= (int)c('about_years_count') ?>" data-suffix="+">0</b><small>years combined</small></div><div><b data-count="<?= (int)c('about_customers_count') ?>">0</b><small>customers</small></div></div></div>
  </div>
</section>

<section class="soft" id="team">
  <div class="container">
    <div class="head c"><span class="eyebrow"><?= e(c('about_founders_eyebrow')) ?></span><h2 data-split><?= ch('about_founders_title') ?></h2></div>
    <div class="grid g4">
      <?php foreach ($founders as $i => $p) person_card($p, $i, false); ?>
    </div>
    <div class="head c" style="margin-top:100px"><span class="eyebrow"><?= e(c('about_leaders_eyebrow')) ?></span><h2 data-split><?= ch('about_leaders_title') ?></h2></div>
    <div class="grid g4">
      <?php foreach ($leaders as $i => $p) person_card($p, $i, true); ?>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="head c"><span class="eyebrow"><?= e(c('about_backed_eyebrow')) ?></span><h2 data-split><?= ch('about_backed_title') ?></h2><p><?= e(c('about_backed_sub')) ?></p></div>
    <div class="inv">
      <?php foreach ($investors as $i => $v): ?><div class="reveal"<?= $i ? ' style="--d:' . ($i * 120) . 'ms"' : '' ?>><b><?= e($v['name']) ?></b><small><?= e($v['description']) ?></small></div><?php endforeach; ?>
    </div>
  </div>
</section>

<section class="dark" style="text-align:center"><div class="container"><h2 data-split><?= ch('about_cta_title') ?></h2><p style="margin:18px 0 30px"><?= e(c('about_cta_sub')) ?></p><a href="contact.php" class="btn btn-gold"><?= e(c('about_cta_btn1')) ?></a> <a href="careers.php" class="btn btn-ghost"><?= e(c('about_cta_btn2')) ?></a></div></section>

<?php require __DIR__ . '/includes/footer.php'; ?>
