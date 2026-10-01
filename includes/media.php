<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

const MEDIA_MAX_BYTES = 5 * 1024 * 1024;

/**
 * Saves an uploaded image from $_FILES[$field] into /media and the media library table.
 * Returns the relative path ("media/abc.jpg"), null if no file was sent, or throws RuntimeException with a user-facing message.
 */
function save_image_upload(string $field): ?string
{
    $f = $_FILES[$field] ?? null;
    if (!$f || $f['error'] === UPLOAD_ERR_NO_FILE) return null;
    return store_image($f);
}

function store_image(array $f): string
{
    if ($f['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Image upload failed (error ' . $f['error'] . '). Try a smaller file.');
    if ($f['size'] > MEDIA_MAX_BYTES) throw new RuntimeException('Image is too large (max 5 MB).');

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    if (!isset($allowed[$mime]) || @getimagesize($f['tmp_name']) === false) throw new RuntimeException('Only JPG, PNG, WebP or GIF images are allowed.');

    $name = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($f['tmp_name'], ROOT . '/media/' . $name)) throw new RuntimeException('Could not save the image. Check that the media folder is writable.');

    $orig = mb_substr(preg_replace('/[^\w.\- ]/u', '_', basename((string)$f['name'])), 0, 190);
    db()->prepare('INSERT INTO media (file_path, original_name) VALUES (?, ?)')->execute(['media/' . $name, $orig]);
    return 'media/' . $name;
}
