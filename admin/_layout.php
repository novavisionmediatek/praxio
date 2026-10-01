<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');

/**
 * Content lists editable from admin/manage.php.
 * field type: text | textarea | number | select | bool | image   (display order is handled by the up/down arrows, not a field)
 * 'group' = column that splits the list into independent ordered groups (e.g. which page the card appears on).
 */
const MANAGED = [
    'faqs' => ['label' => 'FAQs', 'one' => 'FAQ', 'nav' => 'website', 'icon' => 'help', 'title' => 'question', 'group' => 'category',
        'desc' => 'Questions shown on the FAQ page. Tick "Show on home page" to also feature one on the home page.', 'fields' => [
        'category' => ['select', 'Category', ['lease' => 'Leasing', 'fin' => 'Finance & tax', 'process' => 'Process', 'lender' => 'Lenders']],
        'question' => ['text', 'Question'],
        'answer' => ['textarea', 'Answer (basic HTML such as links is allowed)'],
        'show_on_home' => ['bool', 'Show on home page'],
        'home_answer' => ['textarea', 'Shorter answer for the home page (optional)'],
        'is_active' => ['bool', 'Visible on website'],
    ]],
    'team_members' => ['label' => 'Team', 'one' => 'team member', 'nav' => 'website', 'icon' => 'users', 'title' => 'name', 'group' => 'tier',
        'desc' => 'Founders and leadership shown on the About page.', 'fields' => [
        'tier' => ['select', 'Group', ['founder' => 'Founder', 'leader' => 'Leadership']], 'name' => ['text', 'Name'], 'initials' => ['text', 'Initials (max 4)'],
        'role' => ['text', 'Role'], 'photo_url' => ['image', 'Photo (optional, initials are shown if empty)', true],
        'bio' => ['textarea', 'Background, one bullet per line'], 'is_active' => ['bool', 'Visible on website'],
    ]],
    'testimonials' => ['label' => 'Testimonials', 'one' => 'testimonial', 'nav' => 'website', 'icon' => 'quote', 'title' => 'author', 'group' => null,
        'desc' => 'Customer quotes in the home page carousel.', 'fields' => [
        'quote' => ['textarea', 'Quote'], 'author' => ['text', 'Name'], 'designation' => ['text', 'Designation and company'], 'initials' => ['text', 'Initials (max 4)'],
        'is_active' => ['bool', 'Visible on website'],
    ]],
    'investors' => ['label' => 'Investors', 'one' => 'investor', 'nav' => 'website', 'icon' => 'briefcase', 'title' => 'name', 'group' => null,
        'desc' => 'Backers shown on the home and About pages.', 'fields' => [
        'name' => ['text', 'Name'], 'description' => ['text', 'Description'], 'is_active' => ['bool', 'Visible on website'],
    ]],
    'partners' => ['label' => 'Partner logos', 'one' => 'partner', 'nav' => 'website', 'icon' => 'bank', 'title' => 'name', 'group' => null,
        'desc' => 'Banks and NBFCs in the scrolling strip on the home page. Add a logo for each, or leave it empty to show the name as text.', 'fields' => [
        'name' => ['text', 'Name (also used as the logo description)'], 'logo_url' => ['image', 'Logo (PNG or WebP with a transparent background works best)', true], 'is_active' => ['bool', 'Visible on website'],
    ]],
    'job_openings' => ['label' => 'Job openings', 'one' => 'job opening', 'nav' => 'website', 'icon' => 'job', 'title' => 'title', 'group' => null,
        'desc' => 'Roles listed on the Careers page.', 'fields' => [
        'title' => ['text', 'Job title'], 'department' => ['text', 'Department'], 'job_type' => ['text', 'Type (e.g. Full-time)'], 'is_active' => ['bool', 'Visible on website'],
    ]],
    'menu_items' => ['label' => 'Menu links', 'one' => 'link', 'nav' => 'site', 'icon' => 'menu', 'title' => 'label', 'group' => 'location',
        'desc' => 'Links in the top menu and in the two link columns of the footer. Order them with the arrows.', 'fields' => [
        'location' => ['select', 'Where it appears', ['header' => 'Top menu', 'footer_company' => 'Footer: first column', 'footer_explore' => 'Footer: second column']],
        'label' => ['text', 'Text shown'], 'url' => ['url', 'Link (e.g. about.php, https://example.com, #team, mailto:you@x.com)'],
        'new_tab' => ['bool', 'Open in a new tab'], 'is_active' => ['bool', 'Visible on website'],
    ]],
    'stats' => ['label' => 'Home page numbers', 'one' => 'number', 'nav' => 'blocks', 'icon' => 'chart', 'title' => 'label', 'group' => null,
        'desc' => 'The animated counters strip on the home page.', 'fields' => [
        'label' => ['text', 'Label'], 'stat_value' => ['number', 'Number'], 'prefix' => ['text', 'Before the number (e.g. ₹)'], 'suffix' => ['text', 'After the number (e.g. Cr, +)'],
        'is_active' => ['bool', 'Visible on website'],
    ]],
    'asset_categories' => ['label' => 'Asset categories', 'one' => 'asset category', 'nav' => 'blocks', 'icon' => 'box', 'title' => 'title', 'group' => 'placement',
        'desc' => 'Picture cards for the assets Praxia finances.', 'fields' => [
        'placement' => ['select', 'Shown on', ['home' => 'Home page', 'product' => 'Product page']], 'title' => ['text', 'Title'], 'description' => ['text', 'Description'],
        'image_url' => ['image', 'Image'], 'is_active' => ['bool', 'Visible on website'],
    ]],
    'features' => ['label' => 'Feature cards', 'one' => 'feature card', 'nav' => 'blocks', 'icon' => 'grid', 'title' => 'title', 'group' => 'placement',
        'desc' => 'Icon cards on the Home, Product (diligence) and Careers pages.', 'fields' => [
        'placement' => ['select', 'Shown on', ['home' => 'Home: Why Praxia', 'diligence' => 'Product: Our diligence', 'careers' => "Careers: Where you'll grow"]],
        'icon' => ['select', 'Icon', 'ICONS'], 'title' => ['text', 'Title'], 'description' => ['textarea', 'Description'], 'is_active' => ['bool', 'Visible on website'],
    ]],
    'steps' => ['label' => 'Process steps', 'one' => 'step', 'nav' => 'blocks', 'icon' => 'steps', 'title' => 'title', 'group' => 'placement',
        'desc' => 'The numbered "how it works" steps.', 'fields' => [
        'placement' => ['select', 'Shown on', ['home' => 'Home page', 'product' => 'Product page']], 'title' => ['text', 'Title'], 'description' => ['text', 'Description'],
        'is_active' => ['bool', 'Visible on website'],
    ]],
    'compare_rows' => ['label' => 'Loan vs lease table', 'one' => 'row', 'nav' => 'blocks', 'icon' => 'table', 'title' => 'aspect', 'group' => null,
        'desc' => 'Comparison table on the Product page.', 'fields' => [
        'aspect' => ['text', 'Row label'], 'loan_text' => ['text', 'Traditional loan'], 'lease_text' => ['text', 'Praxia lease'], 'is_active' => ['bool', 'Visible on website'],
    ]],
];

