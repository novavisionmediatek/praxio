<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_once __DIR__ . '/../includes/media.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$cs = $id ? row('SELECT * FROM case_studies WHERE id = ?', [$id]) : null;
if ($id && !$cs) { flash('Case study not found.', 'error'); header('Location: case_studies.php'); exit; }

$text = ['title', 'slug', 'tag', 'summary', 'client_name', 'location', 'service', 'duration', 'image_alt', 'metrics', 'challenge', 'solution', 'outcome',
         'results', 'body', 'quote_text', 'quote_author', 'quote_role', 'home_tag', 'home_title', 'home_kpis', 'seo_title', 'seo_desc'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_csrf();
    $pdo = db();
    $v = [];
    foreach ($text as $k) $v[$k] = trim(str_replace("\r\n", "\n", (string)($_POST[$k] ?? '')));
    $v['body'] = clean_rich_html($v['body']);   // long description: keep only safe formatting
    $v['is_active'] = isset($_POST['is_active']) ? 1 : 0;
    $v['home_order'] = ($_POST['home_order'] ?? '') === '' ? null : max(1, min(3, (int)$_POST['home_order']));

    try {
        if ($v['title'] === '') throw new RuntimeException('Title is required.');
        if ($v['tag'] === '') throw new RuntimeException('Tag is required (for example "Automotive · Credit rating AA+").');
        $img = save_image_upload('image_url__file') ?? trim((string)($_POST['image_url'] ?? ''));
        if ($img === '') throw new RuntimeException('A main image is required: upload one or paste a URL.');
        if (!preg_match('~^(https?://|media/)~i', $img)) throw new RuntimeException('Main image must be an upload or a full http(s) URL.');
        $v['image_url'] = $img;
        $v['slug'] = unique_case_slug(slugify($v['slug'] !== '' ? $v['slug'] : $v['title']), $id);
        foreach (['summary', 'client_name', 'location', 'service', 'duration', 'results', 'body', 'quote_text', 'quote_author', 'quote_role', 'home_tag', 'home_title', 'home_kpis', 'seo_title', 'seo_desc'] as $opt) {
            if ($v[$opt] === '') $v[$opt] = null;
        }

        $pdo->beginTransaction();
        if ($id) {
            $set = implode(', ', array_map(fn($c) => "`$c` = ?", array_keys($v)));
            $pdo->prepare("UPDATE case_studies SET $set WHERE id = ?")->execute([...array_values($v), $id]);
        } else {
            $v['sort_order'] = (int)row('SELECT COALESCE(MAX(sort_order), 0) + 1 AS n FROM case_studies')['n'];   // new ones go last
            $cols = implode(', ', array_map(fn($c) => "`$c`", array_keys($v)));
            $pdo->prepare("INSERT INTO case_studies ($cols) VALUES (" . implode(', ', array_fill(0, count($v), '?')) . ')')->execute(array_values($v));
            $id = (int)$pdo->lastInsertId();
        }

        // Existing gallery rows: caption, order, delete
        foreach ((array)($_POST['gal'] ?? []) as $gid => $g) {
            $gid = (int)$gid;
            if (!empty($_POST['gal_del'][$gid])) {
                $pdo->prepare('DELETE FROM case_study_images WHERE id = ? AND case_study_id = ?')->execute([$gid, $id]);
            } else {
                $pdo->prepare('UPDATE case_study_images SET caption = ?, sort_order = ? WHERE id = ? AND case_study_id = ?')
                    ->execute([mb_substr(trim((string)($g['caption'] ?? '')), 0, 190), (int)($g['sort'] ?? 0), $gid, $id]);
            }
        }
        // New gallery uploads (multiple)
        $files = $_FILES['gallery_new'] ?? null;
        $problems = [];
        if ($files && is_array($files['name'])) {
            $next = (int)row('SELECT COALESCE(MAX(sort_order), 0) + 1 AS n FROM case_study_images WHERE case_study_id = ?', [$id])['n'];
            foreach ($files['name'] as $i => $name) {
                if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
                try {
                    $path = store_image(['name' => $name, 'tmp_name' => $files['tmp_name'][$i], 'error' => $files['error'][$i], 'size' => $files['size'][$i]]);
                    $pdo->prepare('INSERT INTO case_study_images (case_study_id, image_url, sort_order) VALUES (?,?,?)')->execute([$id, $path, $next++]);
                } catch (RuntimeException $ex) { $problems[] = "$name: " . $ex->getMessage(); }
            }
        }
        $pdo->commit();
        if ($problems) flash('Saved, but some photos failed: ' . implode(' | ', $problems), 'error'); else flash('Case study saved.');
        header('Location: case_study_edit.php?id=' . $id); exit;
    } catch (RuntimeException $ex) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        flash($ex->getMessage(), 'error');
        header('Location: case_study_edit.php' . ($id ? "?id=$id" : '')); exit;
    }
}

