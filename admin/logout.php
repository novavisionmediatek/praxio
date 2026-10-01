<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_ok($_POST['csrf'] ?? null)) {
    $_SESSION = [];
    session_destroy();
}
header('Location: login.php');
