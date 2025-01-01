<?php
declare(strict_types=1);

namespace BricksChild\Backend;

use BricksChild\Abstract\AbstractAssetManager;

class BackendIncludeManager extends AbstractAssetManager
{
    public function includeFiles(): void
    {
        try {
            $files = $this->getAssetFiles(self::PATHS['backend']['includes'], 'php');
            
            foreach ($files as $file) {
                $file_path = get_stylesheet_directory() . $file;
                
                if (file_exists($file_path)) {
                    require_once $file_path;
                }
            }
        } catch (\Exception $e) {
            error_log("BricksChild AssetsManager Error loading PHP files: {$e->getMessage()}");
        }
    }
} 