$gallery = $id ? rows('SELECT * FROM case_study_images WHERE case_study_id = ? ORDER BY sort_order, id', [$id]) : [];
$f = fn(string $k) => (string)($cs[$k] ?? '');

admin_head($id ? 'Edit case study' : 'New case study', $id ? $f('title') : 'Fill in what you have; empty sections are simply left out of the page',
    '<a class="btn sec" href="case_studies.php">← All case studies</a>' . ($cs ? '<a class="btn sec" href="../case-study.php?slug=' . e($cs['slug']) . '" target="_blank">View page ↗</a>' : ''));
?>
<form method="post" enctype="multipart/form-data"><?= csrf_field() ?>

<div class="panel"><h2>Basics</h2><p class="hint">Shown on the case study card and as the page banner.</p>
  <div class="grid2">
    <?php fld('Title', 'title', $f('title'), 'text', '', [], true, true);
    fld('Tag (industry · rating)', 'tag', $f('tag'), 'text', 'e.g. Automotive · Credit rating AA+', [], false, true);
    fld('Page address', 'slug', $f('slug'), 'text', $id ? 'Leave as is. Changing it changes the page link.' : 'Leave blank to create it from the title.'); ?>
    <div class="fld full"><label>Main image</label><?php image_field('image_url', $f('image_url')); ?></div>
    <?php fld('Image description (for accessibility)', 'image_alt', $f('image_alt'), 'text', '', [], true); ?>
  </div>
  <label class="chk"><input type="checkbox" name="is_active" value="1"<?= !$cs || $cs['is_active'] ? ' checked' : '' ?>> Published on the website (untick to keep it as a draft)</label>
</div>

<div class="panel"><h2>At a glance</h2><p class="hint">The sidebar facts and the key numbers bar.</p>
  <div class="grid2">
    <?php fld('Client name', 'client_name', $f('client_name')); fld('Location', 'location', $f('location'));
    fld('Service provided', 'service', $f('service')); fld('Duration', 'duration', $f('duration'));
    fld('Key numbers, one per line as value|label', 'metrics', $f('metrics'), 'area', 'Example: ₹150 Cr|credit line', [], true); ?>
  </div>
</div>

<div class="panel"><h2>Full description</h2><p class="hint">The detailed write-up. It appears as the main text of the case study page, below the introduction. Write it like a WordPress post: headings, images from your media library, tables, videos, colours and more.</p>
  <textarea name="body" id="rteField"><?= e(clean_rich_html($f('body'))) ?></textarea>
</div>

<div class="panel"><h2>The story</h2><p class="hint">Plain text. A blank line starts a new paragraph.</p>
  <div class="grid2">
    <?php fld('Introduction (large text at the top)', 'summary', $f('summary'), 'area', '', [], true);
    fld('Challenge', 'challenge', $f('challenge'), 'area', '', [], true); fld('Solution', 'solution', $f('solution'), 'area', '', [], true); fld('Outcome', 'outcome', $f('outcome'), 'area', '', [], true);
    fld('Key results, one per line', 'results', $f('results'), 'area', 'Shown as a checklist.', [], true); ?>
  </div>
</div>

<div class="panel"><h2>Client quote <span class="muted" style="font-weight:400">(optional)</span></h2>
  <div class="grid2">
    <?php fld('Quote', 'quote_text', $f('quote_text'), 'area', '', [], true); fld('Said by', 'quote_author', $f('quote_author')); fld('Role / company', 'quote_role', $f('quote_role')); ?>
  </div>
</div>

<div class="panel"><h2>Photo gallery <span class="muted" style="font-weight:400">(optional)</span></h2>
  <?php if ($gallery): ?><div class="wrap"><table><thead><tr><th>Photo</th><th>Caption</th><th>Order</th><th>Remove</th></tr></thead><tbody>
  <?php foreach ($gallery as $g): ?><tr>
    <td class="num"><img class="thumb" src="<?= e(str_starts_with($g['image_url'], 'media/') ? '../' . $g['image_url'] : $g['image_url']) ?>" alt=""></td>
    <td><input name="gal[<?= (int)$g['id'] ?>][caption]" value="<?= e($g['caption']) ?>" placeholder="Caption (optional)"></td>
    <td style="width:90px"><input type="number" name="gal[<?= (int)$g['id'] ?>][sort]" value="<?= (int)$g['sort_order'] ?>"></td>
    <td class="num"><input type="checkbox" name="gal_del[<?= (int)$g['id'] ?>]" value="1"></td>
  </tr><?php endforeach; ?></tbody></table></div><?php endif; ?>
  <div class="fld" style="margin-top:14px"><label>Add photos</label><input type="file" name="gallery_new[]" multiple accept="image/jpeg,image/png,image/webp,image/gif"><small>Select several at once. JPG, PNG, WebP or GIF, max 5 MB each.</small></div>
