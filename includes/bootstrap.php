<?php
declare(strict_types=1);

define('ROOT', dirname(__DIR__));
if (!is_file(ROOT . '/config/config.php')) {
    http_response_code(500);
    exit('Missing config/config.php. Copy config/config.sample.php to config/config.php, then open setup.php.');
}
$CONFIG = require ROOT . '/config/config.php';

ini_set('display_errors', $CONFIG['debug'] ? '1' : '0');
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

function config(string $key, $default = null)
{
    global $CONFIG;
    return $CONFIG[$key] ?? $default;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo) return $pdo;
    $c = config('db');
    $dsn = "mysql:host={$c['host']};port={$c['port']};dbname={$c['name']};charset={$c['charset']}";
    try {
        $pdo = new PDO($dsn, $c['user'], $c['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        error_log('Praxia DB connection failed: ' . $e->getMessage());
        if (!is_file(ROOT . '/config/installed.lock')) {
            exit('Database not set up yet. Start MySQL in XAMPP, then open <a href="/' . e(basename(ROOT)) . '/setup.php">setup.php</a>.');
        }
        exit('Database connection failed. Check config/config.php and that MySQL is running.');
    }
    return $pdo;
}

function e($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function rows(string $sql, array $params = []): array
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}

function row(string $sql, array $params = []): ?array
{
    $st = db()->prepare($sql);
    $st->execute($params);
    $r = $st->fetch();
    return $r ?: null;
}

function all_settings(): array
{
    static $all = null;
    if ($all === null) {
        $all = [];
        foreach (rows('SELECT setting_key, setting_value FROM settings') as $r) {
            $all[$r['setting_key']] = $r['setting_value'];
        }
    }
    return $all;
}

function setting(string $key, string $default = ''): string
{
    return all_settings()[$key] ?? $default;
}

/** Page copy / image path from admin "Page content", falling back to the default in content_defs.php. */
function c(string $key): string
{
    static $defs = null;
    $all = all_settings();
    if (array_key_exists($key, $all)) return $all[$key];
    if ($defs === null) {
        $defs = [];
        foreach (require ROOT . '/includes/content_defs.php' as $page) {
            foreach ($page['fields'] as $k => $f) $defs[$k] = $f[2];
        }
    }
    return $defs[$key] ?? '';
}

/** Escaped text where *word* becomes a highlighted span. */
function ch(string $key): string
{
    return preg_replace('/\*([^*]+)\*/', '<span class="hl">$1</span>', e(c($key)));
}

/** Escaped content field as a list of lines. */
function cl(string $key): array
{
    return lines(c($key));
}

/** Escaped, ready-to-use image URL (absolute URLs and uploaded media/... paths both work). */
function img(string $v): string
{
    return e($v);
}
/** "value|label" per line -> [[value,label],...] */
function pairs(?string $text): array
{
    $out = [];
    foreach (preg_split('/\R/', trim((string)$text)) as $line) {
        if (trim($line) === '') continue;
        [$v, $l] = array_pad(explode('|', $line, 2), 2, '');
        $out[] = [trim($v), trim($l)];
    }
    return $out;
}

function lines(?string $text): array
{
    return array_values(array_filter(array_map('trim', preg_split('/\R/', (string)$text)), 'strlen'));
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function csrf_ok(?string $token): bool
{
    return is_string($token) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

function client_ip(): string
{
    return substr($_SERVER['REMOTE_ADDR'] ?? '', 0, 45);
}

function json_out(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function slugify(string $s): string
{
    $s = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $s) ?? '', '-'));
    return substr($s !== '' ? $s : 'case-study', 0, 120);
}

/** Returns $base, or $base-2, $base-3… so it does not clash with another case study's slug. */
function unique_case_slug(string $base, int $exceptId = 0): string
{
    $slug = $base; $n = 2;
    while (row('SELECT id FROM case_studies WHERE slug = ? AND id <> ?', [$slug, $exceptId])) $slug = $base . '-' . $n++;
    return $slug;
}

/** Plain text -> escaped <p> paragraphs (blank line = new paragraph, single newline = <br>). */
function paras(?string $text): string
{
    $out = '';
    foreach (preg_split('/\R{2,}/', trim((string)$text)) as $p) {
        if (trim($p) !== '') $out .= '<p>' . nl2br(e(trim($p))) . '</p>';
    }
    return $out;
}

/** Keeps only harmless inline CSS declarations (alignment, colours, sizes, spacing). */
function clean_inline_style(string $style): string
{
    $okProps = ['font-family', 'text-align', 'color', 'background-color', 'font-size', 'font-weight', 'font-style', 'text-decoration', 'width', 'height', 'max-width',
                'float', 'margin', 'margin-left', 'margin-right', 'margin-top', 'margin-bottom', 'padding', 'padding-left', 'padding-right', 'padding-top',
                'padding-bottom', 'border', 'border-collapse', 'border-width', 'border-style', 'border-color', 'vertical-align', 'display', 'line-height', 'text-indent'];
    $out = [];
    foreach (explode(';', $style) as $decl) {
        if (!str_contains($decl, ':')) continue;
        [$p, $v] = array_map('trim', explode(':', $decl, 2));
        $p = strtolower($p);
        if (!in_array($p, $okProps, true) || $v === '' || strlen($v) > 80) continue;
        $charset = $p === 'font-family' ? '/^[a-z0-9 ,\'\-]+$/i' : '/^[#a-z0-9%.,()\s\-+]+$/i';
        if (!preg_match($charset, $v) || preg_match('/url|expression|javascript|import/i', $v)) continue;
        if ($p === 'display' && !in_array(strtolower($v), ['block', 'inline-block', 'table', 'inline'], true)) continue;
        $out[] = "$p: $v";
    }
    return implode('; ', $out);
}

/**
 * Cleans HTML from the admin rich-text editor down to a safe allowlist (no scripts, event handlers, odd links or unknown embeds).
 * Plain text without tags is turned into paragraphs. Safe to run again on already-clean HTML.
 */
function clean_rich_html(?string $html): string
{
    $html = trim((string)$html);
    if ($html === '') return '';
    if (!preg_match('/<[a-z][^>]*>/i', $html)) return paras($html);

    $allowed = ['p', 'br', 'strong', 'em', 'u', 's', 'sub', 'sup', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'a', 'blockquote', 'img', 'figure', 'figcaption',
                'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td', 'caption', 'hr', 'span', 'pre', 'code', 'iframe'];
    $rename  = ['b' => 'strong', 'i' => 'em', 'strike' => 's', 'del' => 's', 'div' => 'p'];
    $drop    = ['script', 'style', 'object', 'embed', 'form', 'input', 'button', 'textarea', 'select', 'svg', 'math', 'link', 'meta', 'head', 'title', 'noscript', 'template'];
    $attrOk  = [
        'a' => ['href', 'title'], 'img' => ['src', 'alt', 'title', 'width', 'height'], 'td' => ['colspan', 'rowspan'], 'th' => ['colspan', 'rowspan', 'scope'],
        'table' => ['border', 'cellpadding', 'cellspacing'], 'iframe' => ['src', 'title'],
    ];
    $embedHost = '~^https://(www\.youtube(-nocookie)?\.com/embed/|player\.vimeo\.com/video/)~i';

    $doc = new DOMDocument('1.0', 'UTF-8');
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="UTF-8"><body>' . $html . '</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    $body = $doc->getElementsByTagName('body')->item(0);
    if (!$body) return '';

    $walk = function (DOMNode $node) use (&$walk, $allowed, $rename, $drop, $attrOk, $embedHost, $doc): void {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child->nodeType === XML_COMMENT_NODE) { $node->removeChild($child); continue; }
            if ($child->nodeType !== XML_ELEMENT_NODE) continue;

            $orig = strtolower($child->nodeName);
            if (in_array($orig, $drop, true)) { $node->removeChild($child); continue; }
            $walk($child);
            $tag = $rename[$orig] ?? $orig;
            if (!in_array($tag, $allowed, true)) {                       // unknown tag: keep its content, drop the tag itself
                while ($child->firstChild) $node->insertBefore($child->firstChild, $child);
                $node->removeChild($child);
                continue;
            }
            if ($tag !== $orig) {
                $new = $doc->createElement($tag);
                foreach (iterator_to_array($child->attributes) as $at) $new->setAttribute($at->nodeName, $at->nodeValue);
                while ($child->firstChild) $new->appendChild($child->firstChild);
                $node->replaceChild($new, $child);
                $child = $new;
            }

            $keep = [];
            foreach (($attrOk[$tag] ?? []) as $name) if ($child->hasAttribute($name)) $keep[$name] = trim($child->getAttribute($name));
            $style = clean_inline_style($child->getAttribute('style'));
            foreach (iterator_to_array($child->attributes) as $at) $child->removeAttributeNode($at);

            if ($tag === 'a') {
                if (isset($keep['href']) && preg_match('~^(https?://|mailto:|tel:|/|#|[a-z0-9_./-]+\.php)~i', $keep['href'])) {
                    $child->setAttribute('href', $keep['href']);
                    if (preg_match('~^https?://~i', $keep['href'])) { $child->setAttribute('target', '_blank'); $child->setAttribute('rel', 'noopener noreferrer'); }
                    if (!empty($keep['title'])) $child->setAttribute('title', $keep['title']);
                }
            } elseif ($tag === 'img') {
                if (!isset($keep['src']) || !preg_match('~^(https?://|media/|/)~i', $keep['src'])) { $node->removeChild($child); continue; }
                $child->setAttribute('src', $keep['src']);
                $child->setAttribute('alt', $keep['alt'] ?? '');
                $child->setAttribute('loading', 'lazy');
                foreach (['width', 'height'] as $dim) if (isset($keep[$dim]) && ctype_digit($keep[$dim])) $child->setAttribute($dim, $keep[$dim]);
                if (!empty($keep['title'])) $child->setAttribute('title', $keep['title']);
            } elseif ($tag === 'iframe') {
                if (!isset($keep['src']) || !preg_match($embedHost, $keep['src'])) { $node->removeChild($child); continue; }
                $child->setAttribute('src', $keep['src']);
                $child->setAttribute('allowfullscreen', 'allowfullscreen');
                $child->setAttribute('loading', 'lazy');
                $child->setAttribute('title', $keep['title'] ?? 'Embedded video');
                $child->appendChild($doc->createTextNode(''));           // forces <iframe></iframe> instead of a self-closing tag
                $style = '';
            } else {
                foreach (['colspan', 'rowspan', 'border', 'cellpadding', 'cellspacing', 'scope'] as $n) {
                    if (isset($keep[$n]) && preg_match('/^[a-z0-9]+$/i', $keep[$n])) $child->setAttribute($n, $keep[$n]);
                }
            }
            if ($style !== '') $child->setAttribute('style', $style);
        }
    };
    $walk($body);

    $out = '';
    foreach ($body->childNodes as $n) $out .= $doc->saveHTML($n);
    $out = preg_replace('~<p(?:\s[^>]*)?>(?:\s|\x{00A0}|<br\s*/?>|&nbsp;)*</p>~iu', '', $out);   // drop empty paragraphs
    return trim((string)$out);
}
