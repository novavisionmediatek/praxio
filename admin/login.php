<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';

if (admin_user()) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fails = $_SESSION['login_fails'] ?? ['n' => 0, 'until' => 0];
    if ($fails['until'] > time()) {
        $error = 'Too many attempts. Try again in a minute.';
    } elseif (!csrf_ok($_POST['csrf'] ?? null)) {
        $error = 'Session expired. Please try again.';
    } else {
        $u = row('SELECT * FROM admins WHERE username = ?', [trim((string)($_POST['username'] ?? ''))]);
        // Always run a hash check so timing does not reveal whether the user exists.
        $hash = $u['password_hash'] ?? password_hash('x', PASSWORD_DEFAULT);
        if ($u && password_verify((string)($_POST['password'] ?? ''), $hash)) {
            session_regenerate_id(true);
            $_SESSION['admin'] = $u['username'];
            unset($_SESSION['login_fails']);
            if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
                db()->prepare('UPDATE admins SET password_hash = ? WHERE id = ?')->execute([password_hash((string)$_POST['password'], PASSWORD_DEFAULT), $u['id']]);
            }
            header('Location: index.php'); exit;
        }
        $fails['n']++;
        if ($fails['n'] >= 5) { $fails = ['n' => 0, 'until' => time() + 60]; }
        $_SESSION['login_fails'] = $fails;
        $error = 'Wrong username or password.';
    }
}
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Sign in · Praxia Admin</title><link rel="stylesheet" href="admin.css">
<style>body{display:grid;place-items:center;min-height:100vh;background:linear-gradient(135deg,#023047,#0b4260)}
.login{background:#fff;border-radius:16px;padding:36px;width:calc(100% - 32px);max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,.3)}
.login .logo{display:flex;align-items:center;gap:10px;font-weight:800;letter-spacing:.14em;color:#023047;margin-bottom:6px}.login .logo svg{width:30px;height:30px}
.login p{color:#6a7b8a;margin:0 0 22px}.login .btn{width:100%;justify-content:center;padding:11px;margin-top:6px}</style></head><body>
<form method="post" class="login"><div class="logo"><svg viewBox="0 0 40 40" fill="none" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 30 20 12l16 18" stroke="#D9A441"/><path d="M4 22 20 4l16 18" stroke="#023047"/><path d="M4 38 20 20l16 18" stroke="#219EBC" opacity=".7"/></svg>PRAXIA</div>
<p>Sign in to manage the website.</p>
<?= $error ? '<div class="flash error">' . e($error) . '</div>' : '' ?><?= csrf_field() ?>
<div class="fld"><label>Username</label><input name="username" required autofocus autocomplete="username"></div>
<div class="fld"><label>Password</label><input name="password" type="password" required autocomplete="current-password"></div>
<button class="btn gold">Sign in</button></form></body></html>