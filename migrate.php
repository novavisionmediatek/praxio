<?php
declare(strict_types=1);
/**
 * Applies database updates after you copy a newer version of the site over an existing install.
 * Safe to run any number of times: adds missing tables/columns and fills only EMPTY content tables.
 * Browser: log in to /admin/ first, then open /migrate.php.   CLI: php migrate.php
 */
require_once __DIR__ . '/includes/bootstrap.php';
if (PHP_SAPI !== 'cli' && empty($_SESSION['admin'])) {
    http_response_code(403);
    exit('Log in to the admin panel first, then reload this page.');
}
require __DIR__ . '/database/install_lib.php';
try {
    praxia_install(db(), config('db')['name'], ROOT);
    echo PHP_SAPI === 'cli' ? "Database is up to date.\n" : 'Database is up to date. <a href="admin/">Back to admin</a>';
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Migration failed: ' . e($e->getMessage());
}
