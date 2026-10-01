<?php
/** Icon library for feature cards. Admin picks a name; pages render the matching SVG. */
const ICONS = [
    'card'   => ['Card / balance sheet', '<rect x="4" y="5" width="16" height="14" rx="2"/><path d="M4 10h16M9 15h2"/>'],
    'wave'   => ['Wave / cash flow', '<path d="M3 12c3-6 6-6 9 0s6 6 9 0"/>'],
    'coin'   => ['Coin / capital', '<path d="M12 3v18M17 7H9.5a2.5 2.5 0 0 0 0 5h5a2.5 2.5 0 0 1 0 5H7"/>'],
    'doc'    => ['Document / tax', '<path d="M6 3h9l4 4v14H6zM14 3v5h5M9 13h7M9 17h5"/>'],
    'bolt'   => ['Lightning / speed', '<path d="M13 2 4 14h7l-1 8 9-12h-7z"/>'],
    'target' => ['Target / delivery', '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2"/>'],
    'bars'   => ['Bars / growth', '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>'],
    'team'   => ['People / team', '<circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M3 20a6 6 0 0 1 12 0M15 20a5 5 0 0 1 6 0"/>'],
    'shield' => ['Shield / security', '<path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6z"/>'],
    'chart'  => ['Line chart', '<path d="M4 4v16h16M8 14l3-3 3 2 5-6"/>'],
];

function icon_svg(string $name): string
{
    $svg = ICONS[$name][1] ?? ICONS['card'][1];
    return '<svg viewBox="0 0 24 24">' . $svg . '</svg>';
}

function feature_cards(array $items): void
{
    foreach ($items as $i => $f): ?>
      <div class="feat reveal"<?= $i % 3 ? ' style="--d:' . (($i % 3) * 100) . 'ms"' : '' ?>><div class="icon"><?= icon_svg($f['icon']) ?></div><h3><?= e($f['title']) ?></h3><p><?= e($f['description']) ?></p></div>
    <?php endforeach;
}
