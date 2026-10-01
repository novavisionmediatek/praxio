<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_once __DIR__ . '/../includes/media.php';
require_once __DIR__ . '/../includes/icons.php';
require_admin();

// Generic list + add/edit/delete/reorder for every table listed in MANAGED (see _layout.php).
$t = $_GET['t'] ?? '';
if (!isset(MANAGED[$t])) { header('Location: index.php'); exit; }
$cfg = MANAGED[$t];   // $t is validated against this whitelist, so it is safe inside the SQL identifiers below
$fields = $cfg['fields'];
$groupCol = $cfg['group'];
$self = 'manage.php?t=' . $t;

function select_options(array $f): array
{
    return $f[2] === 'ICONS' ? array_map(fn($i) => $i[0], ICONS) : $f[2];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_csrf();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'delete') {
        db()->prepare("DELETE FROM `$t` WHERE id = ?")->execute([$id]);
        flash('Deleted.');
    } elseif ($action === 'toggle') {
        db()->prepare("UPDATE `$t` SET is_active = 1 - is_active WHERE id = ?")->execute([$id]);
    } elseif ($action === 'up' || $action === 'down') {
        move_row($t, $groupCol, $id, $action);
    } elseif ($action === 'save') {
        $vals = [];
        try {
            foreach ($fields as $col => $f) {
                $raw = $_POST[$col] ?? '';
                if ($f[0] === 'url') {
                    $u = trim((string)$raw);
                    if ($u === '' || preg_match('~^\s*(javascript|data|vbscript):~i', $u) || !preg_match('~^(https?://\S+|mailto:\S+|tel:\S+|#\S*|/\S*|[A-Za-z0-9_][A-Za-z0-9_./%?=&#:-]*)$~', $u)) {
                        throw new RuntimeException('Enter a valid link, such as about.php, #team, https://example.com or mailto:you@example.com');
                    }
                    $vals[$col] = $u;
                    continue;
                }
                if ($f[0] === 'image') {
                    $v = save_image_upload($col . '__file') ?? trim((string)$raw);
                    if ($v === '') {
                        if (empty($f[2])) throw new RuntimeException($f[1] . ' is required: upload a file or paste an image URL.');
                        $v = null;
                    } elseif (!preg_match('~^(https?://|media/)~i', $v)) {
                        throw new RuntimeException($f[1] . ' must be an uploaded file or a URL starting with http:// or https://');
                    }
                    $vals[$col] = $v;
                    continue;
                }
                $vals[$col] = match ($f[0]) {
                    'bool'   => isset($_POST[$col]) ? 1 : 0,
                    'number' => ($raw === '' && !empty($f[2])) ? null : (int)$raw,
                    'select' => array_key_exists($raw, select_options($f)) ? $raw : array_key_first(select_options($f)),
                    default  => trim((string)$raw),
                };
            }
        } catch (RuntimeException $ex) {
            flash($ex->getMessage(), 'error');
            header("Location: $self&edit=$id"); exit;
        }
        if (array_key_exists('home_answer', $vals) && $vals['home_answer'] === '') $vals['home_answer'] = null;
        if ($id) {
            $set = implode(', ', array_map(fn($c) => "`$c` = ?", array_keys($vals)));
            db()->prepare("UPDATE `$t` SET $set WHERE id = ?")->execute([...array_values($vals), $id]);
        } else {
            $vals['sort_order'] = (int)row("SELECT COALESCE(MAX(sort_order), 0) + 1 AS n FROM `$t`")['n'];   // new items go last
            $cols = implode(', ', array_map(fn($c) => "`$c`", array_keys($vals)));
            db()->prepare("INSERT INTO `$t` ($cols) VALUES (" . implode(', ', array_fill(0, count($vals), '?')) . ')')->execute(array_values($vals));
        }
        flash('Saved.');
    }
    header("Location: $self"); exit;
}

$editing = isset($_GET['edit']) ? (int)$_GET['edit'] : null;   // 0 = new
$current = $editing ? row("SELECT * FROM `$t` WHERE id = ?", [$editing]) : null;
if ($editing && !$current) { flash('Item not found.', 'error'); header("Location: $self"); exit; }

