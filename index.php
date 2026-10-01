<?php
$pageFile = 'index.php';
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/icons.php';
$pageTitle = c('home_seo_title');
$pageDesc = c('home_seo_desc');
require __DIR__ . '/includes/header.php';

$partners  = rows('SELECT name, logo_url FROM partners WHERE is_active=1 ORDER BY sort_order, id');
$logoH     = max(16, min(120, (int)c('home_trust_height') ?: 44));
$logoSpeed = max(10, min(200, (int)c('home_trust_speed') ?: 40));
$cats      = rows("SELECT * FROM asset_categories WHERE placement='home' AND is_active=1 ORDER BY sort_order, id");
$stats     = rows('SELECT * FROM stats WHERE is_active=1 ORDER BY sort_order, id');
$features  = rows("SELECT * FROM features WHERE placement='home' AND is_active=1 ORDER BY sort_order, id");
$steps     = rows("SELECT * FROM steps WHERE placement='home' AND is_active=1 ORDER BY sort_order, id");
$cases     = rows('SELECT * FROM case_studies WHERE is_active=1 AND home_order IS NOT NULL ORDER BY home_order, id LIMIT 3');
$investors = rows('SELECT * FROM investors WHERE is_active=1 ORDER BY sort_order, id');
$quotes    = rows('SELECT * FROM testimonials WHERE is_active=1 ORDER BY sort_order, id');
$faqs      = rows("SELECT question, COALESCE(NULLIF(home_answer, ''), answer) AS a FROM faqs WHERE is_active=1 AND show_on_home=1 ORDER BY sort_order, id");
?>

<!-- 1. HERO: dual pathway -->
<section class="hero">
  <div class="container">
    <span class="pill reveal"><i></i><?= e(c('home_hero_pill')) ?></span>
    <h1 data-split><?= ch('home_hero_title') ?> <span class="rotator" data-words="<?= e(json_encode(cl('home_hero_words'), JSON_UNESCAPED_UNICODE)) ?>"></span></h1>
    <p class="sub reveal" style="--d:300ms"><?= e(c('home_hero_sub')) ?></p>

    <div class="paths">
      <a href="product.php" class="path reveal left">
        <div class="imgwrap"><img loading="eager" alt="<?= e(c('home_path1_title')) ?>" src="<?= img(c('home_path1_img')) ?>"></div>
        <div class="path-in"><span class="tag"><?= e(c('home_path1_tag')) ?></span><h3><?= e(c('home_path1_title')) ?></h3><p><?= e(c('home_path1_text')) ?></p><span class="btn btn-gold"><?= e(c('home_path1_btn')) ?> <span class="arr">→</span></span></div>
      </a>
      <a href="contact.php" class="path b reveal right">
        <div class="imgwrap"><img loading="eager" alt="<?= e(c('home_path2_title')) ?>" src="<?= img(c('home_path2_img')) ?>"></div>
        <div class="path-in"><span class="tag"><?= e(c('home_path2_tag')) ?></span><h3><?= e(c('home_path2_title')) ?></h3><p><?= e(c('home_path2_text')) ?></p><span class="btn btn-ghost"><?= e(c('home_path2_btn')) ?> <span class="arr">→</span></span></div>
      </a>
    </div>

    <?php $formType = 'quote'; ?>
    <form class="quick reveal" data-form action="api/submit.php" method="post">
      <?php require __DIR__ . '/includes/form_fields.php'; ?>
      <h4><?= e(c('home_quote_title')) ?></h4>
      <div class="qf"><label>Asset type</label><select name="asset_type"><?php foreach (cl('home_quote_types') as $t): ?><option><?= e($t) ?></option><?php endforeach; ?></select></div>
      <div class="qf"><label>Approx. value (₹ Cr)</label><input name="asset_value" placeholder="e.g. 10" inputmode="decimal" maxlength="20"></div>
      <div class="qf"><label>Phone</label><input name="phone" type="tel" placeholder="+91" required maxlength="20"></div>
      <button class="btn btn-gold" type="submit"><?= e(c('home_quote_btn')) ?> <span class="arr">→</span></button>
      <div class="form-msg" role="status"></div>
    </form>

    <div class="trust">
      <small><?= e(c('home_trust_label')) ?></small>
      <div class="marquee<?= c('home_trust_gray') === '1' ? ' gray' : '' ?>"><div class="track" style="animation-duration:<?= $logoSpeed ?>s">
        <?php for ($i = 0; $i < 2; $i++) foreach ($partners as $p): ?><?php if (!empty($p['logo_url'])): ?><span class="plogo"><img src="<?= img($p['logo_url']) ?>" alt="<?= e($p['name']) ?>" loading="lazy" style="height:<?= $logoH ?>px"></span><?php else: ?><span><?= e($p['name']) ?></span><?php endif; ?><?php endforeach; ?>
      </div></div>
    </div>
  </div>
