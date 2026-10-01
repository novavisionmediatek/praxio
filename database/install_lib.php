<?php
declare(strict_types=1);
/**
 * Idempotent installer/migrator used by setup.php (first install) and migrate.php (after updates).
 * Creates missing tables/columns and fills content tables that are still empty. Never overwrites existing content.
 */
function praxia_install(PDO $pdo, string $dbName, string $root): void
{
    $dbName = str_replace('`', '', $dbName);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbName`");

    foreach (array_filter(array_map('trim', explode(';', file_get_contents($root . '/database/schema.sql')))) as $sql) {
        $sql = trim(preg_replace('/^--.*$/m', '', $sql));
        if ($sql !== '') $pdo->exec($sql);
    }

    // Columns added after the first release (CREATE TABLE IF NOT EXISTS will not add them to old tables).
    $addColumn = function (string $table, string $col, string $ddl) use ($pdo, $dbName) {
        $st = $pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?');
        $st->execute([$dbName, $table, $col]);
        if (!(int)$st->fetchColumn()) $pdo->exec("ALTER TABLE `$table` ADD COLUMN `$col` $ddl");
    };
    $addColumn('team_members', 'photo_url', 'VARCHAR(500) NULL AFTER role');
    $addColumn('partners', 'logo_url', 'VARCHAR(500) NULL AFTER name');
    $addColumn('case_studies', 'slug', 'VARCHAR(160) NULL UNIQUE AFTER id');
    $addColumn('case_studies', 'body', 'MEDIUMTEXT NULL AFTER slug');
    $addColumn('case_studies', 'summary', 'TEXT NULL');
    $addColumn('case_studies', 'client_name', 'VARCHAR(160) NULL');
    $addColumn('case_studies', 'location', 'VARCHAR(160) NULL');
    $addColumn('case_studies', 'service', 'VARCHAR(160) NULL');
    $addColumn('case_studies', 'duration', 'VARCHAR(80) NULL');
    $addColumn('case_studies', 'results', 'TEXT NULL');
    $addColumn('case_studies', 'quote_text', 'TEXT NULL');
    $addColumn('case_studies', 'quote_author', 'VARCHAR(120) NULL');
    $addColumn('case_studies', 'quote_role', 'VARCHAR(160) NULL');
    $addColumn('case_studies', 'seo_title', 'VARCHAR(190) NULL');
    $addColumn('case_studies', 'seo_desc', 'VARCHAR(300) NULL');

    $pdo->exec("ALTER TABLE enquiries MODIFY source ENUM('quote','proposal','contact','popup') NOT NULL");   // adds 'popup' on older installs

    $seed = require $root . '/database/seed.php';
    $pdo->beginTransaction();

    $ins = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_key = setting_key');
    foreach ($seed['settings'] as $k => $v) $ins->execute([$k, $v]);

    $empty = fn(string $t) => (int)$pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn() === 0;
    $fill = function (string $table, string $cols, array $rows) use ($pdo, $empty) {
        if (!$empty($table)) return;
        $n = count(explode(',', $cols)) ;
        $st = $pdo->prepare("INSERT INTO `$table` ($cols) VALUES (" . implode(',', array_fill(0, $n, '?')) . ')');
        foreach ($rows as $i => $r) $st->execute([...$r, $i]);
    };

    $fill('partners', 'name, sort_order', array_map(fn($n) => [$n], $seed['partners']));
    $fill('asset_categories', 'placement, title, description, image_url, sort_order', $seed['asset_categories']);
    $fill('stats', 'label, stat_value, prefix, suffix, sort_order', $seed['stats']);
    $fill('case_studies', 'image_url, image_alt, tag, title, metrics, challenge, solution, outcome, home_order, home_tag, home_title, home_kpis, sort_order', $seed['case_studies']);
    $fill('team_members', 'tier, initials, name, role, bio, sort_order', $seed['team_members']);
    $fill('investors', 'name, description, sort_order', $seed['investors']);
    $fill('testimonials', 'quote, author, designation, initials, sort_order', $seed['testimonials']);
    $fill('faqs', 'category, question, answer, show_on_home, home_answer, sort_order',
        array_map(fn($f) => [$f[0], $f[1], $f[2], $f[3] !== null ? 1 : 0, $f[3]], $seed['faqs']));
    $fill('job_openings', 'title, department, job_type, description, sort_order', $seed['job_openings']);
    $fill('menu_items', 'location, label, url, sort_order', $seed['menu_items']);
    $fill('features', 'placement, icon, title, description, sort_order', $seed['features']);
    $fill('steps', 'placement, title, description, sort_order', $seed['steps']);
    $fill('compare_rows', 'aspect, loan_text, lease_text, sort_order', $seed['compare_rows']);

    // Give every case study a URL slug for its own page.
    foreach ($pdo->query("SELECT id, title FROM case_studies WHERE slug IS NULL OR slug = ''")->fetchAll() as $r) {
        $base = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $r['title']), '-')) ?: 'case-study';
        $base = substr($base, 0, 120); $slug = $base; $n = 2;
        $chk = $pdo->prepare('SELECT COUNT(*) FROM case_studies WHERE slug = ? AND id <> ?');
        while (true) { $chk->execute([$slug, $r['id']]); if (!(int)$chk->fetchColumn()) break; $slug = $base . '-' . $n++; }
        $pdo->prepare('UPDATE case_studies SET slug = ? WHERE id = ?')->execute([$slug, $r['id']]);
    }

    $pdo->commit();
}
