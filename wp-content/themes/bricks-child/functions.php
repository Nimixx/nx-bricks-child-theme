<?php

/**
 * Asset Configuration
 */
$assets = [
    'frontend' => [
        'css' => [
            'index-style' => [
                'path' => '/frontend/assets/css/index.style.css',
                'deps' => ['bricks-frontend'],
                'condition' => null
            ]
        ],
        'js' => [
            'anime' => [
                'path' => '/frontend/assets/js/anime.min.js',
                'deps' => ['jquery'],
                'in_footer' => true,
                'condition' => fn() => is_front_page() || is_single()
            ]
        ]
    ],
    'backend' => [
        'includes' => [
            'features' => [
                'disable-admin-notice' => [
                    'path' => '/backend/assets/includes/features/disableAdminNotice.php',
                    'priority' => 1
                ],
                'media-optimizer' => [
                    'path' => '/backend/assets/includes/features/mediaOptimizer.php',
                    'priority' => 5
                ]
            ],
            'builder' => [
                'register-colors' => [
                    'path' => '/backend/assets/includes/builder/RegisterColors.php',
                    'priority' => 10
                ]
            ]
        ]
    ]
];

// Load backend includes - přesunuto před ostatní kód
load_backend_includes();

/**
 * Enqueue frontend assets
 */
function enqueue_frontend_assets(): void 
{
    global $assets;

    if (bricks_is_builder_main()) {
        return;
    }

    // Základní style.css
    wp_enqueue_style(
        'bricks-child',
        get_stylesheet_uri(),
        ['bricks-frontend'],
        filemtime(get_stylesheet_directory() . '/style.css')
    );

    // CSS soubory
    foreach ($assets['frontend']['css'] as $handle => $style) {
        if (isset($style['condition']) && is_callable($style['condition']) && !$style['condition']()) {
            continue;
        }

        wp_enqueue_style(
            "bricks-child-{$handle}",
            get_stylesheet_directory_uri() . $style['path'],
            $style['deps'] ?? [],
            filemtime(get_stylesheet_directory() . $style['path'])
        );
    }

    // JS soubory
    foreach ($assets['frontend']['js'] as $handle => $script) {
        if (isset($script['condition']) && is_callable($script['condition']) && !$script['condition']()) {
            continue;
        }

        wp_enqueue_script(
            "bricks-child-{$handle}",
            get_stylesheet_directory_uri() . $script['path'],
            $script['deps'] ?? [],
            filemtime(get_stylesheet_directory() . $script['path']),
            $script['in_footer'] ?? true
        );
    }
}

/**
 * Load backend includes
 */
function load_backend_includes(): void 
{
    global $assets;

    if (!isset($assets['backend']['includes'])) {
        return;
    }

    // Funkce pro načtení souborů s prioritou
    $load_files = function($files) {
        if (!is_array($files)) return;
        
        // Seřazení podle priority
        uasort($files, function($a, $b) {
            return ($a['priority'] ?? 10) <=> ($b['priority'] ?? 10);
        });

        // Načtení souborů
        foreach ($files as $file) {
            $file_path = get_stylesheet_directory() . $file['path'];
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
    };

    // Načtení features
    if (isset($assets['backend']['includes']['features'])) {
        $load_files($assets['backend']['includes']['features']);
    }

    // Načtení builder souborů
    if (isset($assets['backend']['includes']['builder'])) {
        $load_files($assets['backend']['includes']['builder']);
    }
}

// Hooks
add_action('wp_enqueue_scripts', 'enqueue_frontend_assets');