const NAV_ICONS = [
    'home' => '<path d="M3 11 12 3l9 8M5 10v10h14V10"/>', 'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
    'file' => '<path d="M6 3h9l4 4v14H6zM14 3v5h5M9 13h7M9 17h5"/>', 'page' => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/>',
    'case' => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V4h6v3M3 13h18"/>', 'help' => '<circle cx="12" cy="12" r="9"/><path d="M9.5 9.5a2.5 2.5 0 1 1 3.5 2.3c-.7.4-1 .9-1 1.7M12 17h.01"/>',
    'menu' => '<path d="M4 6h16M4 12h16M4 18h10"/>', 'layout' => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 16h18"/>',
    'users' => '<circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M3 20a6 6 0 0 1 12 0M15 20a5 5 0 0 1 6 0"/>', 'quote' => '<path d="M5 18V11a5 5 0 0 1 5-5M14 18V11a5 5 0 0 1 5-5M5 18h5v-5H5zM14 18h5v-5h-5z"/>',
    'briefcase' => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V4h6v3"/>', 'bank' => '<path d="M3 10 12 4l9 6M5 10v8M9 10v8M15 10v8M19 10v8M3 20h18"/>',
    'job' => '<circle cx="12" cy="8" r="3.5"/><path d="M5 20a7 7 0 0 1 14 0"/>', 'chart' => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
    'box' => '<path d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5zM3 7.5 12 12l9-4.5M12 12v9"/>', 'grid' => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
    'steps' => '<circle cx="6" cy="6" r="2.5"/><circle cx="18" cy="12" r="2.5"/><circle cx="6" cy="18" r="2.5"/><path d="M8 7l8 4M8 17l8-4"/>', 'table' => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 10h18M9 4v16"/>',
    'image' => '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="m21 16-5-5-8 9"/>', 'gear' => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2"/>',
];

