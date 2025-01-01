<?php
declare(strict_types=1);

require_once get_stylesheet_directory() . '/core/ScriptLoader.php';

use BricksChild\Core\ScriptLoader;

/**
 * Asset Registration and Loading
 * 
 * This section handles all theme assets (CSS/JS) registration and loading
 * using the ScriptLoader class for efficient asset management.
 */
$scripts = ScriptLoader::getInstance();

// Register frontend styles
$scripts->add('index-style', [
    'type' => 'css',
    'path' => '/frontend/assets/css/index.style.css',
    'deps' => ['bricks-frontend']
]);

// Register animation scripts
$scripts->add('anime', [
    'path' => '/frontend/assets/js/anime.min.js',
    'deps' => ['jquery'],
    'frontend_only' => true,
    'condition' => fn() => is_front_page() || is_single()
]);

// Register admin-specific scripts
$scripts->add('admin-script', [
    'path' => '/backend/assets/js/admin.js',
    'frontend_only' => false,
    'condition' => fn() => is_admin()
]);

// Hook asset loading into WordPress
add_action('wp_enqueue_scripts', [$scripts, 'enqueue']);
add_action('admin_enqueue_scripts', [$scripts, 'enqueue']);

/**
 * Backend Features Configuration
 * 
 * Configuration array for backend features and builder components.
 * Each feature is loaded based on its priority (lower number = higher priority).
 * 
 * @var array[] $includes {
 *     Array of feature configurations
 *     
 *     @type array {
 *         @type string $path     Path to the feature file relative to theme directory
 *         @type int    $priority Loading priority (1-10, lower = higher priority)
 *     }
 * }
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

/**
 * Load Backend Features
 * 
 * Loads all backend feature files that exist in the filesystem.
 * Files are filtered to ensure they exist before attempting to load them.
 */
array_map(
    fn($file) => require_once get_stylesheet_directory() . $file['path'],
    array_filter(
        $includes,
        fn($file) => file_exists(get_stylesheet_directory() . $file['path'])
    )
);

