<?php
declare(strict_types=1);

namespace BricksChild\Core;

class ScriptLoader 
{
    private array $assets = [];
    private static ?self $instance = null;

    public static function getInstance(): self 
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

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

    private function getVersion(string $path): string
    {
        $full_path = get_stylesheet_directory() . $path;
        return file_exists($full_path) ? (string) filemtime($full_path) : '1.0.0';
    }

    public function enqueue(): void 
    {
        if (bricks_is_builder_main()) {
            return;
        }

        // Základní style.css pro child theme
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