<?php

require_once get_stylesheet_directory() . '/core/ScriptLoader.php';

use BricksChild\Core\ScriptLoader;

/**
 * Registrace scriptů a stylů
 */
$scripts = ScriptLoader::getInstance();

// Konfigurace pro animace
$scripts->addScriptGroup('animations', [
    'condition' => fn() => is_front_page() || is_single(),
    'scripts' => [
        'anime' => [
            'path' => '/frontend/assets/js/anime.min.js',
            'deps' => ['jquery'],
            'in_footer' => true
        ]
    ]
]);

// Základní styly
$scripts->addStyle(
    'index-style',
    '/frontend/assets/css/index.style.css',
    ['bricks-frontend']
);

// Vlastní verze pro externí knihovny
$scripts->setVersion('anime', '3.2.1');

// Enqueue assets
add_action('wp_enqueue_scripts', [$scripts, 'enqueue']);

/**
 * Backend includes configuration
 */
$includes = [
    // Features
    [
        'path' => '/backend/assets/includes/features/disableAdminNotice.php',
        'priority' => 1
    ],
    [
        'path' => '/backend/assets/includes/features/mediaOptimizer.php',
        'priority' => 5
    ],
    // Builder
    [
        'path' => '/backend/assets/includes/builder/RegisterColors.php',
        'priority' => 10
    ]
];

// Seřazení podle priority
usort($includes, fn($a, $b) => ($a['priority'] ?? 10) <=> ($b['priority'] ?? 10));

// Načtení PHP souborů
foreach ($includes as $file) {
    $file_path = get_stylesheet_directory() . $file['path'];
    if (file_exists($file_path)) {
        require_once $file_path;
    }
}

