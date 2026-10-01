<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_once __DIR__ . '/../includes/media.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_csrf();
    if (($_POST['action'] ?? '') === 'upload') {
        $ok = 0; $errs = [];
        $files = $_FILES['images'] ?? null;
        if ($files && is_array($files['name'])) {
            foreach ($files['name'] as $i => $n) {
                if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
                try {
                    store_image(['name' => $n, 'tmp_name' => $files['tmp_name'][$i], 'error' => $files['error'][$i], 'size' => $files['size'][$i]]);
                    $ok++;
                } catch (RuntimeException $ex) { $errs[] = "$n: " . $ex->getMessage(); }
            }
        }
        if ($errs) flash("$ok uploaded. Problems: " . implode(' | ', $errs), 'error'); else flash("$ok image" . ($ok === 1 ? '' : 's') . ' uploaded.');
    } elseif (($_POST['action'] ?? '') === 'delete') {
        $m = row('SELECT * FROM media WHERE id = ?', [(int)($_POST['id'] ?? 0)]);
        if ($m) {
            @unlink(ROOT . '/media/' . basename($m['file_path']));
            db()->prepare('DELETE FROM media WHERE id = ?')->execute([$m['id']]);
            flash('Image deleted. Anywhere it was used will show a broken image, so replace it there too.');
        }
    }
    header('Location: media.php'); exit;
}

$list = rows('SELECT * FROM media ORDER BY id DESC');
admin_head('Media library', 'Images you have uploaded. Pick them from any image field.');
?>
<form method="post" enctype="multipart/form-data" class="panel"><?= csrf_field() ?><input type="hidden" name="action" value="upload">
  <h2>Upload images</h2><p class="hint">Select one or many. JPG, PNG, WebP or GIF, max 5 MB each.</p>
  <div style="display:flex;gap:10px;flex-wrap:wrap"><input type="file" name="images[]" multiple required accept="image/jpeg,image/png,image/webp,image/gif" style="max-width:420px"><button class="btn gold">Upload</button></div>
</form>

<?php if ($list): ?>
<div class="mgrid">
<?php foreach ($list as $m): ?>
  <div class="card mitem"><img src="../<?= e($m['file_path']) ?>" alt="">
    <p><?= e($m['original_name']) ?></p>
    <input readonly value="<?= e($m['file_path']) ?>" onclick="this.select()">
    <form method="post" onsubmit="return confirm('Delete this image file?')" style="margin-top:8px"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$m['id'] ?>"><button class="btn del sm">Delete</button></form></div>
<?php endforeach; ?>
</div>
<?php else: ?><div class="card"><div class="empty"><b>No uploads yet</b>Upload your first image above.</div></div><?php endif; ?>
<?php admin_foot(); ?>
