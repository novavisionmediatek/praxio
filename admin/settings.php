<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_admin();

$keys = [
    'contact_email' => ['Contact email', 'Shown in the top bar, footer and Contact page.'],
    'contact_phone' => ['Contact phone', 'Shown in the top bar, footer and Contact page.'],
    'company_legal_name' => ['Company legal name', 'Shown in the footer and Contact page.'],
    'notification_email' => ['Send new enquiries to', 'Needs "mail_notifications" turned on in config/config.php and a working mail server.'],
    'team_size' => ['Team size', 'The "team members" counter on the About page.'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_csrf();
    if (($_POST['action'] ?? '') === 'settings') {
        $email = trim((string)($_POST['contact_email'] ?? ''));
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) { flash('Contact email is not a valid email address.', 'error'); header('Location: settings.php'); exit; }
        $st = db()->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
        foreach ($keys as $k => $_) $st->execute([$k, trim((string)($_POST[$k] ?? ''))]);
        flash('Settings saved.');
    } elseif (($_POST['action'] ?? '') === 'password') {
        $u = row('SELECT * FROM admins WHERE username = ?', [admin_user()]);
        $new = (string)($_POST['new'] ?? '');
        if (!$u || !password_verify((string)($_POST['current'] ?? ''), $u['password_hash'])) flash('Current password is wrong.', 'error');
        elseif (strlen($new) < 8) flash('New password must be at least 8 characters.', 'error');
        elseif ($new !== (string)($_POST['confirm'] ?? '')) flash('The two new passwords do not match.', 'error');
        else {
            db()->prepare('UPDATE admins SET password_hash = ? WHERE id = ?')->execute([password_hash($new, PASSWORD_DEFAULT), $u['id']]);
            flash('Password changed.');
        }
    }
    header('Location: settings.php'); exit;
}

admin_head('Settings', 'Contact details and your admin account');
?>
<form method="post" class="panel"><?= csrf_field() ?><input type="hidden" name="action" value="settings">
  <h2>Website details</h2><p class="hint">Used across the whole website.</p>
  <div class="grid2"><?php foreach ($keys as $k => [$label, $hint]) fld($label, $k, setting($k), 'text', $hint); ?></div>
  <div class="savebar"><button class="btn gold">Save settings</button></div>
</form>

<form method="post" class="panel"><?= csrf_field() ?><input type="hidden" name="action" value="password">
  <h2>Change password</h2><p class="hint">Signed in as <b><?= e(admin_user()) ?></b>.</p>
  <div class="grid2">
    <div class="fld full"><label>Current password</label><input type="password" name="current" required autocomplete="current-password"></div>
    <div class="fld"><label>New password</label><input type="password" name="new" required minlength="8" autocomplete="new-password"><small>At least 8 characters.</small></div>
    <div class="fld"><label>Confirm new password</label><input type="password" name="confirm" required minlength="8" autocomplete="new-password"></div>
  </div>
  <button class="btn">Change password</button>
</form>
<?php admin_foot(); ?>
