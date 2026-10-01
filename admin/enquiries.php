<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_admin();

const SOURCES = ['quote' => 'Quick quote', 'proposal' => 'Proposal', 'contact' => 'Contact', 'popup' => 'Popup'];
$statuses = ['new', 'contacted', 'closed'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if (($_POST['action'] ?? '') === 'status' && in_array($_POST['status'] ?? '', $statuses, true)) {
        db()->prepare('UPDATE enquiries SET status = ? WHERE id = ?')->execute([$_POST['status'], $id]);
        flash('Status updated.');
    } elseif (($_POST['action'] ?? '') === 'delete') {
        db()->prepare('DELETE FROM enquiries WHERE id = ?')->execute([$id]);
        flash('Enquiry deleted.');
    }
    header('Location: enquiries.php?' . http_build_query(array_filter(['s' => $_GET['s'] ?? '', 'q' => $_GET['q'] ?? '', 'page' => $_GET['page'] ?? '']))); exit;
}

$s = in_array($_GET['s'] ?? '', $statuses, true) ? $_GET['s'] : '';
$q = trim((string)($_GET['q'] ?? ''));
$where = []; $args = [];
if ($s) { $where[] = 'status = ?'; $args[] = $s; }
if ($q !== '') {
    $where[] = '(name LIKE ? OR company LIKE ? OR email LIKE ? OR phone LIKE ? OR message LIKE ?)';
    array_push($args, ...array_fill(0, 5, '%' . $q . '%'));
}
$w = $where ? 'WHERE ' . implode(' AND ', $where) : '';

if (isset($_GET['csv'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="enquiries-' . date('Ymd') . '.csv"');
    $o = fopen('php://output', 'w');
    fwrite($o, "\xEF\xBB\xBF");
    fputcsv($o, ['ID', 'Date', 'Type', 'Name', 'Company', 'Email', 'Phone', 'Asset type', 'Value (Cr)', 'Audience', 'Message', 'Status']);
    foreach (rows("SELECT * FROM enquiries $w ORDER BY id DESC", $args) as $r) {
        $cells = [$r['id'], $r['created_at'], $r['source'], $r['name'], $r['company'], $r['email'], $r['phone'], $r['asset_type'], $r['asset_value'], $r['audience'], $r['message'], $r['status']];
        // Prefix formula-looking cells so spreadsheets do not execute them.
        fputcsv($o, array_map(fn($c) => preg_match('/^[=+\-@]/', (string)$c) ? "'" . $c : $c, $cells));
    }
    exit;
}

$counts = ['' => (int)row('SELECT COUNT(*) n FROM enquiries')['n']];
foreach ($statuses as $st) $counts[$st] = (int)row('SELECT COUNT(*) n FROM enquiries WHERE status = ?', [$st])['n'];
$total = (int)row("SELECT COUNT(*) n FROM enquiries $w", $args)['n'];
[$offset, $pager] = paginate($total, 20, http_build_query(array_filter(['s' => $s, 'q' => $q])));
$list = rows("SELECT * FROM enquiries $w ORDER BY id DESC LIMIT 20 OFFSET $offset", $args);

admin_head('Enquiries', 'Quotes, proposals and contact requests from the website', '<a class="btn sec" href="?' . e(http_build_query(array_filter(['s' => $s, 'q' => $q, 'csv' => 1]))) . '">Export CSV</a>');
?>
<div class="tabs"><a href="enquiries.php" class="<?= $s === '' ? 'on' : '' ?>">All<small><?= $counts[''] ?></small></a>
<?php foreach ($statuses as $st): ?><a href="?s=<?= $st ?>" class="<?= $s === $st ? 'on' : '' ?>"><?= ucfirst($st) ?><small><?= $counts[$st] ?></small></a><?php endforeach; ?></div>

<div class="card tablecard">
  <div class="th"><h2><?= $total ?> result<?= $total === 1 ? '' : 's' ?></h2>
    <form class="tools" method="get"><?php if ($s): ?><input type="hidden" name="s" value="<?= e($s) ?>"><?php endif; ?><input type="search" name="q" value="<?= e($q) ?>" placeholder="Search name, company, email, phone…"><button class="btn sec">Search</button><?php if ($q): ?><a class="btn ghost" href="?<?= e(http_build_query(array_filter(['s' => $s]))) ?>">Clear</a><?php endif; ?></form></div>
  <?php if ($list): ?><div class="wrap"><table><thead><tr><th>Received</th><th>Type</th><th>Contact</th><th>Request</th><th>Status</th><th></th></tr></thead><tbody>
  <?php foreach ($list as $r): ?><tr>
    <td class="num"><?= e(date('d M Y', strtotime($r['created_at']))) ?><br><span class="muted"><?= e(date('H:i', strtotime($r['created_at']))) ?></span></td>
    <td><span class="pill"><?= e(SOURCES[$r['source']] ?? $r['source']) ?></span></td>
    <td><b><?= e($r['name'] ?: '—') ?></b><br><?= e($r['company']) ?><br><?php if ($r['email']): ?><a href="mailto:<?= e($r['email']) ?>"><?= e($r['email']) ?></a><br><?php endif; ?><?php if ($r['phone']): ?><a href="tel:<?= e($r['phone']) ?>"><?= e($r['phone']) ?></a><?php endif; ?></td>
    <td style="max-width:340px"><?php $line = trim(($r['asset_type'] ?? '') . ' ' . ($r['asset_value'] ? '· ₹' . $r['asset_value'] . ' Cr' : '') . ' ' . ($r['audience'] ? '· ' . $r['audience'] : '')); if ($line): ?><b><?= e($line) ?></b><br><?php endif; ?><?= nl2br(e($r['message'])) ?></td>
    <td><form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
      <select name="status" onchange="this.form.submit()"><?php foreach ($statuses as $st): ?><option<?= $st === $r['status'] ? ' selected' : '' ?>><?= $st ?></option><?php endforeach; ?></select></form></td>
    <td class="num"><form method="post" class="inline" onsubmit="return confirm('Delete this enquiry permanently?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn del sm">Delete</button></form></td>
  </tr><?php endforeach; ?></tbody></table></div><?= $pager ?>
  <?php else: ?><div class="empty"><b>Nothing found</b><?= $q || $s ? 'Try a different search or filter.' : 'Website form submissions will appear here.' ?></div><?php endif; ?>
</div>
<?php admin_foot(); ?>