</div>

<details class="sec"><summary>Home page card &amp; search engines (optional)</summary><div>
  <div class="grid2" style="padding-top:16px">
    <?php fld('Position on the home page (1 to 3, blank = not shown)', 'home_order', $f('home_order'), 'number');
    fld('Home card tag', 'home_tag', $f('home_tag')); fld('Home card title', 'home_title', $f('home_title'), 'text', '', [], true);
    fld('Home card numbers, one per line as value|label', 'home_kpis', $f('home_kpis'), 'area', '', [], true);
    fld('Google title', 'seo_title', $f('seo_title'), 'text', '', [], true); fld('Google description', 'seo_desc', $f('seo_desc'), 'area', '', [], true); ?>
  </div></div>
</details>

<div class="savebar"><button class="btn gold">Save case study</button><a class="btn sec" href="case_studies.php">Cancel</a></div>
</form>
<?php
require_once __DIR__ . '/../includes/fonts.php';
// Site root URL (so images saved as "media/x.jpg" also preview correctly inside the admin editor)
$siteRoot = (($_SERVER['HTTPS'] ?? 'off') !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\') . '/';
?>
<script src="vendor/tinymce/tinymce.min.js"></script>
<script>
(function () {
  const csrf = <?= json_encode(csrf_token()) ?>;
  const pickImage = cb => window.openMediaPicker(path => cb(path));
  tinymce.init({
    selector: '#rteField',
    license_key: 'gpl',
    base_url: 'vendor/tinymce', suffix: '.min',
    height: 640, min_height: 420, resize: true, branding: false, promotion: false,
    menubar: 'file edit view insert format table tools',
    plugins: 'advlist autolink lists link image media table code fullscreen charmap searchreplace wordcount visualblocks preview insertdatetime anchor',
    toolbar: 'undo redo | blocks fontfamily fontsize lineheight | bold italic underline strikethrough subscript superscript | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link addmedia image media table | blockquote hr | removeformat | visualblocks code preview fullscreen',
    toolbar_mode: 'wrap', toolbar_sticky: true,
    font_size_formats: '10px 12px 14px 16px 18px 20px 22px 24px 28px 32px 36px 42px 48px 60px 72px',
    font_family_formats: <?= json_encode(tinymce_font_formats()) ?>,
    content_css: <?= json_encode(google_fonts_url()) ?>,
    line_height_formats: '1 1.2 1.4 1.6 1.8 2 2.5',
    block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6; Quote=blockquote; Preformatted=pre',
    document_base_url: <?= json_encode($siteRoot) ?>, relative_urls: true, convert_urls: true, remove_script_host: false,
    image_caption: true, image_advtab: false, image_dimensions: false,
    automatic_uploads: true, paste_data_images: true,
    images_upload_handler: blob => new Promise((resolve, reject) => {
      const fd = new FormData(); fd.append('file', blob.blob(), blob.filename());
      fetch('upload_image.php', { method: 'POST', body: fd, headers: { 'X-CSRF-Token': csrf } })
        .then(r => r.json().then(j => ({ ok: r.ok, j })))
        .then(({ ok, j }) => ok && j.location ? resolve(j.location) : reject({ message: j.error || 'Upload failed', remove: true }))
        .catch(() => reject({ message: 'Upload failed. Check your connection.', remove: true }));
    }),
    file_picker_types: 'image',
    file_picker_callback: (cb, value, meta) => { if (meta.filetype === 'image') pickImage(path => cb(path, { alt: '' })); },
    content_style: 'body{font:17px/1.75 "Plus Jakarta Sans",-apple-system,Segoe UI,Arial,sans-serif;color:#1b2a37;padding:12px}h1{font-size:2.4rem;color:#023047}h2{font-size:1.7rem;color:#023047}h3{font-size:1.3rem;color:#023047}h4{font-size:1.1rem;color:#023047}h5{font-size:1rem;color:#023047}h6{font-size:.9rem;color:#023047;text-transform:uppercase}img{max-width:100%;height:auto;border-radius:10px}blockquote{border-left:4px solid #D9A441;margin:1.2em 0;padding-left:1em;font-weight:600}table{border-collapse:collapse;max-width:100%}td,th{border:1px solid #cfd8e1;padding:6px 10px}a{color:#0b4260}',
    setup: editor => {
      editor.ui.registry.addButton('addmedia', {
        icon: 'gallery', text: 'Add media', tooltip: 'Insert an image from the media library',
        onAction: () => pickImage(path => editor.insertContent('<p><img src="' + path + '" alt=""></p>'))
      });
    }
  });
})();
</script>
<?php image_picker_assets(); admin_foot(); ?>
