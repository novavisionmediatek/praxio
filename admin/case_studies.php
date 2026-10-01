<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_csrf();
    $id = (int)($_POST['id'] ?? 0);
    switch ($_POST['action'] ?? '') {
        case 'delete': db()->prepare('DELETE FROM case_studies WHERE id = ?')->execute([$id]); flash('Case study deleted.'); break;
        case 'toggle': db()->prepare('UPDATE case_studies SET is_active = 1 - is_active WHERE id = ?')->execute([$id]); break;
        case 'up': case 'down': move_row('case_studies', null, $id, $_POST['action']); break;
    }
    header('Location: case_studies.php'); exit;
}

$list = rows('SELECT c.*, (SELECT COUNT(*) FROM case_study_images i WHERE i.case_study_id = c.id) AS photos FROM case_studies c ORDER BY sort_order, id');
$firstId = $list[0]['id'] ?? 0; $lastId = $list ? $list[count($list) - 1]['id'] : 0;

admin_head('Case studies', 'Each case study has its own page on the website', '<a class="btn gold" href="case_study_edit.php">+ New case study</a>');
?>
<div class="card tablecard">
<?php if ($list): ?><div class="wrap"><table><thead><tr><th>Order</th><th>Image</th><th>Case study</th><th>Photos</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach ($list as $r): ?><tr>
  <td class="num"><form method="post" class="mv"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
    <button name="action" value="up" <?= $firstId == $r['id'] ? 'disabled' : '' ?>>▲</button><button name="action" value="down" <?= $lastId == $r['id'] ? 'disabled' : '' ?>>▼</button></form></td>
  <td class="num"><img class="thumb" src="<?= e(str_starts_with($r['image_url'], 'media/') ? '../' . $r['image_url'] : $r['image_url']) ?>" alt=""></td>
  <td><b><?= e($r['title']) ?></b><br><span class="muted"><?= e($r['tag']) ?></span><?= $r['home_order'] ? ' <span class="pill">On home page</span>' : '' ?></td>
  <td class="num"><?= (int)$r['photos'] ?></td>
  <td class="num"><form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn sm <?= $r['is_active'] ? 'sec' : 'ghost' ?>"><span class="pill <?= $r['is_active'] ? 'live' : 'draft' ?>"><?= $r['is_active'] ? 'Published' : 'Draft' ?></span></button></form></td>
  <td class="num"><a class="btn sec sm" href="../case-study.php?slug=<?= e($r['slug']) ?>" target="_blank">View ↗</a>
    <a class="btn sec sm" href="case_study_edit.php?id=<?= (int)$r['id'] ?>">Edit</a>
    <form method="post" class="inline" onsubmit="return confirm('Delete this case study and its page?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn del sm">Delete</button></form></td>
</tr><?php endforeach; ?></tbody></table></div>
<?php else: ?><div class="empty"><b>No case studies yet</b>Click "New case study" to publish the first one.</div><?php endif; ?>
</div>
<?php admin_foot(); ?>
