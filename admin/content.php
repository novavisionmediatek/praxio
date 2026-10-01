<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_once __DIR__ . '/../includes/media.php';
require_admin();

// Every heading, paragraph, button label, SEO tag and page image. Fields come from includes/content_defs.php.
$defs = require __DIR__ . '/../includes/content_defs.php';
$page = $_GET['p'] ?? 'home';
if (!isset($defs[$page])) $page = 'home';
$fields = $defs[$page]['fields'];
$section = $defs[$page]['section'] ?? 'pages';
$siteTitles = ['header' => ['Logo & top bar', 'Logo, favicon, top bar and the menu button'], 'footer' => ['Footer', 'About text, column titles, copyright and social links'], 'popup' => ['Enquiry popup', 'The popup that appears when someone opens the website']];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_csrf();
    $keys = array_keys($fields);

    if (($_POST['action'] ?? '') === 'reset') {
        $in = implode(',', array_fill(0, count($keys), '?'));
        db()->prepare("DELETE FROM settings WHERE setting_key IN ($in)")->execute($keys);
        flash('This page was reset to its original text and images.');
        header("Location: content.php?p=$page"); exit;
    }

    $save = db()->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    $drop = db()->prepare('DELETE FROM settings WHERE setting_key = ?');
    $errors = [];
    foreach ($fields as $key => [$type, $label]) {
        if ($type === 'image') {
            try {
                $v = save_image_upload($key . '__file') ?? trim((string)($_POST[$key] ?? ''));
            } catch (RuntimeException $ex) { $errors[] = "$label: " . $ex->getMessage(); continue; }
            if ($v === '') $drop->execute([$key]);                                  // empty = back to the default image
            elseif (!preg_match('~^(https?://|media/)~i', $v)) $errors[] = "$label: use an uploaded file or a full http(s) URL.";
            else $save->execute([$key, $v]);
        } else {
            $v = str_replace("\r\n", "\n", trim((string)($_POST[$key] ?? '')));
            $save->execute([$key, mb_substr($v, 0, 5000)]);
        }
    }
    if ($errors) flash('Saved, but: ' . implode(' | ', $errors), 'error'); else flash('Saved. The live page is updated.');
    header("Location: content.php?p=$page"); exit;
}

/** Splits the flat field list into titled groups using the "Group: field" label convention. */
$groups = [];
foreach ($fields as $key => [$type, $label, $default]) {
    if (str_contains($label, '(SEO)')) { $g = 'Search engines (SEO)'; $short = str_replace(' (SEO)', '', $label); }
    elseif (str_starts_with($label, 'Banner')) { $g = 'Page banner'; $short = trim(substr($label, 6)) ?: 'Image'; }
    elseif (str_contains($label, ':')) { [$g, $short] = array_map('trim', explode(':', $label, 2)); }
    else { $g = 'General'; $short = $label; }
    $groups[$g][$key] = [$type, ucfirst($short), $default];
}

$all = all_settings();
$tabs = '';
foreach ($defs as $k => $d) if (($d['section'] ?? 'pages') === $section && $section === 'pages') $tabs .= '<a href="?p=' . e($k) . '" class="' . ($k === $page ? 'on' : '') . '">' . e($d['label']) . '</a>';
$view = ['header' => 'index.php', 'footer' => 'index.php', 'popup' => 'index.php', 'home' => 'index.php', 'product' => 'product.php', 'about' => 'about.php', 'cases' => 'case-studies.php', 'faq' => 'faq.php', 'careers' => 'careers.php', 'contact' => 'contact.php'][$page] ?? null;

admin_head($siteTitles[$page][0] ?? 'Pages & text', $siteTitles[$page][1] ?? 'Edit the wording and pictures of each page',
    ($view ?? '../index.php') ? '<a class="btn sec" href="../' . e($view ?? 'index.php') . '" target="_blank">View website ↗</a>' : '');
?>
<?php if ($tabs): ?><div class="tabs"><?= $tabs ?></div><?php endif; ?>
<?php if ($section === 'pages'): ?><p class="hint">Tip: put *asterisks* around words in a heading to show them in the gold highlight. Lists such as FAQs, team and cards are in the left menu.</p><?php endif; ?>

<form method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="action" value="save">
<?php $first = true; foreach ($groups as $gname => $gfields): ?>
<details class="sec"<?= $first ? ' open' : '' ?>><summary><?= e($gname) ?> <span class="muted" style="font-weight:400">· <?= count($gfields) ?> field<?= count($gfields) === 1 ? '' : 's' ?></span></summary>
  <div class="grid2" style="padding-top:16px">
  <?php foreach ($gfields as $key => [$type, $label, $default]): $val = array_key_exists($key, $all) ? $all[$key] : $default; ?>
    <div class="fld <?= $type === 'text' && mb_strlen($val) < 60 ? '' : 'full' ?>"><label><?= e($label) ?></label>
    <?php if ($type === 'image'): image_field($key, $val, 'Clear the URL to go back to the default image.');
          elseif ($type === 'toggle'): ?><select name="<?= e($key) ?>"><option value="1"<?= $val === '1' ? ' selected' : '' ?>>Yes</option><option value="0"<?= $val === '1' ? '' : ' selected' ?>>No</option></select>
    <?php elseif ($type === 'text'): ?><input name="<?= e($key) ?>" value="<?= e($val) ?>">
    <?php else: ?><textarea name="<?= e($key) ?>"<?= $type === 'lines' ? ' placeholder="One item per line"' : '' ?>><?= e($val) ?></textarea><?= $type === 'lines' ? '<small>One item per line.</small>' : '' ?>
    <?php endif; ?></div>
  <?php endforeach; ?>
  </div>
</details>
<?php $first = false; endforeach; ?>
<div class="savebar"><button class="btn gold">Save changes</button></div>
</form>

<form method="post" onsubmit="return confirm('Throw away all your edits on this page and go back to the original text and images?')" style="margin-top:8px">
<?= csrf_field() ?><input type="hidden" name="action" value="reset"><button class="btn del sm">Reset this page to original</button></form>
<?php image_picker_assets(); admin_foot(); ?>
