<?php
/** Footer. Logo, text, link columns and social links are managed in admin → Header & footer. */
$footCompany = menu_links('footer_company');
$footExplore = menu_links('footer_explore');
$socials = social_links();
$address = trim(c('site_footer_address'));
$copy = str_replace(['{year}', '{company}'], [date('Y'), setting('company_legal_name')], c('site_footer_copy'));
?><footer class="footer"><div class="container">
  <div class="foot-grid">
    <div><a href="index.php" class="logo"><?= site_logo_html('footer') ?></a><p><?= e(c('site_footer_blurb')) ?></p>
      <?php if ($socials): ?><div class="social"><?php foreach ($socials as $s): ?><a href="<?= e($s['url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($s['label']) ?>" title="<?= e($s['label']) ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?= $s['svg'] ?></svg></a><?php endforeach; ?></div><?php endif; ?></div>
    <div><h4><?= e(c('site_foot_col1')) ?></h4><?php foreach ($footCompany as $m): ?><a href="<?= e($m['href']) ?>"<?= $m['new_tab'] ? ' target="_blank" rel="noopener"' : '' ?>><?= e($m['label']) ?></a><?php endforeach; ?></div>
    <div><h4><?= e(c('site_foot_col2')) ?></h4><?php foreach ($footExplore as $m): ?><a href="<?= e($m['href']) ?>"<?= $m['new_tab'] ? ' target="_blank" rel="noopener"' : '' ?>><?= e($m['label']) ?></a><?php endforeach; ?></div>
    <div><h4><?= e(c('site_foot_col3')) ?></h4><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a><a href="tel:<?= e($phoneHref) ?>"><?= e($phone) ?></a>
      <?php if ($address !== ''): ?><p style="margin-top:12px"><?= nl2br(e($address)) ?></p><?php endif; ?>
      <p style="margin-top:12px"><?= e(setting('company_legal_name')) ?></p></div>
  </div>
  <div class="foot-bottom"><span><?= e($copy) ?></span><span><?= e(c('site_domain')) ?></span></div>
</div></footer>
<?php if (c('site_popup_on') === '1' && ($pageFile ?? '') !== 'contact.php') require __DIR__ . '/popup.php'; ?>
<script src="js/main.js"></script>
</body>
</html>