function admin_user(): ?string { return $_SESSION['admin'] ?? null; }
function require_admin(): void { if (!admin_user()) { header('Location: login.php'); exit; } }

function require_post_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_ok($_POST['csrf'] ?? null)) {
        http_response_code(419);
        exit('Invalid or expired form. Go back, refresh and try again.');
    }
}

function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">'; }

/** flash('Saved.') queues a message; flash('Oops', 'error') queues an error. With no args it returns [msg, type] once. */
function flash(?string $msg = null, string $type = 'ok'): ?array
{
    if ($msg !== null) { $_SESSION['aflash'] = [$msg, $type]; return null; }
    $m = $_SESSION['aflash'] ?? null; unset($_SESSION['aflash']); return $m;
}

function nav_link(string $href, string $label, string $icon, string $cur, int $badge = 0): string
{
    return '<a href="' . e($href) . '" class="' . ($href === $cur ? 'on' : '') . '"><svg viewBox="0 0 24 24">' . NAV_ICONS[$icon] . '</svg>' . e($label)
         . ($badge > 0 ? '<span class="badge">' . $badge . '</span>' : '') . '</a>';
}

/**
 * Opens the page: sidebar + top bar. $actions is trusted HTML for the buttons at the right of the title bar.
 * Close with admin_foot().
 */
function admin_head(string $title, string $sub = '', string $actions = ''): void
{
    $script = basename($_SERVER['SCRIPT_NAME']);
    $cur = $script === 'case_study_edit.php' ? 'case_studies.php' : ($script === 'manage.php' ? 'manage.php?t=' . ($_GET['t'] ?? '') : ($script === 'content.php' ? 'content.php?p=' . (in_array($_GET['p'] ?? '', ['header', 'footer', 'popup'], true) ? $_GET['p'] : 'home') : $script));
    $newEnq = (int)row("SELECT COUNT(*) n FROM enquiries WHERE status='new'")['n'];
    $newApp = (int)row("SELECT COUNT(*) n FROM applications WHERE status='new'")['n'];
    $managed = fn(string $nav) => array_filter(MANAGED, fn($m) => $m['nav'] === $nav);
    ?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow"><title><?= e($title) ?> · Praxia Admin</title><link rel="stylesheet" href="admin.css"></head>
<body>
<aside class="side">
  <a class="brand" href="index.php"><svg viewBox="0 0 40 40" fill="none" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 30 20 12l16 18" stroke="#D9A441"/><path d="M4 22 20 4l16 18" stroke="#fff"/><path d="M4 38 20 20l16 18" stroke="#219EBC" opacity=".7"/></svg>PRAXIA</a>
  <nav class="nav">
    <?= nav_link('index.php', 'Dashboard', 'home', $cur) ?>
    <h6>Inbox</h6>
    <?= nav_link('enquiries.php', 'Enquiries', 'mail', $cur, $newEnq) ?>
    <?= nav_link('applications.php', 'Job applications', 'file', $cur, $newApp) ?>
    <h6>Website content</h6>
    <?= nav_link('content.php?p=home', 'Pages & text', 'page', $cur) ?>
    <?= nav_link('case_studies.php', 'Case studies', 'case', $cur) ?>
    <?php foreach ($managed('website') as $k => $m) echo nav_link('manage.php?t=' . $k, $m['label'], $m['icon'], $cur); ?>
    <h6>Page sections</h6>
    <?php foreach ($managed('blocks') as $k => $m) echo nav_link('manage.php?t=' . $k, $m['label'], $m['icon'], $cur); ?>
    <h6>Header & footer</h6>
    <?= nav_link('content.php?p=header', 'Logo & top bar', 'layout', $cur) ?>
    <?= nav_link('manage.php?t=menu_items', 'Menu links', 'menu', $cur) ?>
    <?= nav_link('content.php?p=footer', 'Footer', 'layout', $cur) ?>
    <?= nav_link('content.php?p=popup', 'Enquiry popup', 'mail', $cur) ?>
    <h6>Library & settings</h6>
    <?= nav_link('media.php', 'Media library', 'image', $cur) ?>
    <?= nav_link('settings.php', 'Settings', 'gear', $cur) ?>
  </nav>
  <div class="userbox"><b><?= e(admin_user()) ?></b><a href="../index.php" target="_blank" style="color:#9fc0d3">View website ↗</a>
    <form method="post" action="logout.php"><?= csrf_field() ?><button class="btn sec sm" style="width:100%;justify-content:center">Log out</button></form></div>
</aside>
<div class="main">
  <header class="top"><button class="burger" aria-label="Menu" onclick="document.body.classList.toggle('menu')"><svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
    <div><h1><?= e($title) ?></h1><?php if ($sub): ?><div class="sub"><?= e($sub) ?></div><?php endif; ?></div>
    <?php if ($actions): ?><div class="acts"><?= $actions ?></div><?php endif; ?></header>
  <main class="content">
<?php if ($m = flash()): ?><div class="flash <?= e($m[1]) ?>"><?= e($m[0]) ?></div><?php endif;
}

