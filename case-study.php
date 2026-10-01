<?php
$pageFile = 'case-studies.php';   // keeps "Case Studies" highlighted in the menu
require_once __DIR__ . '/includes/bootstrap.php';

$cs = row('SELECT * FROM case_studies WHERE slug = ? AND is_active = 1', [(string)($_GET['slug'] ?? '')]);
if (!$cs) {
    http_response_code(404);
    $pageTitle = 'Case study not found – Praxia';
    $pageDesc = '';
    require __DIR__ . '/includes/header.php';
    echo '<section class="phero"><div class="container"><h1>Case study not found</h1><p><a class="btn btn-gold" href="case-studies.php">See all case studies</a></p></div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $cs['seo_title'] ?: $cs['title'] . ' – Praxia';
$pageDesc = $cs['seo_desc'] ?: mb_strimwidth((string)($cs['summary'] ?: $cs['outcome'] ?: $cs['challenge']), 0, 160, '…');
require_once __DIR__ . '/includes/fonts.php';
$description = clean_rich_html($cs['body']);
$gf = google_fonts_url(fonts_used_in($description));
$extraHead = $gf ? '<link rel="stylesheet" href="' . e($gf) . '">' : '';
require __DIR__ . '/includes/header.php';

$all     = rows('SELECT id, slug, title FROM case_studies WHERE is_active = 1 ORDER BY sort_order, id');
$pos     = array_search((int)$cs['id'], array_map('intval', array_column($all, 'id')), true);
$prev    = $pos > 0 ? $all[$pos - 1] : null;
$next    = $pos !== false && $pos < count($all) - 1 ? $all[$pos + 1] : null;
$gallery = rows('SELECT * FROM case_study_images WHERE case_study_id = ? ORDER BY sort_order, id', [$cs['id']]);
$more    = rows('SELECT * FROM case_studies WHERE is_active = 1 AND id <> ? ORDER BY sort_order, id LIMIT 3', [$cs['id']]);
$metrics = pairs($cs['metrics']);
$results = lines($cs['results']);
$facts   = array_filter([
    'Client' => $cs['client_name'], 'Industry' => $cs['tag'], 'Location' => $cs['location'],
    'Service' => $cs['service'], 'Duration' => $cs['duration'],
], 'strlen');
$sections = array_filter([['Challenge', $cs['challenge']], ['Solution', $cs['solution']], ['Outcome', $cs['outcome']]], fn($s) => trim((string)$s[1]) !== '');
?>

<section class="phero" style="padding-bottom:<?= $metrics ? '150px' : '110px' ?>">
  <div class="bgimg imgwrap" data-parallax><img alt="<?= e($cs['image_alt']) ?>" src="<?= img($cs['image_url']) ?>"></div>
  <div class="container"><div class="crumb"><a href="index.php">Home</a> / <a href="case-studies.php">Case studies</a> / <?= e($cs['client_name'] ?: $cs['tag']) ?></div>
    <h1 data-split><?= e($cs['title']) ?></h1>
    <p class="reveal" style="--d:300ms"><?= e($cs['tag']) ?></p></div>
</section>

<section style="padding-top:0">
  <div class="container">
    <?php if ($metrics): ?>
    <div class="csd-bar reveal"><?php foreach ($metrics as [$v, $l]): ?><div><b><?= e($v) ?></b><small><?= e($l) ?></small></div><?php endforeach; ?></div>
    <?php endif; ?>

    <div class="csd"<?= $metrics ? '' : ' style="margin-top:60px"' ?>>
      <article>
        <?php if (trim((string)$cs['summary']) !== ''): ?><div class="csd-lead reveal"><?= nl2br(e($cs['summary'])) ?></div><div style="height:46px"></div><?php endif; ?>

        <?php foreach ($sections as $i => [$label, $text]): ?>
        <div class="csd-sec reveal"><h2><i><?= $i + 1 ?></i><?= e($label) ?></h2><?= paras($text) ?></div>
        <?php endforeach; ?>

        <?php if ($description !== ''): ?>
        <div class="csd-prose csd-sec reveal"><?= $description ?></div>
        <?php endif; ?>

        <?php if ($results): ?>
        <div class="csd-sec reveal"><h2><i>★</i>Key results</h2><ul class="csd-results"><?php foreach ($results as $r): ?><li><?= e($r) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <?php if ($gallery): ?>
        <div class="csd-sec reveal"><h2><i>▣</i>Gallery</h2>
          <div class="csd-gallery"><?php foreach ($gallery as $g): ?><figure><img loading="lazy" src="<?= img($g['image_url']) ?>" alt="<?= e($g['caption'] ?: $cs['title']) ?>"><?php if ($g['caption'] !== ''): ?><figcaption><?= e($g['caption']) ?></figcaption><?php endif; ?></figure><?php endforeach; ?></div></div>
        <?php endif; ?>

        <?php if (trim((string)$cs['quote_text']) !== ''): ?>
        <blockquote class="csd-quote reveal" style="margin-left:0;margin-right:0"><p>“<?= e($cs['quote_text']) ?>”</p>
          <?php if ($cs['quote_author']): ?><div class="by"><?= e($cs['quote_author']) ?><?php if ($cs['quote_role']): ?><small><?= e($cs['quote_role']) ?></small><?php endif; ?></div><?php endif; ?></blockquote>
        <?php endif; ?>

        <div class="csd-nav">
          <?php if ($prev): ?><a href="case-study.php?slug=<?= e($prev['slug']) ?>">← <?= e(mb_strimwidth($prev['title'], 0, 48, '…')) ?></a><?php else: ?><span></span><?php endif; ?>
          <?php if ($next): ?><a href="case-study.php?slug=<?= e($next['slug']) ?>"><?= e(mb_strimwidth($next['title'], 0, 48, '…')) ?> →</a><?php endif; ?>
        </div>
      </article>

      <aside class="csd-side">
        <?php if ($facts): ?>
        <div class="csd-card reveal"><h3>At a glance</h3><dl><?php foreach ($facts as $k => $v): ?><dt><?= e($k) ?></dt><dd><?= e($v) ?></dd><?php endforeach; ?></dl></div>
        <?php endif; ?>
        <div class="csd-card csd-cta reveal"><h3>Have a similar CapEx challenge?</h3><p>Tell us what you need to fund and we'll structure a lease for it.</p><a href="contact.php" class="btn btn-gold">Talk to us <span class="arr">→</span></a></div>
        <a href="case-studies.php" class="link">← All case studies</a>
      </aside>
    </div>
  </div>
</section>

<?php if ($more): ?>
<section class="dark">
  <div class="container">
    <div class="head"><span class="eyebrow">More case studies</span><h2 data-split>Keep reading</h2></div>
    <div class="grid g3">
      <?php foreach ($more as $i => $c): ?>
      <a href="case-study.php?slug=<?= e($c['slug']) ?>" class="cs reveal"<?= $i ? ' style="--d:' . ($i * 120) . 'ms"' : '' ?>><div class="imgwrap"><img loading="lazy" alt="<?= e($c['image_alt']) ?>" src="<?= img($c['image_url']) ?>"></div><div class="cs-in"><span class="tag"><?= e($c['home_tag'] ?: $c['tag']) ?></span><h3><?= e($c['home_title'] ?: $c['title']) ?></h3></div></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
