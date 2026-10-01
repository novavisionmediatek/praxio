<?php
$pageFile = 'contact.php';
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = c('contact_seo_title');
$pageDesc = c('contact_seo_desc');
require __DIR__ . '/includes/header.php';
$formType = 'contact';
?>

<section class="phero">
  <div class="bgimg imgwrap" data-parallax><img alt="" src="<?= img(c('contact_hero_img')) ?>"></div>
  <div class="container"><div class="crumb"><a href="index.php">Home</a> / Contact</div>
    <h1 data-split><?= ch('contact_hero_title') ?></h1>
    <p class="reveal" style="--d:300ms"><?= e(c('contact_hero_sub')) ?></p></div>
</section>

<section class="soft">
  <div class="container contact-grid">
    <div class="reveal left">
      <h2 data-split><?= e(c('contact_reach_title')) ?></h2>
      <p style="margin:18px 0 38px"><?= e(c('contact_reach_text')) ?></p>
      <div class="info"><div class="icon"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></div><div><b>Email</b><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></div></div>
      <div class="info"><div class="icon"><svg viewBox="0 0 24 24"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/></svg></div><div><b>Phone</b><a href="tel:<?= e($phoneHref) ?>"><?= e($phone) ?></a></div></div>
      <div class="info"><div class="icon"><svg viewBox="0 0 24 24"><path d="M4 21V7l8-4 8 4v14M9 21v-6h6v6"/></svg></div><div><b>Company</b><span><?= e(setting('company_legal_name')) ?></span></div></div>
    </div>
    <form class="plain reveal right" data-form action="api/submit.php" method="post">
      <?php require __DIR__ . '/includes/form_fields.php'; ?>
      <div class="row2"><div class="field"><input name="name" placeholder=" " required maxlength="120"><label>Full name</label></div><div class="field"><input name="company" placeholder=" " required maxlength="160"><label>Company</label></div></div>
      <div class="row2"><div class="field"><input type="email" name="email" placeholder=" " required maxlength="190"><label>Work email</label></div><div class="field"><input type="tel" name="phone" placeholder=" " maxlength="20"><label>Phone</label></div></div>
      <div class="field"><select name="audience"><?php foreach (cl('contact_audiences') as $o): ?><option><?= e($o) ?></option><?php endforeach; ?></select><label>I am a</label></div>
      <div class="field"><textarea name="message" placeholder=" " maxlength="3000"></textarea><label>Asset type, value and tenure</label></div>
      <button class="btn btn-gold" type="submit"><?= e(c('contact_btn')) ?> <span class="arr">→</span></button>
      <div class="form-msg" role="status"></div>
    </form>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