function admin_foot(): void { echo '</main></div></body></html>'; }

/** Labelled form field. $type: text | number | area | select */
function fld(string $label, string $name, $val, string $type = 'text', string $hint = '', array $opts = [], bool $full = false, bool $required = false): void
{
    echo '<div class="fld' . ($full ? ' full' : '') . '"><label>' . e($label) . '</label>';
    if ($type === 'area') echo '<textarea name="' . e($name) . '">' . e((string)$val) . '</textarea>';
    elseif ($type === 'select') {
        echo '<select name="' . e($name) . '">';
        foreach ($opts as $k => $l) echo '<option value="' . e((string)$k) . '"' . ((string)$val === (string)$k ? ' selected' : '') . '>' . e($l) . '</option>';
        echo '</select>';
    } else echo '<input name="' . e($name) . '" value="' . e((string)$val) . '"' . ($type === 'number' ? ' type="number"' : '') . ($required ? ' required' : '') . '>';
    if ($hint) echo '<small>' . e($hint) . '</small>';
    echo '</div>';
}

/** Simple pagination: returns [offset, pagerHtml]. */
function paginate(int $total, int $per, string $baseQuery = ''): array
{
    $pages = max(1, (int)ceil($total / $per));
    $p = min($pages, max(1, (int)($_GET['page'] ?? 1)));
    $html = '';
    if ($pages > 1) {
        $html = '<div class="pager">';
        for ($i = 1; $i <= $pages; $i++) $html .= $i === $p ? "<span>$i</span>" : '<a href="?' . e($baseQuery) . ($baseQuery ? '&' : '') . 'page=' . $i . '">' . $i . '</a>';
        $html .= '</div>';
    }
    return [($p - 1) * $per, $html];
}

/**
 * Moves one row up/down among its group's rows. The whole table is first renumbered 1..n in its current order
 * (so ties disappear), then the row swaps numbers with its neighbour in the same group.
 * $table and $groupCol must be trusted literals.
 */
function move_row(string $table, ?string $groupCol, int $id, string $dir): void
{
    $all = rows("SELECT id" . ($groupCol ? ", `$groupCol` AS g" : '') . " FROM `$table` ORDER BY sort_order, id");
    $num = []; $n = 0;
    foreach ($all as $r) $num[$r['id']] = ++$n;
    $me = null;
    foreach ($all as $r) if ((int)$r['id'] === $id) $me = $r;
    if (!$me) return;
    $peers = array_values(array_filter($all, fn($r) => !$groupCol || $r['g'] === $me['g']));
    $i = array_search($id, array_map(fn($r) => (int)$r['id'], $peers), true);
    $j = $dir === 'up' ? $i - 1 : $i + 1;
    if ($i !== false && isset($peers[$j])) {
        $a = $peers[$i]['id']; $b = $peers[$j]['id'];
        [$num[$a], $num[$b]] = [$num[$b], $num[$a]];
    }
    $st = db()->prepare("UPDATE `$table` SET sort_order = ? WHERE id = ?");
    foreach ($num as $rid => $v) $st->execute([$v, $rid]);
}