if ($editing !== null) {
    admin_head(($editing ? 'Edit ' : 'Add ') . $cfg['one'], $cfg['label'], '<a class="btn sec" href="' . e($self) . '">← Back to list</a>');
    ?>
<form method="post" enctype="multipart/form-data" class="panel"><?= csrf_field() ?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)$editing ?>">
  <div class="grid2">
  <?php foreach ($fields as $col => $f):
      if ($f[0] === 'bool') continue;
      $val = $current[$col] ?? '';
      $full = in_array($f[0], ['textarea', 'image'], true); ?>
    <div class="fld<?= $full ? ' full' : '' ?>"><label><?= e($f[1]) ?></label>
    <?php if ($f[0] === 'image'): image_field($col, (string)$val, !empty($f[2]) ? 'Clear the URL to remove the image.' : '');
          elseif ($f[0] === 'textarea'): ?><textarea name="<?= e($col) ?>"><?= e($val) ?></textarea>
    <?php elseif ($f[0] === 'select'): ?><select name="<?= e($col) ?>"><?php foreach (select_options($f) as $k => $lab): ?><option value="<?= e($k) ?>"<?= (string)$val === (string)$k ? ' selected' : '' ?>><?= e($lab) ?></option><?php endforeach; ?></select>
    <?php else: ?><input name="<?= e($col) ?>" type="<?= $f[0] === 'number' ? 'number' : 'text' ?>" value="<?= e($val) ?>"<?= in_array($f[0], ['text', 'url'], true) && !in_array($col, ['prefix', 'suffix'], true) ? ' required' : '' ?>>
    <?php endif; ?></div>
  <?php endforeach; ?>
  </div>
  <?php foreach ($fields as $col => $f): if ($f[0] !== 'bool') continue; $val = $current[$col] ?? 1; ?>
    <div class="fld"><label class="chk"><input type="checkbox" name="<?= e($col) ?>" value="1"<?= $val ? ' checked' : '' ?>> <?= e($f[1]) ?></label></div>
  <?php endforeach; ?>
  <div class="savebar"><button class="btn gold">Save</button><a class="btn sec" href="<?= e($self) ?>">Cancel</a></div>
</form>
<?php image_picker_assets();
    admin_foot();
    exit;
}

// ---- list view ----
$orderBy = ($groupCol ? "`$groupCol`, " : '') . 'sort_order, id';
$list = rows("SELECT * FROM `$t` ORDER BY $orderBy");
$groupLabels = ($groupCol && $fields[$groupCol][0] === 'select') ? select_options($fields[$groupCol]) : [];
$imgCol = null;
foreach ($fields as $col => $f) if ($f[0] === 'image') { $imgCol = $col; break; }
$firstIn = []; $lastIn = [];
foreach ($list as $r) { $g = $groupCol ? $r[$groupCol] : ''; $firstIn[$g] ??= $r['id']; $lastIn[$g] = $r['id']; }

admin_head($cfg['label'], $cfg['desc'], '<a class="btn gold" href="' . e($self) . '&edit=0">+ Add ' . e($cfg['one']) . '</a>');
?>
<div class="card tablecard">
<?php if ($list): ?><div class="wrap"><table><thead><tr><th>Order</th><?php if ($imgCol): ?><th>Image</th><?php endif; ?><th><?= e($fields[$cfg['title']][1]) ?></th><?php if ($groupCol): ?><th><?= e($fields[$groupCol][1]) ?></th><?php endif; ?><th>Visible</th><th></th></tr></thead><tbody>
<?php foreach ($list as $r): $g = $groupCol ? $r[$groupCol] : ''; ?><tr>
  <td class="num"><form method="post" class="mv"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
    <button name="action" value="up" title="Move up" <?= $firstIn[$g] == $r['id'] ? 'disabled' : '' ?>>▲</button><button name="action" value="down" title="Move down" <?= $lastIn[$g] == $r['id'] ? 'disabled' : '' ?>>▼</button></form></td>
  <?php if ($imgCol): $iv = (string)($r[$imgCol] ?? ''); ?><td class="num"><?php if ($iv !== ''): ?><img class="thumb" src="<?= e(str_starts_with($iv, 'media/') ? '../' . $iv : $iv) ?>" alt=""<?= $imgCol === 'logo_url' ? ' style="object-fit:contain;background:#f4f6f8;padding:4px"' : '' ?>><?php else: ?><span class="muted">—</span><?php endif; ?></td><?php endif; ?>
  <td><b><?= e(mb_strimwidth((string)$r[$cfg['title']], 0, 90, '…')) ?></b></td>
  <?php if ($groupCol): ?><td><span class="pill"><?= e($groupLabels[$g] ?? $g) ?></span></td><?php endif; ?>
  <td class="num"><form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn sm <?= $r['is_active'] ? 'sec' : 'ghost' ?>" title="Click to <?= $r['is_active'] ? 'hide' : 'show' ?>"><span class="pill <?= $r['is_active'] ? 'live' : 'draft' ?>"><?= $r['is_active'] ? 'Live' : 'Hidden' ?></span></button></form></td>
  <td class="num"><a class="btn sec sm" href="<?= e($self) ?>&edit=<?= (int)$r['id'] ?>">Edit</a>
    <form method="post" class="inline" onsubmit="return confirm('Delete this <?= e($cfg['one']) ?>?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn del sm">Delete</button></form></td>
</tr><?php endforeach; ?></tbody></table></div>
<?php else: ?><div class="empty"><b>No <?= e(strtolower($cfg['label'])) ?> yet</b>Click "Add <?= e($cfg['one']) ?>" to create the first one.</div><?php endif; ?>
</div>
<p class="hint">Use ▲ ▼ to change the order shown on the website<?= $groupCol ? ' (ordering is kept separately within each ' . e(strtolower($fields[$groupCol][1])) . ')' : '' ?>. Click Live/Hidden to show or hide an item without deleting it.</p>
<?php admin_foot(); ?>
