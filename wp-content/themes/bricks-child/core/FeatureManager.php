<?php
declare(strict_types=1);

namespace BricksChild;

class FeatureManager
{
    private const FEATURES_DIR = '/backend/assets/includes/features';
    private static ?self $instance = null;
    private array $features = [];

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->registerFeatures();
        $this->initializeFeatures();
    }

    private function registerFeatures(): void
    {
        $this->features = [
            'DisableAdminNotice' => [
                'class' => 'BricksChild\Features\DisableAdminNotice',
                'file' => 'disableAdminNotice.php',
                'priority' => 10
            ],
            'MediaOptimizer' => [
                'class' => 'BricksChild\Features\MediaOptimizer',
                'file' => 'mediaOptimizer.php',
                'priority' => 5
            ]
        ];

        // Seřazení podle priority
        uasort($this->features, fn($a, $b) => $a['priority'] <=> $b['priority']);
    }

    private function initializeFeatures(): void
    {
        foreach ($this->features as $feature) {
            $file_path = get_stylesheet_directory() . self::FEATURES_DIR . '/' . $feature['file'];
            
            if (file_exists($file_path)) {
                require_once $file_path;
                
                if (class_exists($feature['class']) && method_exists($feature['class'], 'initialize')) {
                    call_user_func([$feature['class'], 'initialize']);
                }
            }
        }
    }

    private function __clone() {}
    public function __wakeup() {}
} 