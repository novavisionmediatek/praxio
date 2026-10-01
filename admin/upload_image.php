<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_once __DIR__ . '/../includes/media.php';

// Image upload endpoint used by the rich-text editor (drag/drop, paste, upload tab).
if (!admin_user()) json_out(['error' => 'Please log in again.'], 401);
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_ok($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) json_out(['error' => 'Session expired. Refresh the page.'], 419);
try {
    $path = store_image($_FILES['file'] ?? ['error' => UPLOAD_ERR_NO_FILE]);
    json_out(['location' => $path]);
} catch (RuntimeException $e) {
    json_out(['error' => $e->getMessage()], 422);
}
