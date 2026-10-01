<?php
declare(strict_types=1);
/**
 * One-time installer: creates the database + tables, loads the starting content and creates the admin login.
 *   Browser: http://localhost/praxia/setup.php
 *   CLI:     php setup.php admin "YourPassword"
 * Locks itself afterwards (config/installed.lock). Delete that file to run it again.
 */
$root = __DIR__;
if (!is_file($root . '/config/config.php')) {
    http_response_code(500);
    exit('Missing config/config.php. Copy config/config.sample.php to config/config.php, edit the database login if needed, then reload this page.');
}
$cfg = require $root . '/config/config.php';
$lock = $root . '/config/installed.lock';
$cli = PHP_SAPI === 'cli';

function out(string $m, bool $cli): void { echo $cli ? $m . "\n" : '<p>' . htmlspecialchars($m) . '</p>'; }

if (is_file($lock)) {
    http_response_code(403);
    exit($cli ? "Already installed. Delete config/installed.lock to run again.\n" : 'Already installed. Delete config/installed.lock to run setup again.');
}

$error = '';
$done = false;
$user = $cli ? ($argv[1] ?? '') : trim((string)($_POST['username'] ?? ''));
$pass = $cli ? ($argv[2] ?? '') : (string)($_POST['password'] ?? '');
$submitted = $cli ? ($user !== '' || $pass !== '') : $_SERVER['REQUEST_METHOD'] === 'POST';

if ($submitted) {
    if (!preg_match('/^[A-Za-z0-9_.-]{3,40}$/', $user)) $error = 'Username must be 3-40 letters, numbers or _ . -';
    elseif (strlen($pass) < 8) $error = 'Password must be at least 8 characters.';

    if (!$error) {
        try {
            $c = $cfg['db'];
            $pdo = new PDO("mysql:host={$c['host']};port={$c['port']};charset={$c['charset']}", $c['user'], $c['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            require $root . '/database/install_lib.php';
            praxia_install($pdo, $c['name'], $root);
            $pdo->exec('USE `' . str_replace('`', '', $c['name']) . '`');
            $pdo->beginTransaction();

            $pdo->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?) ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)')
                ->execute([$user, password_hash($pass, PASSWORD_DEFAULT)]);

            $pdo->commit();
            file_put_contents($lock, date('c'));
            $done = true;
        } catch (Throwable $e) {
            if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
            $error = 'Setup failed: ' . $e->getMessage() . ' (Is MySQL running in XAMPP? Check config/config.php.)';
        }
    }
}

if ($cli) {
    if ($done) out("Installed. Admin user '$user' created. Open /admin/ in your browser.", true);
    else out($error ?: 'Usage: php setup.php <admin-username> <admin-password>', true);
    exit($done ? 0 : 1);
}
?><!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Praxia setup</title>
<style>body{font-family:system-ui,sans-serif;background:#f4f6f8;display:grid;place-items:center;min-height:100vh;margin:0}
.box{background:#fff;padding:36px;border-radius:16px;max-width:420px;width:calc(100% - 32px);box-shadow:0 10px 40px rgba(2,48,71,.12)}
h1{margin:0 0 6px;font-size:1.4rem;color:#023047}p{color:#556;line-height:1.5}label{display:block;margin:14px 0 6px;font-weight:600;font-size:.9rem}
input{width:100%;padding:11px 12px;border:1px solid #cdd5dc;border-radius:8px;font-size:1rem;box-sizing:border-box}
button{margin-top:20px;width:100%;padding:12px;border:0;border-radius:8px;background:#D9A441;color:#023047;font-weight:800;font-size:1rem;cursor:pointer}
.err{background:#fde8e8;color:#b32d2d;padding:10px 12px;border-radius:8px}.ok{background:#e3f6ee;color:#17734f;padding:10px 12px;border-radius:8px}</style></head><body><div class="box">
<h1>Praxia setup</h1>
<?php if ($done): ?>
  <p class="ok">Database <b><?= htmlspecialchars($cfg['db']['name']) ?></b> created and filled. Admin user <b><?= htmlspecialchars($user) ?></b> is ready.</p>
  <p><a href="admin/">Go to the admin panel</a> · <a href="index.php">View the site</a></p>
<?php else: ?>
  <p>Creates the MySQL database, tables and starting content, and your admin login. Uses the credentials in <code>config/config.php</code>.</p>
  <?php if ($error): ?><p class="err"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <form method="post">
    <label>Admin username</label><input name="username" required value="<?= htmlspecialchars($user) ?>" pattern="[A-Za-z0-9_.\-]{3,40}">
    <label>Admin password (min 8 characters)</label><input name="password" type="password" required minlength="8">
    <button type="submit">Install</button>
  </form>
<?php endif; ?>
</div></body></html>
