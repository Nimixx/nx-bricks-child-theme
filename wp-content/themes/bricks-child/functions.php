<?php

require_once get_stylesheet_directory() . '/core/ScriptLoader.php';

use BricksChild\Core\ScriptLoader;

/**
 * Registrace scriptů a stylů
 */
$scripts = ScriptLoader::getInstance();

// Základní styly
$scripts->add('index-style', [
    'type' => 'css',
    'path' => '/frontend/assets/css/index.style.css',
    'deps' => ['bricks-frontend']
]);

// Animace
$scripts->add('anime', [
    'path' => '/frontend/assets/js/anime.min.js',
    'deps' => ['jquery'],
    'frontend_only' => true,
    'condition' => fn() => is_front_page() || is_single()
]);

// Backend script (příklad)
$scripts->add('admin-script', [
    'path' => '/backend/assets/js/admin.js',
    'frontend_only' => false,
    'condition' => fn() => is_admin()
]);

// Enqueue assets
add_action('wp_enqueue_scripts', [$scripts, 'enqueue']);
add_action('admin_enqueue_scripts', [$scripts, 'enqueue']);

/**
 * Backend includes configuration
 */
$includes = [
    [
        'path' => '/backend/assets/includes/features/disableAdminNotice.php',
        'priority' => 1
    ],
    [
        'path' => '/backend/assets/includes/features/mediaOptimizer.php',
        'priority' => 5
    ],
    [
        'path' => '/backend/assets/includes/builder/RegisterColors.php',
        'priority' => 10
    ]
];

// Načtení PHP souborů
array_map(
    fn($file) => require_once get_stylesheet_directory() . $file['path'],
    array_filter(
        $includes,
        fn($file) => file_exists(get_stylesheet_directory() . $file['path'])
    )
);

