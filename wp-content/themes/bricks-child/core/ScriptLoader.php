<?php
declare(strict_types=1);

namespace BricksChild\Core;

class ScriptLoader 
{
    private array $scripts = [];
    private array $styles = [];
    private array $scriptGroups = [];
    private static ?self $instance = null;

    public static function getInstance(): self 
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function addScriptGroup(string $name, array $config): self
    {
        $this->scriptGroups[$name] = $config;
        return $this;
    }

    public function addScript(string $handle, string $path, array $deps = [], bool $in_footer = true, ?callable $condition = null): self 
    {
        $this->scripts[$handle] = [
            'path' => $path,
            'deps' => $deps,
            'in_footer' => $in_footer,
            'condition' => $condition,
            'version' => null,
            'skip_version_check' => false
        ];
        return $this;
    }

    public function addStyle(string $handle, string $path, array $deps = [], ?callable $condition = null): self 
    {
        $this->styles[$handle] = [
            'path' => $path,
            'deps' => $deps,
            'condition' => $condition,
            'version' => null,
            'skip_version_check' => false
        ];
        return $this;
    }

    public function setVersion(string $handle, string $version, bool $skip_version_check = true): self
    {
        if (isset($this->scripts[$handle])) {
            $this->scripts[$handle]['version'] = $version;
            $this->scripts[$handle]['skip_version_check'] = $skip_version_check;
        }
        if (isset($this->styles[$handle])) {
            $this->styles[$handle]['version'] = $version;
            $this->styles[$handle]['skip_version_check'] = $skip_version_check;
        }
        return $this;
    }

    private function getFileVersion(string $path, array $asset): string
    {
        if ($asset['skip_version_check']) {
            return (string) $asset['version'];
        }

        $full_path = get_stylesheet_directory() . $path;
        
        if (!file_exists($full_path)) {
            error_log("BricksChild: Asset file not found: {$full_path}");
            return (string) ($asset['version'] ?? '1.0.0');
        }

        return (string) ($asset['version'] ?? filemtime($full_path));
    }

    public function enqueue(): void 
    {
        if (bricks_is_builder_main()) {
            return;
        }

        // Načtení skupin scriptů
        foreach ($this->scriptGroups as $group => $config) {
            if (isset($config['condition']) && !$config['condition']()) {
                continue;
            }
            
            foreach ($config['scripts'] ?? [] as $handle => $script) {
                $this->addScript(
                    $handle, 
                    $script['path'], 
                    $script['deps'] ?? [], 
                    $script['in_footer'] ?? true,
                    $script['condition'] ?? null
                );
            }
            
            foreach ($config['styles'] ?? [] as $handle => $style) {
                $this->addStyle(
                    $handle, 
                    $style['path'], 
                    $style['deps'] ?? [],
                    $style['condition'] ?? null
                );
            }
        }

        // Základní style.css
        if (file_exists(get_stylesheet_directory() . '/style.css')) {
            wp_enqueue_style(
                'bricks-child',
                get_stylesheet_uri(),
                ['bricks-frontend'],
                (string) filemtime(get_stylesheet_directory() . '/style.css')
            );
        }

        // Enqueue styles
        foreach ($this->styles as $handle => $style) {
            if (isset($style['condition']) && !$style['condition']()) {
                continue;
            }

            wp_enqueue_style(
                $handle,
                get_stylesheet_directory_uri() . $style['path'],
                $style['deps'],
                $this->getFileVersion($style['path'], $style)
            );
        }

        // Enqueue scripts
        foreach ($this->scripts as $handle => $script) {
            if (isset($script['condition']) && !$script['condition']()) {
                continue;
            }

            wp_enqueue_script(
                $handle,
                get_stylesheet_directory_uri() . $script['path'],
                $script['deps'],
                $this->getFileVersion($script['path'], $script),
                $script['in_footer']
            );
        }
    }
} 