</section>

<!-- 2. VALUE PROP + PRODUCT CARDS -->
<section>
  <div class="container">
    <div class="head c"><span class="eyebrow"><?= e(c('home_finance_eyebrow')) ?></span><h2 data-split><?= ch('home_finance_title') ?></h2><p><?= e(c('home_finance_sub')) ?></p></div>
    <div class="grid g4">
      <?php foreach ($cats as $i => $c): ?>
      <a href="product.php" class="prod reveal"<?= $i ? ' style="--d:' . ($i * 100) . 'ms"' : '' ?>><div class="imgwrap"><span class="num-badge"><?= sprintf('%02d', $i + 1) ?></span><img loading="lazy" alt="<?= e($c['title']) ?>" src="<?= img($c['image_url']) ?>"></div><div class="prod-b"><h3><?= e($c['title']) ?></h3><p><?= e($c['description']) ?></p><span class="link">Learn more →</span></div></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 3. STATS -->
<div class="stats"><div class="container stat-grid">
  <?php foreach ($stats as $s): ?>
  <div class="stat"><b data-count="<?= (int)$s['stat_value'] ?>"<?= $s['prefix'] !== '' ? ' data-prefix="' . e($s['prefix']) . '"' : '' ?><?= $s['suffix'] !== '' ? ' data-suffix="' . e($s['suffix']) . '"' : '' ?>>0</b><span><?= e($s['label']) ?></span></div>
  <?php endforeach; ?>
</div></div>

<!-- 4. EDUCATIONAL: lease vs loan -->
<section>
  <div class="container split">
    <div class="frame reveal left">
      <div class="imgwrap" data-parallax><img loading="lazy" alt="<?= e(c('home_edu_title')) ?>" src="<?= img(c('home_edu_img')) ?>"></div>
      <div class="float f1"><div class="dot"><?= e(c('home_edu_f1_big')) ?></div><div><b><?= e(c('home_edu_f1_title')) ?></b><small><?= e(c('home_edu_f1_sub')) ?></small></div></div>
      <div class="float f2"><div class="dot"><?= e(c('home_edu_f2_big')) ?></div><div><b><?= e(c('home_edu_f2_title')) ?></b><small><?= e(c('home_edu_f2_sub')) ?></small></div></div>
    </div>
    <div class="reveal right">
      <span class="eyebrow"><?= e(c('home_edu_eyebrow')) ?></span>
      <h2 data-split><?= ch('home_edu_title') ?></h2>
      <p style="margin-top:20px;font-size:1.08rem"><?= e(c('home_edu_text')) ?></p>
      <ul class="list"><?php foreach (cl('home_edu_list') as $li): ?><li><?= e($li) ?></li><?php endforeach; ?></ul>
      <a href="product.php" class="btn btn-navy" style="margin-top:20px"><?= e(c('home_edu_btn')) ?> <span class="arr">→</span></a>
    </div>
  </div>
</section>

<!-- 5. FEATURES -->
<section class="soft">
  <div class="container">
    <div class="head c"><span class="eyebrow"><?= e(c('home_features_eyebrow')) ?></span><h2 data-split><?= ch('home_features_title') ?></h2></div>
    <div class="grid g3"><?php feature_cards($features); ?></div>
  </div>
</section>

<!-- 6. PROCESS -->
<section>
  <div class="container">
    <div class="head c"><span class="eyebrow"><?= e(c('home_process_eyebrow')) ?></span><h2 data-split><?= ch('home_process_title') ?></h2></div>
    <div class="steps">
      <?php foreach ($steps as $i => $s): ?><div class="step reveal"<?= $i ? ' style="--d:' . ($i * 120) . 'ms"' : '' ?>><div class="n"><?= $i + 1 ?></div><h3><?= e($s['title']) ?></h3><p><?= e($s['description']) ?></p></div><?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 7. CASE STUDIES -->