/** Image input: preview, upload, paste URL, or pick from the media library. */
function image_field(string $name, string $value, string $hint = ''): void
{
    $src = str_starts_with($value, 'media/') ? '../' . $value : $value;
    ?>
    <div class="imgfield">
      <img class="imgprev" src="<?= e($src) ?>" alt="" style="display:<?= $value === '' ? 'none' : 'block' ?>">
      <input type="file" name="<?= e($name) ?>__file" accept="image/jpeg,image/png,image/webp,image/gif" class="imgfile">
      <div style="display:flex;gap:8px;margin-top:8px"><input name="<?= e($name) ?>" value="<?= e($value) ?>" placeholder="…or paste an image URL" class="imgurl"><button type="button" class="btn sec imgpick" style="white-space:nowrap">Choose from library</button></div>
      <small class="muted">JPG, PNG, WebP or GIF, max 5 MB. <?= e($hint) ?></small>
    </div>
    <?php
}

/** Modal + script that powers image_field(). Call once per page that uses it, before admin_foot(). */
function image_picker_assets(): void
{
    $lib = rows('SELECT file_path, original_name FROM media ORDER BY id DESC LIMIT 200');
    ?>
<div id="pickModal" style="display:none;position:fixed;inset:0;background:rgba(2,48,71,.6);z-index:60;padding:30px;overflow:auto">
  <div class="panel" style="max-width:860px;margin:0 auto">
    <div style="display:flex;justify-content:space-between;align-items:center"><h2 class="panel-h">Media library</h2><button type="button" class="btn sec sm" id="pickClose">Close</button></div>
    <div class="mgrid" style="margin-top:14px;grid-template-columns:repeat(auto-fill,minmax(120px,1fr))">
      <?php foreach ($lib as $m): ?><button type="button" data-path="<?= e($m['file_path']) ?>" title="<?= e($m['original_name']) ?>" style="padding:0;background:#eee;border:2px solid transparent;overflow:hidden"><img src="../<?= e($m['file_path']) ?>" alt="" style="width:100%;height:90px;object-fit:cover;display:block"></button><?php endforeach; ?>
    </div>
    <?php if (!$lib): ?><p class="muted">Nothing uploaded yet. Upload from any image field, or in the Media library.</p><?php endif; ?>
  </div>
</div>
<script>
(function () {
  const modal = document.getElementById('pickModal'); let onPick = null;
  // Reusable by any editor: window.openMediaPicker(path => …) opens the library and returns the chosen "media/…" path.
  window.openMediaPicker = cb => { onPick = cb; modal.style.display = 'block'; };
  const show = (f, v) => { const p = f.querySelector('.imgprev'); if (!v) { p.style.display = 'none'; return; } p.src = /^media\//.test(v) ? '../' + v : v; p.style.display = 'block'; };
  document.querySelectorAll('.imgfield').forEach(f => {
    f.querySelector('.imgfile').addEventListener('change', e => { const file = e.target.files[0]; if (file) { const p = f.querySelector('.imgprev'); p.src = URL.createObjectURL(file); p.style.display = 'block'; } });
    f.querySelector('.imgurl').addEventListener('change', e => show(f, e.target.value.trim()));
    f.querySelector('.imgpick').addEventListener('click', () => window.openMediaPicker(path => {
      f.querySelector('.imgurl').value = path; f.querySelector('.imgfile').value = ''; show(f, path);
    }));
  });
  modal.querySelectorAll('[data-path]').forEach(b => b.addEventListener('click', () => { modal.style.display = 'none'; if (onPick) onPick(b.dataset.path); }));
  document.getElementById('pickClose').onclick = () => modal.style.display = 'none';
  modal.addEventListener('click', e => { if (e.target === modal) modal.style.display = 'none'; });
})();
</script>
<?php
}
