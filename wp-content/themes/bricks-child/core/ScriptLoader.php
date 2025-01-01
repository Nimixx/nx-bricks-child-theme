<?php
declare(strict_types=1);

namespace BricksChild\Core;

/**
 * Simple and flexible asset loader for WordPress themes
 * 
 * Handles registration and enqueueing of CSS and JS files with:
 * - Automatic versioning based on file modification time
 * - Conditional loading based on custom rules
 * - Frontend/Backend separation
 * - Dependencies management
 */
class ScriptLoader 
{
    /** @var array<string, array> Storage for registered assets */
    private array $assets = [];

    /** @var self|null Singleton instance */
    private static ?self $instance = null;

    /**
     * Get singleton instance
     */
    public static function getInstance(): self 
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register a new asset (CSS or JS)
     *
     * @param string $handle Unique identifier for the asset
     * @param array $config {
     *     Asset configuration array
     *     
     *     @type string  $type          Asset type ('js' or 'css'), defaults to 'js'
     *     @type string  $path          Path to the file relative to theme directory
     *     @type array   $deps          Array of dependencies handles
     *     @type bool    $in_footer     Whether to enqueue in footer (JS only), defaults to true
     *     @type callable|null $condition Optional callback to determine if asset should be loaded
     *     @type bool    $frontend_only Whether asset should load only on frontend, defaults to true
     * }
     * @return self For method chaining
     */
    public function add(string $handle, array $config): self 
    {
        $this->assets[$handle] = array_merge([
            'type' => 'js',
            'path' => '',
            'deps' => [],
            'in_footer' => true,
            'condition' => null,
            'frontend_only' => true
        ], $config);

        return $this;
    }

    /**
     * Get asset version based on file modification time
     *
     * @param string $path Path to the file relative to theme directory
     * @return string Version string (timestamp or '1.0.0' if file doesn't exist)
     */
    private function getVersion(string $path): string
    {
        $full_path = get_stylesheet_directory() . $path;
        return file_exists($full_path) ? (string) filemtime($full_path) : '1.0.0';
    }

    /**
     * Enqueue all registered assets if their conditions are met
     * 
     * This method:
     * - Loads the main theme stylesheet
     * - Checks conditions for each asset
     * - Handles frontend/backend separation
     * - Enqueues assets with WordPress functions
     */
    public function enqueue(): void 
    {
        if (bricks_is_builder_main()) {
            return;
        }

        // Load main theme stylesheet
        if (!is_admin() && file_exists(get_stylesheet_directory() . '/style.css')) {
            wp_enqueue_style(
                'bricks-child',
                get_stylesheet_uri(),
                ['bricks-frontend'],
                $this->getVersion('/style.css')
            );
        }

        foreach ($this->assets as $handle => $asset) {
            if (($asset['frontend_only'] && is_admin()) || 
                (isset($asset['condition']) && !$asset['condition']())) {
                continue;
            }

            $version = $this->getVersion($asset['path']);

            if ($asset['type'] === 'css') {
                wp_enqueue_style(
                    $handle,
                    get_stylesheet_directory_uri() . $asset['path'],
                    $asset['deps'],
                    $version
                );
            } else {
                wp_enqueue_script(
                    $handle,
                    get_stylesheet_directory_uri() . $asset['path'],
                    $asset['deps'],
                    $version,
                    $asset['in_footer']
                );
            }
        }
    }
} 