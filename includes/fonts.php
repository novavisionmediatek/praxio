<?php
declare(strict_types=1);
/**
 * Fonts offered in the admin rich-text editor.
 * name => [CSS fallback stack, loaded from Google Fonts?]. System fonts need no download.
 */
const EDITOR_FONTS = [
    // Sans-serif
    'Plus Jakarta Sans' => ['sans-serif', true], 'Inter' => ['sans-serif', true], 'Roboto' => ['sans-serif', true], 'Open Sans' => ['sans-serif', true],
    'Lato' => ['sans-serif', true], 'Montserrat' => ['sans-serif', true], 'Poppins' => ['sans-serif', true], 'Nunito' => ['sans-serif', true],
    'Raleway' => ['sans-serif', true], 'Source Sans 3' => ['sans-serif', true], 'Work Sans' => ['sans-serif', true], 'DM Sans' => ['sans-serif', true],
    'Manrope' => ['sans-serif', true], 'Outfit' => ['sans-serif', true], 'Rubik' => ['sans-serif', true], 'Mulish' => ['sans-serif', true],
    'Quicksand' => ['sans-serif', true], 'Barlow' => ['sans-serif', true], 'Josefin Sans' => ['sans-serif', true], 'Ubuntu' => ['sans-serif', true],
    // Serif
    'Merriweather' => ['serif', true], 'Playfair Display' => ['serif', true], 'Lora' => ['serif', true], 'PT Serif' => ['serif', true],
    'Libre Baskerville' => ['serif', true], 'Cormorant Garamond' => ['serif', true], 'Crimson Text' => ['serif', true], 'Roboto Slab' => ['serif', true],
    'EB Garamond' => ['serif', true],
    // Display / handwriting
    'Oswald' => ['sans-serif', true], 'Bebas Neue' => ['sans-serif', true], 'Anton' => ['sans-serif', true], 'Abril Fatface' => ['serif', true],
    'Dancing Script' => ['cursive', true], 'Pacifico' => ['cursive', true], 'Caveat' => ['cursive', true], 'Lobster' => ['cursive', true],
    // Monospace
    'Roboto Mono' => ['monospace', true], 'Source Code Pro' => ['monospace', true], 'Fira Code' => ['monospace', true],
    // System fonts (use whatever the visitor's device has)
    'Arial' => ['Helvetica, sans-serif', false], 'Helvetica' => ['Arial, sans-serif', false], 'Verdana' => ['Geneva, sans-serif', false],
    'Tahoma' => ['Geneva, sans-serif', false], 'Trebuchet MS' => ['Helvetica, sans-serif', false], 'Georgia' => ['serif', false],
    'Times New Roman' => ['Times, serif', false], 'Garamond' => ['serif', false], 'Courier New' => ['Courier, monospace', false],
    'Impact' => ['Charcoal, sans-serif', false], 'Comic Sans MS' => ['cursive', false],
];

/**
 * Google Fonts stylesheet URL for the given font names (or all Google fonts when null).
 * Uses the v1 API because it quietly skips weights/italics a font does not have (v2 rejects the whole request).
 */
function google_fonts_url(?array $names = null): string
{
    $parts = [];
    foreach (EDITOR_FONTS as $name => [$_, $google]) {
        if (!$google || ($names !== null && !in_array($name, $names, true))) continue;
        $parts[] = str_replace(' ', '+', $name) . ':400,400i,700,700i';
    }
    return $parts ? 'https://fonts.googleapis.com/css?family=' . implode('|', $parts) . '&display=swap' : '';
}
/** TinyMCE font_family_formats string. */
function tinymce_font_formats(): string
{
    $out = [];
    foreach (EDITOR_FONTS as $name => [$fallback]) $out[] = $name . '=' . strtolower($name) . ',' . $fallback;
    return implode('; ', $out);
}

/** Names of editor fonts that appear in font-family declarations inside $html (so the public page loads only those). */
function fonts_used_in(string $html): array
{
    $used = [];
    if (preg_match_all('/font-family:\s*([^;"]+)/i', $html, $m)) {
        foreach ($m[1] as $stack) {
            $first = strtolower(trim(explode(',', $stack)[0], " '\""));
            foreach (EDITOR_FONTS as $name => $_) if (strtolower($name) === $first) $used[$name] = true;
        }
    }
    return array_keys($used);
}
