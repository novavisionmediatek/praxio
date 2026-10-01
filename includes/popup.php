<?php
/** Enquiry popup shown a few seconds after a page opens. Included by footer.php. Texts and timing come from admin → Pages & text → Site-wide. */
$formType = 'popup';
$popDelay = max(0, min(60, (int)c('site_popup_delay')));
$popDays  = max(0, min(365, (int)c('site_popup_days')));
?>
<div class="pop" id="leadPop" role="dialog" aria-modal="true" aria-labelledby="popTitle" data-delay="<?= $popDelay ?>" data-days="<?= $popDays ?>" hidden>
  <div class="pop-card">
    <button class="pop-x" type="button" data-pop-close aria-label="Close">&times;</button>
    <aside class="pop-side">
      <span class="eyebrow"><?= e(c('site_popup_eyebrow')) ?></span>
      <h2 id="popTitle"><?= ch('site_popup_title') ?></h2>
      <p><?= e(c('site_popup_text')) ?></p>
      <ul><?php foreach (cl('site_popup_list') as $li): ?><li><?= e($li) ?></li><?php endforeach; ?></ul>
      <div class="pop-contact"><a href="tel:<?= e($phoneHref) ?>"><?= e($phone) ?></a><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></div>
    </aside>
    <form class="pop-form" data-form action="api/submit.php" method="post">
      <?php require __DIR__ . '/form_fields.php'; ?>
      <div class="row2"><div class="field"><input name="name" placeholder=" " required maxlength="120" autocomplete="name"><label>Full name *</label></div><div class="field"><input name="company" placeholder=" " maxlength="160" autocomplete="organization"><label>Company</label></div></div>
      <div class="row2"><div class="field"><input type="email" name="email" placeholder=" " required maxlength="190" autocomplete="email"><label>Work email *</label></div><div class="field"><input type="tel" name="phone" placeholder=" " required maxlength="20" autocomplete="tel"><label>Phone *</label></div></div>
      <div class="row2"><div class="field"><select name="asset_type"><?php foreach (cl('home_quote_types') as $t): ?><option><?= e($t) ?></option><?php endforeach; ?></select><label>Asset type</label></div><div class="field"><input name="asset_value" placeholder=" " inputmode="decimal" maxlength="20"><label>Approx. value (₹ Cr)</label></div></div>
      <div class="field"><textarea name="message" placeholder=" " maxlength="3000" style="min-height:84px"></textarea><label>Tell us about your requirement</label></div>
      <button class="btn btn-gold" type="submit" style="width:100%;justify-content:center"><?= e(c('site_popup_btn')) ?> <span class="arr">→</span></button>
      <div class="form-msg" role="status"></div>
      <p class="pop-note"><?= e(c('site_popup_note')) ?></p>
    </form>
  </div>
</div>
