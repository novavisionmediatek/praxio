<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';

/**
 * Single endpoint for every public form: quote | proposal | contact | apply.
 * JS posts via fetch (X-Requested-With) and gets JSON; a plain browser post gets redirected back.
 */
$wantsJson = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';

function respond(bool $ok, string $message, int $status = 200): never
{
    global $wantsJson;
    if ($wantsJson) json_out(['ok' => $ok, 'message' => $message], $status);
    $_SESSION['flash'] = $message;
    $back = $_SERVER['HTTP_REFERER'] ?? '../index.php';
    header('Location: ' . $back);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond(false, 'Invalid request.', 405);
if (!csrf_ok($_POST['csrf'] ?? null)) respond(false, 'Your session expired. Please refresh the page and try again.', 419);

// Honeypot: bots fill the hidden field. Pretend success so they do not retry.
if (trim((string)($_POST['website'] ?? '')) !== '') respond(true, 'Thank you! We will be in touch shortly.');

function field(string $k, int $max): string
{
    $v = trim((string)($_POST[$k] ?? ''));
    $v = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $v) ?? '';
    return mb_substr($v, 0, $max);
}

$form  = (string)($_POST['form'] ?? '');
$name  = field('name', 120);
$email = field('email', 190);
$phone = field('phone', 20);

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) respond(false, 'Please enter a valid email address.', 422);
if ($phone !== '' && !preg_match('/^\+?[0-9][0-9 ()\-]{6,18}$/', $phone)) respond(false, 'Please enter a valid phone number.', 422);

$ip = client_ip();
$pdo = db();

// Rate limit: 5 submissions per IP per 10 minutes, across both tables.
$recent = (int)row('SELECT (SELECT COUNT(*) FROM enquiries WHERE ip = ? AND created_at > (NOW() - INTERVAL 10 MINUTE))
                         + (SELECT COUNT(*) FROM applications WHERE ip = ? AND created_at > (NOW() - INTERVAL 10 MINUTE)) AS n', [$ip, $ip])['n'];
if ($recent >= 5) respond(false, 'Too many submissions. Please try again in a few minutes.', 429);

$notify = '';

if (in_array($form, ['quote', 'proposal', 'contact', 'popup'], true)) {
    $company = field('company', 160);
    $message = field('message', 3000);

    if ($form === 'quote' && $phone === '') respond(false, 'Please enter your phone number.', 422);
    if ($form === 'popup' && ($name === '' || $email === '' || $phone === '')) respond(false, 'Please enter your name, email and phone number.', 422);
    if ($form === 'proposal' && ($name === '' || $company === '' || $email === '' || $phone === '')) respond(false, 'Please fill in all required fields.', 422);
    if ($form === 'contact' && ($name === '' || $company === '' || $email === '')) respond(false, 'Please fill in all required fields.', 422);

    $st = $pdo->prepare('INSERT INTO enquiries (source, name, company, email, phone, asset_type, asset_value, audience, message, ip)
                         VALUES (?,?,?,?,?,?,?,?,?,?)');
    $st->execute([
        $form, $name ?: null, $company ?: null, $email ?: null, $phone ?: null,
        field('asset_type', 80) ?: null, field('asset_value', 40) ?: null, field('audience', 80) ?: null,
        $message ?: null, $ip,
    ]);
    $notify = "New {$form} enquiry\n\nName: $name\nCompany: $company\nEmail: $email\nPhone: $phone\n"
            . 'Asset: ' . field('asset_type', 80) . ' ' . field('asset_value', 40) . "\nMessage: $message\n";
    $okMsg = $form === 'popup' ? 'Thank you! Our team will contact you within one working day.' : ($form === 'quote' ? 'Thanks! We will call you shortly with your quote.' : "Thank you! We'll reach out within one working day.");

} elseif ($form === 'apply') {
    if ($name === '' || $email === '') respond(false, 'Please fill in your name and email.', 422);

    $jobId = (int)($_POST['job_id'] ?? 0) ?: null;
    if ($jobId !== null && !row('SELECT id FROM job_openings WHERE id = ? AND is_active = 1', [$jobId])) $jobId = null;

    $cvFile = $cvOriginal = null;
    $f = $_FILES['cv'] ?? null;
    if (!$f || $f['error'] === UPLOAD_ERR_NO_FILE) respond(false, 'Please attach your CV.', 422);
    if ($f['error'] !== UPLOAD_ERR_OK) respond(false, 'CV upload failed. Please try a smaller file.', 422);
    if ($f['size'] > config('max_cv_bytes')) respond(false, 'CV is too large (max ' . (int)(config('max_cv_bytes') / 1048576) . ' MB).', 422);

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    $allowed = [
        'application/pdf' => 'pdf',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
    ];
    if (!isset($allowed[$mime])) respond(false, 'CV must be a PDF, DOC or DOCX file.', 422);

    $cvFile = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($f['tmp_name'], ROOT . '/uploads/' . $cvFile)) respond(false, 'Could not save your CV. Please try again.', 500);
    $cvOriginal = mb_substr(preg_replace('/[^\w.\- ]/u', '_', basename((string)$f['name'])), 0, 190);

    $st = $pdo->prepare('INSERT INTO applications (job_id, name, email, phone, message, cv_file, cv_original, ip) VALUES (?,?,?,?,?,?,?,?)');
    $st->execute([$jobId, $name, $email, $phone ?: null, field('message', 3000) ?: null, $cvFile, $cvOriginal, $ip]);
    $notify = "New job application\n\nName: $name\nEmail: $email\nPhone: $phone\n";
    $okMsg = 'Application received. Thank you for your interest in Praxia!';

} else {
    respond(false, 'Unknown form.', 400);
}

if (config('mail_notifications') && setting('notification_email') !== '') {
    @mail(setting('notification_email'), 'Praxia website: ' . $form, $notify,
        'From: ' . config('mail_from') . "\r\nReply-To: " . ($email ?: config('mail_from')));
}

respond(true, $okMsg);
