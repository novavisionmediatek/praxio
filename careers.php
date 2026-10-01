<?php
$pageFile = 'careers.php';
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = c('careers_seo_title');
$pageDesc = c('careers_seo_desc');
require __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/icons.php';
$why = rows("SELECT * FROM features WHERE placement='careers' AND is_active=1 ORDER BY sort_order, id");
$jobs = rows('SELECT * FROM job_openings WHERE is_active=1 ORDER BY sort_order, id');
$selected = (int)($_GET['job'] ?? 0);
$formType = 'apply';
?>

<section class="phero">
  <div class="bgimg imgwrap" data-parallax><img alt="" src="<?= img(c('careers_hero_img')) ?>"></div>
  <div class="container"><div class="crumb"><a href="index.php">Home</a> / Careers</div>
    <h1 data-split><?= ch('careers_hero_title') ?></h1>
    <p class="reveal" style="--d:300ms"><?= e(c('careers_hero_sub')) ?></p></div>
</section>

<section>
  <div class="container">
    <div class="head c"><span class="eyebrow"><?= e(c('careers_why_eyebrow')) ?></span><h2 data-split><?= ch('careers_why_title') ?></h2></div>
    <div class="grid g3"><?php feature_cards($why); ?></div>
  </div>
</section>

<section class="soft">
  <div class="container" style="max-width:900px">
    <div class="head c"><span class="eyebrow"><?= e(c('careers_roles_eyebrow')) ?></span><h2 data-split><?= ch('careers_roles_title') ?></h2></div>
    <?php foreach ($jobs as $j): ?>
    <a class="job reveal" href="careers.php?job=<?= (int)$j['id'] ?>#apply"><div><h3><?= e($j['title']) ?></h3><small><?= e($j['department']) ?> · <?= e($j['job_type']) ?></small></div><span class="go">Apply →</span></a>
    <?php endforeach; ?>
    <?php if (!$jobs): ?><p style="text-align:center">No open roles right now. Send us an open application below.</p><?php endif; ?>

    <form id="apply" class="plain reveal" data-form action="api/submit.php" method="post" enctype="multipart/form-data" style="margin-top:40px">
      <?php require __DIR__ . '/includes/form_fields.php'; ?>
      <h3 style="margin-bottom:6px"><?= e(c('careers_form_title')) ?></h3><p style="margin-bottom:22px"><?= e(c('careers_form_sub')) ?> CV as PDF, DOC or DOCX, up to <?= (int)(config('max_cv_bytes') / 1048576) ?> MB.</p>
      <div class="field"><select name="job_id"><option value="">Open application</option><?php foreach ($jobs as $j): ?><option value="<?= (int)$j['id'] ?>"<?= $j['id'] == $selected ? ' selected' : '' ?>><?= e($j['title']) ?></option><?php endforeach; ?></select><label>Role</label></div>
      <div class="row2"><div class="field"><input name="name" placeholder=" " required maxlength="120"><label>Full name</label></div><div class="field"><input type="email" name="email" placeholder=" " required maxlength="190"><label>Email</label></div></div>
      <div class="field"><input type="tel" name="phone" placeholder=" " maxlength="20"><label>Phone</label></div>
      <div class="field"><textarea name="message" placeholder=" " maxlength="3000"></textarea><label>How would you contribute?</label></div>
      <div class="upload" data-max="<?= (int)config('max_cv_bytes') ?>">
        <input type="file" id="cvFile" name="cv" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required>
        <label for="cvFile" class="upload-box">
          <span class="upload-ico"><svg viewBox="0 0 24 24"><path d="M12 16V4M7 9l5-5 5 5M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3"/></svg></span>
          <span class="upload-txt"><b>Upload your CV <i>*</i></b><small class="upload-name">Drag a file here or click to browse · PDF, DOC or DOCX · max <?= (int)(config('max_cv_bytes') / 1048576) ?> MB</small></span>
          <span class="upload-btn">Browse</span>
        </label>
        <small class="upload-err" role="alert"></small>
      </div>
      <button class="btn btn-gold" type="submit">Submit application <span class="arr">→</span></button>
      <div class="form-msg" role="status"></div>
    </form>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
