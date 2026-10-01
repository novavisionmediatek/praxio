<?php
/** Expects $pageTitle, $pageDesc, $pageFile (e.g. "faq.php"). Logo, top bar and menu are managed in admin → Header & footer. */
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/layout.php';

$email = setting('contact_email');
$phone = setting('contact_phone');
$phoneHref = preg_replace('/[^+\d]/', '', $phone);
$menu = menu_links('header');
$favicon = c('site_favicon');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="<?= e(csrf_token()) ?>">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($pageDesc) ?>">
<?php if ($favicon !== ''): ?><link rel="icon" href="<?= img($favicon) ?>"><?php endif; ?>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
<?= $extraHead ?? '' ?>
</head>
<body>
<div class="progress" id="progress"></div>
<?php if (!empty($_SESSION['flash'])): ?><div class="form-msg show" style="margin:0;border-radius:0;text-align:center"><?= e($_SESSION['flash']) ?></div><?php unset($_SESSION['flash']); endif; ?>
<?php if (c('site_topbar_on') === '1'): ?>
<div class="topbar"><div class="container"><span><b><?= e(c('site_topbar_lead')) ?></b> <?= e(c('site_topbar_rest')) ?></span><?php if (c('site_topbar_contact') === '1'): ?><span><a href="tel:<?= e($phoneHref) ?>"><?= e($phone) ?></a> &nbsp;·&nbsp; <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></span><?php endif; ?></div></div>
<?php endif; ?>
<header class="header" id="header"><div class="container nav">
  <a href="index.php" class="logo" aria-label="<?= e(c('site_brand_name') ?: 'Praxia') ?> home"><?= site_logo_html('header') ?></a>
  <nav class="nav-links" id="navLinks"><?php foreach ($menu as $m): ?><a href="<?= e($m['href']) ?>" class="<?= $m['active'] ? 'active' : '' ?>"<?= $m['new_tab'] ? ' target="_blank" rel="noopener"' : '' ?>><?= e($m['label']) ?></a><?php endforeach; ?><?php if (c('site_cta_on') === '1' && c('site_cta_label') !== ''): ?><a href="<?= e(safe_url(c('site_cta_url'))) ?>" class="btn btn-gold"><?= e(c('site_cta_label')) ?> <span class="arr">→</span></a><?php endif; ?></nav>
  <button class="burger" id="burger" aria-label="Menu"><span></span><span></span><span></span></button>
</div></header>
