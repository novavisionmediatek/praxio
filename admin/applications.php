<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_admin();

$statuses = ['new', 'shortlisted', 'rejected'];

// CV download (served from the protected uploads/ folder)
if (isset($_GET['cv'])) {
    $a = row('SELECT cv_file, cv_original FROM applications WHERE id = ?', [(int)$_GET['cv']]);
    $path = $a ? ROOT . '/uploads/' . basename((string)$a['cv_file']) : '';
    if (!$a || !is_file($path)) { http_response_code(404); exit('File not found.'); }
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . str_replace('"', '', $a['cv_original'] ?: $a['cv_file']) . '"');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if (($_POST['action'] ?? '') === 'status' && in_array($_POST['status'] ?? '', $statuses, true)) {
        db()->prepare('UPDATE applications SET status = ? WHERE id = ?')->execute([$_POST['status'], $id]);
        flash('Status updated.');
    } elseif (($_POST['action'] ?? '') === 'delete') {
        $a = row('SELECT cv_file FROM applications WHERE id = ?', [$id]);
        if ($a && $a['cv_file']) @unlink(ROOT . '/uploads/' . basename($a['cv_file']));
        db()->prepare('DELETE FROM applications WHERE id = ?')->execute([$id]);
        flash('Application and CV deleted.');
    }
    header('Location: applications.php?' . http_build_query(array_filter(['s' => $_GET['s'] ?? '', 'page' => $_GET['page'] ?? '']))); exit;
}

$s = in_array($_GET['s'] ?? '', $statuses, true) ? $_GET['s'] : '';
$w = $s ? 'WHERE a.status = ?' : ''; $args = $s ? [$s] : [];
$counts = ['' => (int)row('SELECT COUNT(*) n FROM applications')['n']];
foreach ($statuses as $st) $counts[$st] = (int)row('SELECT COUNT(*) n FROM applications WHERE status = ?', [$st])['n'];
[$offset, $pager] = paginate($counts[$s], 20, http_build_query(array_filter(['s' => $s])));
$list = rows("SELECT a.*, j.title AS job_title FROM applications a LEFT JOIN job_openings j ON j.id = a.job_id $w ORDER BY a.id DESC LIMIT 20 OFFSET $offset", $args);

admin_head('Job applications', 'People who applied through the Careers page');
?>
<div class="tabs"><a href="applications.php" class="<?= $s === '' ? 'on' : '' ?>">All<small><?= $counts[''] ?></small></a>
<?php foreach ($statuses as $st): ?><a href="?s=<?= $st ?>" class="<?= $s === $st ? 'on' : '' ?>"><?= ucfirst($st) ?><small><?= $counts[$st] ?></small></a><?php endforeach; ?></div>

<div class="card tablecard">
  <?php if ($list): ?><div class="wrap"><table><thead><tr><th>Received</th><th>Applicant</th><th>Role</th><th>Note</th><th>CV</th><th>Status</th><th></th></tr></thead><tbody>
  <?php foreach ($list as $r): ?><tr>
    <td class="num"><?= e(date('d M Y', strtotime($r['created_at']))) ?></td>
    <td><b><?= e($r['name']) ?></b><br><a href="mailto:<?= e($r['email']) ?>"><?= e($r['email']) ?></a><?= $r['phone'] ? '<br>' . e($r['phone']) : '' ?></td>
    <td><?= e($r['job_title'] ?? 'Open application') ?></td>
    <td style="max-width:300px"><?= nl2br(e($r['message'])) ?></td>
    <td class="num"><?php if ($r['cv_file']): ?><a class="btn sec sm" href="?cv=<?= (int)$r['id'] ?>">Download</a><?php endif; ?></td>
    <td><form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
      <select name="status" onchange="this.form.submit()"><?php foreach ($statuses as $st): ?><option<?= $st === $r['status'] ? ' selected' : '' ?>><?= $st ?></option><?php endforeach; ?></select></form></td>
    <td class="num"><form method="post" class="inline" onsubmit="return confirm('Delete this application and its CV permanently?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn del sm">Delete</button></form></td>
  </tr><?php endforeach; ?></tbody></table></div><?= $pager ?>
  <?php else: ?><div class="empty"><b>No applications</b>Applications from the Careers page will appear here.</div><?php endif; ?>
</div>
<?php admin_foot(); ?>
