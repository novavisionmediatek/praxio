<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_admin();

$n = fn(string $sql) => (int)row($sql)['n'];
$newEnq   = $n("SELECT COUNT(*) n FROM enquiries WHERE status='new'");
$month    = $n('SELECT COUNT(*) n FROM enquiries WHERE created_at > (NOW() - INTERVAL 30 DAY)');
$newApp   = $n("SELECT COUNT(*) n FROM applications WHERE status='new'");
$cases    = $n('SELECT COUNT(*) n FROM case_studies WHERE is_active=1');
$roles    = $n('SELECT COUNT(*) n FROM job_openings WHERE is_active=1');
$latest   = rows('SELECT * FROM enquiries ORDER BY id DESC LIMIT 6');
$latestApp = rows('SELECT a.*, j.title AS job FROM applications a LEFT JOIN job_openings j ON j.id = a.job_id ORDER BY a.id DESC LIMIT 5');

admin_head('Dashboard', 'Welcome back, ' . admin_user());
?>
<div class="kpis">
  <a class="card kpi <?= $newEnq ? 'hot' : '' ?>" href="enquiries.php?s=new"><small>New enquiries</small><b><?= $newEnq ?></b><span>waiting for a reply</span></a>
  <a class="card kpi" href="enquiries.php"><small>Enquiries, last 30 days</small><b><?= $month ?></b><span>quotes, proposals and contact</span></a>
  <a class="card kpi <?= $newApp ? 'hot' : '' ?>" href="applications.php"><small>New applications</small><b><?= $newApp ?></b><span>for <?= $roles ?> open role<?= $roles === 1 ? '' : 's' ?></span></a>
  <a class="card kpi" href="case_studies.php"><small>Published case studies</small><b><?= $cases ?></b><span>on the website</span></a>
</div>

<div class="card tablecard">
  <div class="th"><h2>Latest enquiries</h2><a class="btn sec sm" href="enquiries.php">View all</a></div>
  <?php if ($latest): ?><div class="wrap"><table><thead><tr><th>Received</th><th>Type</th><th>Contact</th><th>Interest</th><th>Status</th></tr></thead><tbody>
  <?php foreach ($latest as $r): ?><tr>
    <td class="num"><?= e(date('d M, H:i', strtotime($r['created_at']))) ?></td>
    <td><span class="pill"><?= e(['quote' => 'Quick quote', 'proposal' => 'Proposal', 'contact' => 'Contact', 'popup' => 'Popup'][$r['source']] ?? $r['source']) ?></span></td>
    <td><b><?= e($r['name'] ?: '—') ?></b><br><span class="muted"><?= e($r['company']) ?><?= $r['phone'] ? ' · ' . e($r['phone']) : '' ?></span></td>
    <td><?= e(mb_strimwidth(trim(($r['asset_type'] ?? '') . ' ' . ($r['asset_value'] ? '₹' . $r['asset_value'] . ' Cr' : '') . ' ' . ($r['message'] ?? '')), 0, 80, '…')) ?></td>
    <td><span class="pill <?= e($r['status']) ?>"><?= e($r['status']) ?></span></td>
  </tr><?php endforeach; ?></tbody></table></div>
  <?php else: ?><div class="empty"><b>No enquiries yet</b>Submissions from the website forms will appear here.</div><?php endif; ?>
</div>

<div class="grid2" style="align-items:start">
  <div class="card tablecard">
    <div class="th"><h2>Latest applications</h2><a class="btn sec sm" href="applications.php">View all</a></div>
    <?php if ($latestApp): ?><div class="wrap"><table><tbody>
    <?php foreach ($latestApp as $a): ?><tr><td><b><?= e($a['name']) ?></b><br><span class="muted"><?= e($a['job'] ?? 'Open application') ?></span></td><td class="num"><?= e(date('d M', strtotime($a['created_at']))) ?></td><td class="num"><span class="pill <?= e($a['status']) ?>"><?= e($a['status']) ?></span></td></tr><?php endforeach; ?>
    </tbody></table></div>
    <?php else: ?><div class="empty">No applications yet.</div><?php endif; ?>
  </div>
  <div class="panel">
    <h2>Quick actions</h2><p class="hint">Jump straight to the most common jobs.</p>
    <div style="display:grid;gap:8px">
      <a class="btn sec" href="content.php?p=home">Edit home page text &amp; images</a>
      <a class="btn sec" href="case_study_edit.php">Add a case study</a>
      <a class="btn sec" href="manage.php?t=job_openings&edit=0">Add a job opening</a>
      <a class="btn sec" href="manage.php?t=faqs&edit=0">Add an FAQ</a>
      <a class="btn sec" href="media.php">Upload images</a>
    </div>
  </div>
</div>
<?php admin_foot(); ?>
