<?php
declare(strict_types=1);
/** Helpers for the shared header and footer. */

/** Only lets through links that are safe to put in an href. */
function safe_url(string $u): string
{
    $u = trim($u);
    if ($u === '' || preg_match('~^\s*(javascript|data|vbscript):~i', $u)) return '#';
    return $u;
}

/** Logo markup: uploaded image (or the built-in mark) plus the optional brand name. $variant is 'header' or 'footer'. */
function site_logo_html(string $variant): string
{
    $img = c('site_logo_img');
    if ($variant === 'footer' && c('site_footer_logo_img') !== '') $img = c('site_footer_logo_img');
    $name = c('site_brand_name');
    if ($img !== '') {
        $h = max(16, min(120, (int)c('site_logo_height') ?: 40));
        $mark = '<img src="' . e($img) . '" alt="' . e($name !== '' ? $name : 'Praxia') . '" style="height:' . $h . 'px">';
    } else {
        $mark = '<svg viewBox="0 0 40 40" fill="none" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 30 20 12l16 18" stroke="#D9A441"/><path d="M4 22 20 4l16 18" stroke="#023047"/><path d="M4 38 20 20l16 18" stroke="#219EBC" opacity=".7"/></svg>';
    }
    return $mark . ($name !== '' ? '<span>' . e($name) . '</span>' : '');
}

/** Active menu links for a location, with the current page flagged. */
function menu_links(string $location): array
{
    global $pageFile;
    $out = [];
    foreach (rows('SELECT label, url, new_tab FROM menu_items WHERE location = ? AND is_active = 1 ORDER BY sort_order, id', [$location]) as $r) {
        $href = safe_url($r['url']);
        $path = basename((string)parse_url($href, PHP_URL_PATH));
        $out[] = [
            'label' => $r['label'], 'href' => $href, 'new_tab' => (bool)$r['new_tab'],
            'active' => !str_contains($href, '#') && !preg_match('~^[a-z]+:~i', $href) && $path === ($pageFile ?? ''),
        ];
    }
    return $out;
}

/** Social icon links that have an address set in admin → Footer. */
function social_links(): array
{
    $icons = [
        'linkedin'  => ['LinkedIn', '<path fill="currentColor" stroke="none" d="M20.45 20.45h-3.56v-5.57c0-1.33-.03-3.04-1.85-3.04-1.86 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.06 2.06 0 1 1 0-4.12 2.06 2.06 0 0 1 0 4.12zM7.12 20.45H3.56V9h3.56v11.45z"/>'],
        'x'         => ['X', '<path fill="currentColor" stroke="none" d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>'],
        'instagram' => ['Instagram', '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".8" fill="currentColor"/>'],
        'facebook'  => ['Facebook', '<path d="M14 9h3V5h-3a4 4 0 0 0-4 4v2H7v4h3v6h4v-6h3l1-4h-4V9z"/>'],
        'youtube'   => ['YouTube', '<rect x="2.5" y="5" width="19" height="14" rx="4"/><path d="m10 9 5 3-5 3z" fill="currentColor"/>'],
    ];
    $out = [];
    foreach ($icons as $key => [$label, $svg]) {
        $url = trim(c('site_social_' . $key));
        if (preg_match('~^https?://~i', $url)) $out[] = ['label' => $label, 'url' => $url, 'svg' => $svg];
    }
    return $out;
}