<section class="dark">
  <div class="container">
    <div class="head"><span class="eyebrow"><?= e(c('home_cases_eyebrow')) ?></span><h2 data-split><?= ch('home_cases_title') ?></h2></div>
    <div class="grid g3">
      <?php foreach ($cases as $i => $c): ?>
      <a href="case-study.php?slug=<?= e($c['slug']) ?>" class="cs reveal"<?= $i ? ' style="--d:' . ($i * 120) . 'ms"' : '' ?>><div class="imgwrap"><img loading="lazy" alt="<?= e($c['image_alt']) ?>" src="<?= img($c['image_url']) ?>"></div><div class="cs-in"><span class="tag"><?= e($c['home_tag'] ?: $c['tag']) ?></span><h3><?= e($c['home_title'] ?: $c['title']) ?></h3><div class="kpi"><?php foreach (pairs($c['home_kpis']) as [$v, $l]): ?><div><b><?= e($v) ?></b><small><?= e($l) ?></small></div><?php endforeach; ?></div></div></a>
      <?php endforeach; ?>
    </div>
    <div style="margin-top:44px;text-align:center"><a href="case-studies.php" class="btn btn-gold"><?= e(c('home_cases_btn')) ?> <span class="arr">→</span></a></div>
  </div>
</section>

<!-- 8. INVESTORS -->
<section class="soft">
  <div class="container">
    <div class="head c"><span class="eyebrow"><?= e(c('home_investors_eyebrow')) ?></span><h2 data-split><?= ch('home_investors_title') ?></h2></div>
    <div class="inv">
      <?php foreach ($investors as $i => $v): ?><div class="reveal"<?= $i ? ' style="--d:' . ($i * 120) . 'ms"' : '' ?>><b><?= e($v['name']) ?></b><small><?= e($v['description']) ?></small></div><?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 9. TESTIMONIALS -->
<section>
  <div class="container">
    <div class="head" style="display:flex;justify-content:space-between;align-items:flex-end;max-width:none;flex-wrap:wrap;gap:20px">
      <div><span class="eyebrow"><?= e(c('home_voices_eyebrow')) ?></span><h2 data-split><?= ch('home_voices_title') ?></h2></div>
      <div class="car-nav"><button data-car="prev" aria-label="Previous">←</button><button data-car="next" aria-label="Next">→</button></div>
    </div>
    <div class="tcar">
      <?php foreach ($quotes as $q): ?>
      <div class="quote"><p><?= e($q['quote']) ?></p><div class="who"><div class="avatar"><?= e($q['initials']) ?></div><div><b><?= e($q['author']) ?></b><small><?= e($q['designation']) ?></small></div></div></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 10. FAQ -->
<section class="soft">
  <div class="container">
    <div class="head c"><span class="eyebrow"><?= e(c('home_faq_eyebrow')) ?></span><h2 data-split><?= ch('home_faq_title') ?></h2></div>
    <div class="faq">
      <?php foreach ($faqs as $i => $f): ?>
      <div class="faq-item<?= $i === 0 ? ' open' : '' ?>"><button class="faq-q"><?= e($f['question']) ?><span class="plus"></span></button><div class="faq-a"><div><p><?= $f['a'] ?></p></div></div></div>
      <?php endforeach; ?>
    </div>
    <p style="text-align:center;margin-top:30px"><a href="faq.php" class="link"><?= e(c('home_faq_link')) ?></a></p>
  </div>
</section>

<!-- 11. APPLY -->
<section class="apply dark">
  <div class="bgimg imgwrap" data-parallax><img loading="lazy" alt="" src="<?= img(c('home_apply_img')) ?>"></div>
  <div class="container apply-grid">
    <div class="reveal left"><span class="eyebrow"><?= e(c('home_apply_eyebrow')) ?></span><h2 data-split><?= ch('home_apply_title') ?></h2><p style="margin-top:20px;font-size:1.1rem"><?= e(c('home_apply_text')) ?></p>
      <ul class="list"><?php foreach (cl('home_apply_list') as $li): ?><li><?= e($li) ?></li><?php endforeach; ?></ul></div>
    <?php $formType = 'proposal'; ?>
    <form class="card-f reveal right" data-form action="api/submit.php" method="post">
      <?php require __DIR__ . '/includes/form_fields.php'; ?>
      <h3><?= e(c('home_apply_form_title')) ?></h3><p style="margin-bottom:22px"><?= e(c('home_apply_form_sub')) ?></p>
      <div class="row2"><div class="field"><input name="name" placeholder=" " required maxlength="120"><label>Full name</label></div><div class="field"><input name="company" placeholder=" " required maxlength="160"><label>Company</label></div></div>
      <div class="row2"><div class="field"><input type="email" name="email" placeholder=" " required maxlength="190"><label>Work email</label></div><div class="field"><input type="tel" name="phone" placeholder=" " required maxlength="20"><label>Phone</label></div></div>
      <div class="field"><textarea name="message" placeholder=" " maxlength="3000"></textarea><label>Asset, value and tenure</label></div>
      <button class="btn btn-gold" style="width:100%" type="submit"><?= e(c('home_apply_btn')) ?> <span class="arr">→</span></button>
      <div class="form-msg" role="status"></div>
    </form>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
